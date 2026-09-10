<?php
// admin_portal/logs.php
require_once 'auth.php';
require_super_admin(); // usually logs are restricted to admins
require_once 'db_config.php';

// Fetch logs
$stmt = $pdo->query("
    SELECT l.action_description, l.created_at, u.username, u.full_name 
    FROM activity_logs l
    JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
    LIMIT 200
");
$logs = $stmt->fetchAll();

// Group logs by date
$grouped_logs = [];
foreach ($logs as $log) {
    $date = date('d/m/Y', strtotime($log['created_at']));
    if (!isset($grouped_logs[$date])) {
        $grouped_logs[$date] = [];
    }
    $grouped_logs[$date][] = $log;
}

include 'layout_top.php';
?>
<input type="hidden" id="customPageTitle" value="System Activity Log">

<style>
    .log-group { margin-bottom: 30px; }
    .log-date { font-weight: 600; font-size: 16px; color: var(--primary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle); }
    .log-item { display: flex; align-items: flex-start; gap: 16px; padding: 12px 0; border-bottom: 1px solid var(--border-subtle); }
    .log-item:last-child { border-bottom: none; }
    .log-time { font-size: 12px; color: var(--text-tertiary); width: 60px; flex-shrink: 0; padding-top: 2px; }
    .log-content { flex: 1; }
    .log-user { font-weight: 600; color: var(--text-primary); }
    .log-action { color: var(--text-secondary); }
    
    .empty-state { padding: 40px; text-align: center; color: var(--text-secondary); }
</style>

<div class="card">
    <?php if (empty($grouped_logs)): ?>
        <div class="empty-state">No activity logs found.</div>
    <?php else: ?>
        <?php foreach ($grouped_logs as $date => $day_logs): ?>
            <div class="log-group">
                <div class="log-date"><?php echo htmlspecialchars($date); ?></div>
                <?php foreach ($day_logs as $log): ?>
                    <?php 
                        $time = date('h:i A', strtotime($log['created_at']));
                        $display_name = !empty($log['full_name']) ? $log['full_name'] : $log['username'];
                    ?>
                    <div class="log-item">
                        <div class="log-time"><?php echo $time; ?></div>
                        <div class="log-content">
                            <span class="log-user"><?php echo htmlspecialchars($display_name); ?></span>
                            <span class="log-action"><?php echo htmlspecialchars($log['action_description']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'layout_bottom.php'; ?>

