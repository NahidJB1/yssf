<?php
session_start();
require_once 'db_config.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true; 
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - YS Admin Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0078D4;
            --primary-hover: #106EBE;
            --bg-body: #FAFAFA;
            --bg-surface: #FFFFFF;
            --text-primary: #242424;
            --text-secondary: #605E5C;
            --border-default: #E1DFDD;
            --danger: #D13438;
            --shadow-card: 0 4px 8px rgba(0, 0, 0, 0.04), 0 0 4px rgba(0, 0, 0, 0.06);
            --border-radius: 4px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', 'Inter', sans-serif;
            background-color: var(--bg-body);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: var(--text-primary);
        }

        .login-container {
            background: var(--bg-surface);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-card);
            width: 100%;
            max-width: 440px;
            border: 1px solid var(--border-default);
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
            color: var(--primary);
            font-size: 32px;
        }

        h2 {
            font-weight: 600;
            font-size: 24px;
            margin-bottom: 8px;
        }

        p.subtitle {
            color: var(--text-secondary);
            margin-bottom: 24px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-default);
            border-radius: var(--border-radius);
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 1px var(--primary);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 36px;
            cursor: pointer;
            color: var(--text-secondary);
        }
        
        .toggle-password:hover {
            color: var(--primary);
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .alert-error {
            color: var(--danger);
            background: #FDE7E9;
            padding: 10px 12px;
            border-radius: var(--border-radius);
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #F9D0C4;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-layer-group"></i>
        </div>
        <h2>Sign In</h2>
        <p class="subtitle">to continue to YS Admin Portal</p>
        
        <?php if ($error): ?>
            <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
                <i class="far fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <button type="submit" class="btn-primary">Sign In</button>
        </form>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
