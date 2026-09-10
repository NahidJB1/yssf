<?php
// admin_portal/profile.php
require_once 'auth.php';
require_once 'db_config.php';

$user_id = get_current_user_id();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profiles/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file = $_FILES['profile_picture'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $file_name = "user_{$user_id}_" . time() . ".$ext";
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $profile_picture = $file_path;
            }
        } else {
            $message = "<div class='alert alert-danger'>Invalid image format. Only JPG, PNG, GIF allowed.</div>";
        }
    }
    
    if (empty($message)) {
        if ($profile_picture) {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, profile_picture = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $phone, $profile_picture, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $phone, $user_id]);
        }
        log_activity($pdo, $user_id, "Updated their profile details");
        $message = "<div class='alert alert-success'>Profile updated successfully.</div>";
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

include 'layout_top.php';
?>
<input type="hidden" id="customPageTitle" value="My Profile">

<style>
    .profile-card { background: var(--bg-surface); padding: 32px; border-radius: var(--border-radius-large); border: 1px solid var(--border-default); box-shadow: var(--shadow-card); max-width: 600px; margin: 0 auto; }
    .profile-header { display: flex; align-items: center; gap: 24px; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--border-subtle); }
    
    .avatar-large { 
        width: 100px; height: 100px; border-radius: 50%; background: var(--primary); 
        color: white; display: flex; justify-content: center; align-items: center; 
        font-size: 36px; font-weight: 600; overflow: hidden;
    }
    .avatar-large img { width: 100%; height: 100%; object-fit: cover; }
    
    .form-group { margin-bottom: 20px; }
    .alert { padding: 12px 16px; border-radius: var(--border-radius); margin-bottom: 20px; font-weight: 500; }
    .alert-success { background: var(--success-bg); color: var(--success); }
    .alert-danger { background: var(--danger-bg); color: var(--danger); }
    
    .btn-danger-outline { background: white; color: var(--danger); border: 1px solid var(--danger); }
    .btn-danger-outline:hover { background: var(--danger-bg); }
</style>

<div class="profile-card">
    <?php if($message) echo $message; ?>
    
    <div class="profile-header">
        <div class="avatar-large">
            <?php if(!empty($user['profile_picture'])): ?>
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile">
            <?php else: ?>
                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
            <?php endif; ?>
        </div>
        <div>
            <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 4px;"><?php echo htmlspecialchars($user['username']); ?></h2>
            <div style="color: var(--text-secondary); text-transform: uppercase; font-size: 12px; font-weight: 600; letter-spacing: 0.5px;">
                <?php echo $user['role'] === 'super_admin' ? 'Super Admin' : 'Consultant'; ?>
            </div>
        </div>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" style="width: 100%; padding: 6px 12px; min-height: 32px; border: 1px solid var(--text-tertiary); border-radius: var(--border-radius); font-size: 14px; font-family: inherit;">
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="profile_picture" accept="image/*" style="width: 100%; padding: 6px; border: 1px dashed var(--border-default); border-radius: var(--border-radius);">
            <div style="font-size: 12px; color: var(--text-tertiary); margin-top: 4px;">Leave blank to keep current picture.</div>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border-subtle);">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="logout.php" class="btn btn-danger-outline"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
        </div>
    </form>
</div>

<?php include 'layout_bottom.php'; ?>

