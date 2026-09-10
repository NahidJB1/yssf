<?php
// admin_portal/analytics.php
require_once 'auth.php';
require_super_admin();
require_once 'db_config.php';

// 1. Leads per month (last 6 months)
$stmtLeads = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM consultations GROUP BY month ORDER BY month DESC LIMIT 6");
$leads_data = array_reverse($stmtLeads->fetchAll());

// 2. Conversion Rate overall
$stmtTotal = $pdo->query("SELECT COUNT(*) FROM consultations");
$total_leads = $stmtTotal->fetchColumn();

$stmtConverted = $pdo->query("SELECT COUNT(*) FROM consultations WHERE processing_status IN ('Visa Approved', 'Enrolled')");
$converted_leads = $stmtConverted->fetchColumn();

$not_converted = $total_leads - $converted_leads;

// 3. Counselor Performance
$stmtCounselors = $pdo->query("
    SELECT u.username, COUNT(c.id) as total_assigned, SUM(CASE WHEN c.processing_status IN ('Visa Approved', 'Enrolled') THEN 1 ELSE 0 END) as total_converted
    FROM users u LEFT JOIN consultations c ON u.id = c.assigned_to WHERE u.role = 'consultant' GROUP BY u.id
");
$counselor_data = $stmtCounselors->fetchAll();

// 4. Popular Study Destinations
$stmtDest = $pdo->query("SELECT interested_to_study as destination, COUNT(*) as count FROM consultations GROUP BY destination ORDER BY count DESC LIMIT 5");
$dest_data = $stmtDest->fetchAll();

// Prepare JSON for JS
$months = json_encode(array_column($leads_data, 'month'));
$month_counts = json_encode(array_column($leads_data, 'count'));

$c_names = json_encode(array_column($counselor_data, 'username'));
$c_assigned = json_encode(array_column($counselor_data, 'total_assigned'));
$c_converted = json_encode(array_column($counselor_data, 'total_converted'));

$d_names = json_encode(array_column($dest_data, 'destination'));
$d_counts = json_encode(array_column($dest_data, 'count'));

include 'layout_top.php';
?>
<input type="hidden" id="customPageTitle" value="Analytics Overview">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px; }
    .stat-card { background: var(--bg-surface); padding: 24px; border-radius: var(--border-radius-large); border: 1px solid var(--border-default); box-shadow: var(--shadow-card); }
    .stat-value { font-size: 32px; font-weight: 600; color: var(--primary); margin-bottom: 4px; }
    .stat-label { color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; }
    
    .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .chart-card { background: var(--bg-surface); padding: 24px; border-radius: var(--border-radius-large); border: 1px solid var(--border-default); box-shadow: var(--shadow-card); }
    .chart-title { font-size: 16px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary); }
    
    @media (max-width: 900px) {
        .stats-row, .charts-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-value"><?php echo $total_leads; ?></div>
        <div class="stat-label">Total Leads</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $converted_leads; ?></div>
        <div class="stat-label">Successful Enrollments</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo $total_leads > 0 ? round(($converted_leads / $total_leads) * 100, 1) : 0; ?>%</div>
        <div class="stat-label">Conversion Rate</div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <div class="chart-title">Leads Per Month</div>
        <canvas id="leadsChart"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-title">Overall Conversion</div>
        <div style="width: 70%; margin: 0 auto;">
            <canvas id="conversionChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-title">Counselor Performance</div>
        <canvas id="counselorChart"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-title">Popular Fields of Interest</div>
        <canvas id="destChart"></canvas>
    </div>
</div>

<script>
    Chart.defaults.font.family = "'Segoe UI', 'Inter', sans-serif";
    Chart.defaults.color = '#605E5C';
    
    const primaryColor = '#0078D4';
    const successColor = '#107C10';
    const neutralColor = '#EDEBE9';
    
    new Chart(document.getElementById('leadsChart'), {
        type: 'bar',
        data: {
            labels: <?php echo $months; ?>,
            datasets: [{
                label: 'New Leads',
                data: <?php echo $month_counts; ?>,
                backgroundColor: primaryColor,
                borderRadius: 4
            }]
        }
    });

    new Chart(document.getElementById('conversionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Converted', 'In Progress / Lost'],
            datasets: [{
                data: [<?php echo $converted_leads; ?>, <?php echo $not_converted; ?>],
                backgroundColor: [successColor, neutralColor],
                borderWidth: 0
            }]
        },
        options: { cutout: '70%' }
    });

    new Chart(document.getElementById('counselorChart'), {
        type: 'bar',
        data: {
            labels: <?php echo $c_names; ?>,
            datasets: [
                { label: 'Converted', data: <?php echo $c_converted; ?>, backgroundColor: successColor, borderRadius: 4 },
                { label: 'Total Assigned', data: <?php echo $c_assigned; ?>, backgroundColor: neutralColor, borderRadius: 4 }
            ]
        },
        options: {
            scales: { x: { stacked: true }, y: { stacked: false } }
        }
    });

    new Chart(document.getElementById('destChart'), {
        type: 'bar',
        data: {
            labels: <?php echo $d_names; ?>,
            datasets: [{
                label: 'Leads',
                data: <?php echo $d_counts; ?>,
                backgroundColor: '#8764B8',
                borderRadius: 4
            }]
        },
        options: { indexAxis: 'y' }
    });
</script>

<?php include 'layout_bottom.php'; ?>
