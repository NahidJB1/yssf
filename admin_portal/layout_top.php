<?php
// admin_portal/layout_top.php
require_once 'auth.php';
$current_user_name = get_current_username();
$is_super = is_super_admin();

// Determine active page
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch user details for sidebar
require_once 'db_config.php';
$stmt_user = $pdo->prepare("SELECT profile_picture, full_name FROM users WHERE id = ?");
$stmt_user->execute([get_current_user_id()]);
$current_user_data = $stmt_user->fetch();
$sidebar_display_name = !empty($current_user_data['full_name']) ? $current_user_data['full_name'] : $current_user_name;
$sidebar_avatar = $current_user_data['profile_picture'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YS Admin Portal</title>
    <!-- Microsoft Fluent-like fonts: Segoe UI is system default on Windows, but fallback to Inter for others -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* Fluent Design Colors */
            --primary: #0078D4; /* Microsoft Blue */
            --primary-hover: #106EBE;
            --primary-light: #EFF6FC;
            
            --bg-body: #FAFAFA;
            --bg-surface: #FFFFFF;
            
            --text-primary: #242424;
            --text-secondary: #605E5C;
            --text-tertiary: #A19F9D;
            
            --border-subtle: #EDEBE9;
            --border-default: #E1DFDD;
            
            --success: #107C10;
            --success-bg: #DFF6DD;
            --danger: #D13438;
            --danger-bg: #FDE7E9;
            --warning: #D83B01;
            
            --shadow-flyout: 0 8px 16px rgba(0, 0, 0, 0.14);
            --shadow-card: 0 2px 4px rgba(0, 0, 0, 0.04), 0 0 2px rgba(0, 0, 0, 0.06);
            
            --border-radius: 4px; /* Fluent uses smaller radius */
            --border-radius-large: 8px;
            
            --sidebar-width: 260px;
            --header-height: 48px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', 'Inter', -apple-system, BlinkMacSystemFont, Roboto, sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-primary); 
            display: flex; 
            height: 100vh;
            overflow: hidden;
            font-size: 14px;
        }

        /* --- Sidebar Navigation --- */
        .sidebar { 
            width: var(--sidebar-width); 
            background: var(--bg-surface); 
            border-right: 1px solid var(--border-subtle); 
            display: flex; 
            flex-direction: column; 
            flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            z-index: 1000;
        }
        
        .sidebar-header { 
            height: var(--header-height);
            display: flex; 
            align-items: center; 
            padding: 0 20px; 
            border-bottom: 1px solid var(--border-subtle);
            font-weight: 600;
            font-size: 16px;
            gap: 12px;
        }
        .sidebar-header i { color: var(--primary); font-size: 18px; }
        
        .nav-links { padding: 12px 0; flex: 1; overflow-y: auto; }
        .nav-item { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 10px 20px; 
            color: var(--text-primary); 
            text-decoration: none; 
            position: relative;
            transition: background 0.2s;
        }
        .nav-item i { width: 20px; text-align: center; color: var(--text-secondary); font-size: 16px; }
        .nav-item:hover { background: var(--bg-body); }
        .nav-item.active { background: var(--primary-light); font-weight: 600; }
        .nav-item.active i { color: var(--primary); }
        .nav-item.active::before {
            content: ''; position: absolute; left: 0; top: 8px; bottom: 8px; width: 3px;
            background: var(--primary); border-radius: 0 4px 4px 0;
        }

        .sidebar-footer { 
            padding: 16px 20px; 
            border-top: 1px solid var(--border-subtle); 
            background: var(--bg-surface);
        }
        .user-info { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .user-avatar { 
            width: 32px; height: 32px; border-radius: 50%; background: var(--primary); 
            color: white; display: flex; justify-content: center; align-items: center; font-weight: 600; 
        }
        .user-name { font-weight: 600; font-size: 13px; }
        .user-role { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; }
        .logout-btn { 
            display: flex; align-items: center; gap: 8px; color: var(--text-primary); text-decoration: none; 
            padding: 6px 8px; border-radius: var(--border-radius); transition: background 0.2s;
        }
        .logout-btn:hover { background: var(--danger-bg); color: var(--danger); }
        .logout-btn:hover i { color: var(--danger); }

        /* --- Main Content Area --- */
        .main-wrapper { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            min-width: 0; /* Important for flex text truncation */
            background: var(--bg-body);
        }
        
        .top-header {
            height: var(--header-height);
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
        }
        .menu-toggle {
            display: none;
            background: none; border: none; font-size: 18px; color: var(--text-primary); cursor: pointer;
        }
        .page-title { font-size: 18px; font-weight: 600; flex: 1; }
        
        .content-area { 
            flex: 1; 
            padding: 24px; 
            overflow-y: auto; 
            position: relative;
        }

        /* --- Shared UI Components --- */
        .card { 
            background: var(--bg-surface); 
            border: 1px solid var(--border-default); 
            border-radius: var(--border-radius-large); 
            box-shadow: var(--shadow-card); 
            padding: 20px; 
            margin-bottom: 20px; 
        }
        
        .btn { 
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 6px 16px; min-height: 32px; font-size: 14px; font-weight: 600;
            border-radius: var(--border-radius); cursor: pointer; transition: all 0.2s;
            text-decoration: none; border: 1px solid transparent; font-family: inherit;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-default { background: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default); }
        .btn-default:hover { background: var(--bg-body); }
        
        input[type="text"], input[type="password"], select, textarea {
            width: 100%; padding: 6px 12px; min-height: 32px; border: 1px solid var(--text-tertiary);
            border-radius: var(--border-radius); font-size: 14px; font-family: inherit; transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none; border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary);
        }
        label { display: block; margin-bottom: 4px; font-weight: 600; font-size: 13px; }

        /* --- Table Styling --- */
        .table-container { 
            background: var(--bg-surface); border: 1px solid var(--border-default); 
            border-radius: var(--border-radius-large); overflow-x: auto; 
        }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 16px; border-bottom: 1px solid var(--border-subtle); vertical-align: middle; }
        th { background: #FAFAFA; font-weight: 600; color: var(--text-secondary); font-size: 12px; }
        tr:hover td { background: var(--bg-body); }
        tr:last-child td { border-bottom: none; }

        .badge { 
            display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 12px; 
            font-size: 12px; font-weight: 600; 
        }
        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-warning { background: #FFF4CE; color: var(--warning); }
        .badge-danger { background: var(--danger-bg); color: var(--danger); }
        .badge-neutral { background: #F3F2F1; color: var(--text-secondary); }

        /* --- Mobile Responsiveness --- */
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4); z-index: 999;
        }
        
        @media (max-width: 768px) {
            .sidebar { position: fixed; transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: var(--shadow-flyout); }
            .menu-toggle { display: block; }
            .sidebar-overlay.active { display: block; }
            .content-area { padding: 16px; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-layer-group"></i>
            YS Admin
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-item <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-list-ul"></i> Dashboard
            </a>
            <?php if($is_super): ?>
            <a href="analytics.php" class="nav-item <?php echo ($current_page === 'analytics.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Analytics
            </a>
            <a href="users.php" class="nav-item <?php echo ($current_page === 'users.php') ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i> Users
            </a>
            <a href="logs.php" class="nav-item <?php echo ($current_page === 'logs.php') ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Activity Logs
            </a>
            <?php endif; ?>
        </div>
        <div class="sidebar-footer">
            <a href="profile.php" style="text-decoration: none; color: inherit;">
                <div class="user-info" style="cursor: pointer;">
                    <div class="user-avatar" style="overflow: hidden;">
                        <?php if($sidebar_avatar): ?>
                            <img src="<?php echo htmlspecialchars($sidebar_avatar); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($sidebar_display_name, 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="user-name"><?php echo htmlspecialchars($sidebar_display_name); ?></div>
                        <div class="user-role"><?php echo $is_super ? 'Super Admin' : 'Consultant'; ?></div>
                    </div>
                </div>
            </a>
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="top-header">
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div class="page-title" id="topPageTitle">Dashboard</div>
        </header>
        
        <main class="content-area">

