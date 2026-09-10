<?php
// admin_portal/users.php
require_once 'auth.php';
require_super_admin();
require_once 'db_config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'consultant';
        
        if (!empty($username) && !empty($password)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $message = "<div class='alert alert-danger'>Username already exists.</div>";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
                if ($insert->execute([$username, $hash, $role])) {
                    log_activity($pdo, get_current_user_id(), "Created new user: $username");
                    $message = "<div class='alert alert-success'>User created successfully.</div>";
                }
            }
        }
    } elseif ($action === 'reset_password') {
        $user_id = $_POST['user_id'] ?? 0;
        $new_password = $_POST['new_password'] ?? '';
        
        if (!empty($new_password)) {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            if ($update->execute([$hash, $user_id])) {
                log_activity($pdo, get_current_user_id(), "Reset password for user ID: $user_id");
                $message = "<div class='alert alert-success'>Password reset successfully.</div>";
            }
        }
    } elseif ($action === 'delete') {
        $user_id = $_POST['user_id'] ?? 0;
        if ($user_id != get_current_user_id()) {
            $delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($delete->execute([$user_id])) {
                log_activity($pdo, get_current_user_id(), "Deleted user ID: $user_id");
                $message = "<div class='alert alert-success'>User deleted.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>You cannot delete your own account.</div>";
        }
    }
}

$stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY role, username");
$users = $stmt->fetchAll();

include 'layout_top.php';
?>
<input type="hidden" id="customPageTitle" value="Manage Users">

<style>
    .layout-grid { display: grid; grid-template-columns: 350px 1fr; gap: 24px; }
    
    .alert { padding: 12px 16px; border-radius: var(--border-radius); margin-bottom: 20px; font-weight: 500; }
    .alert-success { background: var(--success-bg); color: var(--success); }
    .alert-danger { background: var(--danger-bg); color: var(--danger); }
    
    .card-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-subtle); }
    
    @media (max-width: 900px) {
        .layout-grid { grid-template-columns: 1fr; }
    }
</style>

<?php if($message) echo $message; ?>

<div class="layout-grid">
    <div>
        <div class="card">
            <h2 class="card-title">Create New User</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div style="margin-bottom: 16px;">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                
                <div style="margin-bottom: 24px;">
                    <label>Role</label>
                    <select name="role">
                        <option value="consultant">Consultant</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create User</button>
            </form>
        </div>
    </div>
    
    <div>
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid var(--border-subtle);">
                <h2 class="card-title" style="border: none; margin: 0; padding: 0;">Existing Users</h2>
            </div>
            
            <div class="table-container" style="border: none; border-radius: 0; box-shadow: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td style="font-weight: 500;"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td>
                                <?php if($user['role'] === 'super_admin'): ?>
                                    <span class="badge badge-warning">Super Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-neutral">Consultant</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--text-secondary);"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td style="text-align: right;">
                                <?php if($user['id'] != get_current_user_id()): ?>
                                    <form method="POST" style="display:inline-flex; align-items:center; gap: 8px;" onsubmit="return confirm('Reset password for this user?');">
                                        <input type="hidden" name="action" value="reset_password">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="password" name="new_password" placeholder="New pass" required style="width: 100px; padding: 4px; min-height: 28px;">
                                        <button type="submit" class="btn btn-default" style="padding: 4px 8px; min-height: 28px;">Reset</button>
                                    </form>
                                    
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-default" style="padding: 4px 8px; min-height: 28px; color: var(--danger);"><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: var(--text-tertiary); font-style: italic;">Current User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
