<?php
// admin_portal/index.php
require_once 'auth.php';
require_once 'db_config.php';

$current_tab = isset($_GET['tab']) ? filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING) : 'requests';
$search_query = isset($_GET['search']) ? filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) : '';

$db_status = ($current_tab === 'followup') ? 'followed_up' : 'pending';

$user_id = get_current_user_id();
$is_super_admin = is_super_admin();

// Base query
$sql = "SELECT c.*, u.username as assigned_to_name 
        FROM consultations c 
        LEFT JOIN users u ON c.assigned_to = u.id 
        WHERE c.status = :status";
$params = [':status' => $db_status];

if (!$is_super_admin) {
    $sql .= " AND (c.assigned_to IS NULL OR c.assigned_to = :user_id)";
    $params[':user_id'] = $user_id;
}

if (!empty($search_query)) {
    $sql .= " AND (c.full_name LIKE :search OR c.email LIKE :search OR c.phone_number LIKE :search)";
    $params[':search'] = '%' . $search_query . '%';
}

$sql .= " ORDER BY c.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $consultations = $stmt->fetchAll();
} catch (\PDOException $e) {
    $consultations = [];
}

$consultants = [];
if ($is_super_admin) {
    $stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'consultant' ORDER BY username");
    $consultants = $stmt->fetchAll();
}

// Include top layout
include 'layout_top.php';
?>
<input type="hidden" id="customPageTitle" value="Dashboard">

<style>
    .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
    
    .search-box { position: relative; width: 300px; }
    .search-box i { position: absolute; left: 12px; top: 9px; color: var(--text-tertiary); }
    .search-box input { padding-left: 36px; border-radius: 20px; }
    
    .pivot-tabs { display: flex; gap: 24px; border-bottom: 1px solid var(--border-subtle); margin-bottom: 20px; }
    .pivot-tab { 
        padding: 0 4px 12px 4px; color: var(--text-secondary); text-decoration: none; 
        font-size: 16px; font-weight: 500; position: relative;
    }
    .pivot-tab:hover { color: var(--text-primary); }
    .pivot-tab.active { color: var(--text-primary); font-weight: 600; }
    .pivot-tab.active::after {
        content: ''; position: absolute; left: 0; right: 0; bottom: -1px; height: 3px; 
        background: var(--primary); border-radius: 3px 3px 0 0;
    }
    
    .action-select { padding: 4px 8px; font-size: 13px; border-radius: 4px; }
</style>

<div class="toolbar">
    <div class="pivot-tabs">
        <a href="index.php?tab=requests" class="pivot-tab <?php echo $current_tab === 'requests' ? 'active' : ''; ?>">Pending Requests</a>
        <a href="index.php?tab=followup" class="pivot-tab <?php echo $current_tab === 'followup' ? 'active' : ''; ?>">Followed Up</a>
    </div>
    
    <form method="GET" class="search-box">
        <input type="hidden" name="tab" value="<?php echo $current_tab; ?>">
        <i class="fas fa-search"></i>
        <input type="text" name="search" placeholder="Search leads..." value="<?php echo htmlspecialchars($search_query); ?>">
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Name & Contact</th>
                <th>Interested Field</th>
                <th>Stage</th>
                <th>Assignment</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($consultations)): ?>
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">No leads found.</td></tr>
            <?php else: ?>
                <?php foreach($consultations as $c): ?>
                    <tr>
                        <td><?php echo date("M j, Y", strtotime($c['created_at'])); ?></td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($c['full_name']); ?></div>
                            <div style="font-size: 12px; color: var(--text-secondary);">
                                <?php echo htmlspecialchars($c['phone_number']); ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($c['interested_to_study']); ?></td>
                        <td>
                            <?php 
                                $status = $c['processing_status'];
                                $badge_class = 'badge-neutral';
                                if ($status === 'Visa Approved' || $status === 'Enrolled') $badge_class = 'badge-success';
                                elseif (strpos($status, 'Rejected') !== false) $badge_class = 'badge-danger';
                                elseif ($status !== 'Pending') $badge_class = 'badge-warning';
                            ?>
                            <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($status); ?></span>
                        </td>
                        <td>
                            <?php if($c['assigned_to']): ?>
                                <span class="badge badge-success" style="margin-bottom: 4px;">Assigned: <?php echo htmlspecialchars($c['assigned_to_name']); ?></span>
                            <?php else: ?>
                                <span class="badge badge-danger" style="margin-bottom: 4px;">Unassigned</span>
                                <?php if(!$is_super_admin): ?>
                                    <button onclick="claimLead(<?php echo $c['id']; ?>)" class="btn btn-primary" style="padding: 2px 8px; font-size: 12px; min-height: 24px;">Claim</button>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if($is_super_admin): ?>
                                <select class="action-select" onchange="assignLead(<?php echo $c['id']; ?>, this.value)">
                                    <option value="">Re-assign...</option>
                                    <option value="NULL">Unassign</option>
                                    <?php foreach($consultants as $cons): ?>
                                        <option value="<?php echo $cons['id']; ?>"><?php echo htmlspecialchars($cons['username']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <a href="student_profile.php?id=<?php echo $c['id']; ?>" class="btn btn-default">Open</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function claimLead(id) {
        if(confirm('Are you sure you want to claim this lead?')) {
            fetch('api_assign.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=claim&id=' + id
            }).then(() => window.location.reload());
        }
    }

    function assignLead(id, userId) {
        if(userId === '') return;
        fetch('api_assign.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=assign&id=' + id + '&user_id=' + userId
        }).then(() => window.location.reload());
    }
</script>

<?php include 'layout_bottom.php'; ?>
