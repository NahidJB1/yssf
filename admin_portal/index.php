<?php
session_start();

// Security Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'db_config.php';

$current_tab = isset($_GET['tab']) ? filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING) : 'requests';
$db_status = ($current_tab === 'followup') ? 'followed_up' : 'pending';

try {
    $stmt = $pdo->prepare("SELECT * FROM consultations WHERE status = :status ORDER BY created_at DESC");
    $stmt->execute(['status' => $db_status]);
    $consultations = $stmt->fetchAll();
} catch (\PDOException $e) {
    $consultations = [];
}

// Helper function to format WhatsApp number robustly
function formatWhatsAppNumber($phone) {
    $number = preg_replace('/[^0-9]/', '', $phone);
    if (strpos($number, '880') === 0) {
        return '+' . $number;
    }
    if (strpos($number, '0') === 0) {
        return '+88' . $number; 
    }
    return '+880' . $number;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - YS Study Focus</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4361ee;
            --primary-hover: #3a0ca3;
            --secondary: #4cc9f0;
            --dark: #2b2d42;
            --light: #f8f9fa;
            --gray: #8d99ae;
            --success: #2ec4b6;
            --danger: #e63946;
            --card-bg: #ffffff;
            --border: #e9ecef;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body { background-color: #f0f2f5; color: var(--dark); display: flex; min-height: 100vh; }

        /* Sidebar & Overlay */
        .sidebar {
            width: 260px; background-color: #ffffff; border-right: 1px solid var(--border);
            display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 1000;
            transition: transform 0.3s;
        }
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); z-index: 999; opacity: 0; transition: opacity 0.3s;
        }

        .sidebar-header {
            padding: 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;
        }
        .sidebar-header-left { display: flex; align-items: center; gap: 12px; }
        .logo-icon {
            background: var(--primary); color: white; width: 36px; height: 36px;
            border-radius: 8px; display: flex; justify-content: center; align-items: center; font-size: 18px;
        }
        .sidebar-header h2 { font-size: 18px; font-weight: 700; color: var(--dark); }
        .close-sidebar { display: none; font-size: 24px; color: var(--gray); cursor: pointer; }

        .nav-links { padding: 20px 0; flex: 1; }
        .nav-item {
            padding: 12px 24px; display: flex; align-items: center; gap: 12px; color: var(--gray);
            text-decoration: none; font-weight: 500; transition: all 0.2s; border-left: 3px solid transparent;
        }
        .nav-item.active, .nav-item:hover { color: var(--primary); background-color: rgba(67, 97, 238, 0.05); border-left-color: var(--primary); }
        .sidebar-footer { padding: 20px 24px; border-top: 1px solid var(--border); }
        .logout-btn { display: flex; align-items: center; gap: 8px; color: var(--danger); text-decoration: none; font-weight: 500; }

        /* Main Content */
        .main-content { flex: 1; margin-left: 260px; padding: 40px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-header h1 { font-size: 24px; font-weight: 600; }
        
        .tabs { display: flex; gap: 15px; margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 10px; }
        .tab-btn {
            text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; font-size: 15px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            color: var(--gray); background: transparent;
        }
        .tab-btn.active { background: var(--primary); color: white; box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2); }
        .tab-btn:not(.active):hover { background: #e9ecef; color: var(--dark); }

        .stats-badge {
            background: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 500;
            color: var(--gray); border: 1px solid var(--border); box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        /* Cards Layout */
        .cards-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
        .student-card {
            background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: transform 0.2s, box-shadow 0.2s, opacity 0.3s;
            overflow: hidden; display: flex; flex-direction: column;
        }
        .student-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }

        .card-top { padding: 20px; border-bottom: 1px solid #f1f3f5; display: flex; justify-content: space-between; align-items: flex-start; }
        .student-info h3 { font-size: 18px; font-weight: 600; margin-bottom: 4px; color: var(--dark); }
        .student-info p { font-size: 13px; color: var(--gray); }
        .passport-badge { font-size: 11px; padding: 4px 8px; border-radius: 12px; font-weight: 600; text-transform: uppercase; }
        .passport-yes { background: #e3f2fd; color: #1976d2; }
        .passport-no { background: #ffebee; color: #d32f2f; }

        .card-body { padding: 20px; flex: 1; }
        .data-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
        .data-label { color: var(--gray); font-weight: 500; }
        .data-value { font-weight: 600; color: var(--dark); text-align: right; max-width: 60%; word-break: break-word; }

        .card-actions { padding: 15px 20px; background: #fafbfc; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 10px; }
        .btn-row { display: flex; gap: 10px; }
        .btn-whatsapp {
            flex: 1; display: inline-flex; justify-content: center; align-items: center; gap: 8px;
            background: #25D366; color: white; padding: 10px; border-radius: 8px;
            text-decoration: none; font-weight: 600; font-size: 14px; transition: background 0.2s;
        }
        .btn-whatsapp:hover { background: #1da851; }
        .btn-email {
            width: 42px; display: inline-flex; justify-content: center; align-items: center;
            background: white; color: var(--gray); border: 1px solid var(--border); border-radius: 8px;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-email:hover { border-color: var(--primary); color: var(--primary); }
        
        .status-toggle {
            display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 8px 12px;
            background: white; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-weight: 500;
        }
        .status-toggle input { width: 18px; height: 18px; cursor: pointer; }

        .empty-state { grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px dashed var(--gray); }
        .empty-state i { font-size: 48px; color: #dee2e6; margin-bottom: 15px; }
        .empty-state h3 { font-size: 18px; color: var(--dark); margin-bottom: 8px; }
        .empty-state p { color: var(--gray); font-size: 14px; }

        /* Mobile Adjustments */
        .menu-toggle { display: none; }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: 4px 0 15px rgba(0,0,0,0.1); }
            .sidebar-overlay.active { display: block; opacity: 1; }
            .close-sidebar { display: block; }
            .main-content { margin-left: 0; padding: 20px; }
            .menu-toggle { display: block; background: none; border: none; font-size: 24px; color: var(--dark); cursor: pointer; margin-right: 15px; }
            .page-header { justify-content: flex-start; }
            .page-header h1 { flex: 1; }
            .tabs { flex-wrap: wrap; }
            .tab-btn { flex: 1; justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-header-left">
                <div class="logo-icon"><i class="fas fa-user-graduate"></i></div>
                <h2>YS Admin</h2>
            </div>
            <i class="fas fa-times close-sidebar" id="closeSidebar"></i>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-item active"><i class="fas fa-inbox"></i> Consultation</a>
            <a href="https://ysstudyfocus.com/index.html" target="_blank" rel="noopener noreferrer" class="nav-item">
                <i class="fas fa-globe"></i> Visit Website
            </a>
        </div>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <button class="menu-toggle" id="openSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Consultation</h1>
            <div class="stats-badge">
                <?php echo count($consultations); ?> Total
            </div>
        </div>

        <div class="tabs">
            <a href="index.php?tab=requests" class="tab-btn <?php echo $current_tab === 'requests' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list"></i> Consultation Requests
            </a>
            <a href="index.php?tab=followup" class="tab-btn <?php echo $current_tab === 'followup' ? 'active' : ''; ?>">
                <i class="fas fa-check-double"></i> Follow Up
            </a>
        </div>

        <div class="cards-container">
            <?php if (empty($consultations)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>No records found</h3>
                    <p>There are no applications in this list right now.</p>
                </div>
            <?php else: ?>
                <?php foreach ($consultations as $consult): ?>
                    <?php 
                        $date = date("M j, Y • g:i A", strtotime($consult['created_at']));
                        $whatsapp_num = formatWhatsAppNumber($consult['phone_number']);
                        $wa_link = "https://wa.me/" . ltrim($whatsapp_num, '+');
                        $has_passport = (stripos($consult['has_passport'], 'yes') !== false);
                    ?>
                    <div class="student-card" id="card-<?php echo $consult['id']; ?>">
                        <div class="card-top">
                            <div class="student-info">
                                <h3><?php echo htmlspecialchars($consult['full_name']); ?></h3>
                                <p><i class="far fa-clock"></i> <?php echo $date; ?></p>
                            </div>
                            <span class="passport-badge <?php echo $has_passport ? 'passport-yes' : 'passport-no'; ?>">
                                <i class="fas <?php echo $has_passport ? 'fa-passport' : 'fa-times'; ?>"></i> Passport
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <div class="data-row">
                                <span class="data-label">Qualification</span>
                                <span class="data-value"><?php echo htmlspecialchars($consult['highest_education']); ?></span>
                            </div>
                            <div class="data-row">
                                <span class="data-label">Result/Score</span>
                                <span class="data-value"><?php echo htmlspecialchars($consult['result_score']); ?></span>
                            </div>
                            <div class="data-row">
                                <span class="data-label">Passing Year</span>
                                <span class="data-value"><?php echo htmlspecialchars($consult['passing_year']); ?></span>
                            </div>
                            <div class="data-row">
                                <span class="data-label">Interested Field</span>
                                <span class="data-value" style="color: var(--primary);"><?php echo htmlspecialchars($consult['interested_to_study']); ?></span>
                            </div>
                            <div class="data-row">
                                <span class="data-label">Budget</span>
                                <span class="data-value" style="color: var(--success);"><?php echo htmlspecialchars($consult['budget']); ?></span>
                            </div>
                        </div>

                        <div class="card-actions">
                            <label class="status-toggle">
                                <input type="checkbox" onchange="updateStatus(<?php echo $consult['id']; ?>, this.checked)" <?php echo $db_status === 'followed_up' ? 'checked' : ''; ?>>
                                <span>Done Consultation</span>
                            </label>
                            
                            <div class="btn-row">
                                <a href="<?php echo $wa_link; ?>" target="_blank" class="btn-whatsapp">
                                    <i class="fab fa-whatsapp"></i> Chat (<?php echo htmlspecialchars($consult['phone_number']); ?>)
                                </a>
                                <a href="mailto:<?php echo htmlspecialchars($consult['email']); ?>" class="btn-email" title="Send Email">
                                    <i class="far fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Sidebar logic
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const openBtn = document.getElementById('openSidebar');
        const closeBtn = document.getElementById('closeSidebar');

        function toggleMenu() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        openBtn.addEventListener('click', toggleMenu);
        closeBtn.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);

        // Status update logic
        function updateStatus(id, isChecked) {
            const newStatus = isChecked ? 'followed_up' : 'pending';
            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', newStatus);

            fetch('update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    // Visually hide the card since it belongs to the other tab now
                    const card = document.getElementById('card-' + id);
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                } else {
                    alert('Error updating status.');
                    // Revert checkbox
                    event.target.checked = !isChecked;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error.');
                event.target.checked = !isChecked;
            });
        }
    </script>
</body>
</html>
