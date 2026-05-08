<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT ID, Username, Password FROM user_data WHERE Email_ID = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // In a real application, you should use password_verify()
            // For demo purposes, we're using a simple comparison
            if ($password === $user['Password']) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['username'] = $user['Username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "Invalid email or password!";
        }
        
        $stmt->close();
    } else {
        $error = "Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0a0a0a">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="SUS TPMS">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SUS Premium TPMS</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Sleek Black Transparent Modern Theme */
        :root {
            --black-primary: #0a0a0a;
            --black-secondary: #1a1a1a;
            --black-tertiary: #2a2a2a;
            --accent-glow: rgba(255, 255, 255, 0.1);
            --accent-primary: #ffffff;
            --accent-secondary: #e0e0e0;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --text-muted: #808080;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --neon-blue: #3b82f6;
        }

        body {
            background: 
                radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
                linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 50%, var(--black-primary) 100%);
            background-attachment: fixed;
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(45deg, transparent 49%, var(--accent-glow) 50%, transparent 51%),
                linear-gradient(-45deg, transparent 49%, var(--accent-glow) 50%, transparent 51%);
            background-size: 60px 60px;
            animation: gridMove 40s linear infinite;
            pointer-events: none;
            z-index: -1;
            opacity: 0.3;
        }

        @keyframes gridMove {
            0% { background-position: 0 0; }
            100% { background-position: 60px 60px; }
        }

        .modern-header {
            background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .modern-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(59, 130, 246, 0.2), 
                transparent);
            animation: headerShine 6s infinite;
        }

        @keyframes headerShine {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }

        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 20px 60px;
            min-height: 100vh;
        }

        .modern-form {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .modern-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, 
                transparent, 
                var(--neon-blue), 
                transparent);
        }

        .modern-form:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .form-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-align: center;
            margin-bottom: 2.5rem;
            letter-spacing: -0.5px;
        }

        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.8rem;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            backdrop-filter: blur(10px);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--neon-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background: rgba(255, 255, 255, 0.12);
        }

        .modern-btn {
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            border: none;
            color: white;
            font-weight: 600;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            width: 100%;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .modern-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.2), 
                transparent);
            transition: left 0.5s ease;
        }

        .modern-btn:hover::before {
            left: 100%;
        }

        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .form-footer {
            text-align: center;
            margin-top: 2.5rem;
            color: var(--text-secondary);
        }

        .form-link {
            color: var(--neon-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .form-link:hover {
            color: #60a5fa;
            text-decoration: underline;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 1.8rem;
            border-left: 4px solid;
            backdrop-filter: blur(10px);
            font-weight: 500;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: var(--danger);
            color: var(--danger);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.3);
        }

        .logo-symbol {
            position: relative;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-main {
            font-family: 'Inter', sans-serif;
            font-size: 18px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1;
        }

        .logo-subtitle {
            font-family: 'Inter', sans-serif;
            font-size: 8px;
            color: var(--neon-blue);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }

        nav a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 8px 16px;
            border-radius: 6px;
        }

        nav a:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.05);
        }

        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .modern-form {
                padding: 2rem;
                margin: 0 1rem;
            }
            
            .form-title {
                font-size: 1.8rem;
            }
            
            .header-content {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            nav ul {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="modern-header">
        <div class="container">
            <div class="header-content">
                <!-- Modern Logo -->
                <a href="index.php" class="logo-container">
                    <div class="logo-symbol">
                        <div style="width: 24px; height: 24px; background: var(--black-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <div style="width: 12px; height: 12px; background: linear-gradient(135deg, var(--neon-blue), #1d4ed8); border-radius: 50%;"></div>
                        </div>
                    </div>
                    <div class="logo-text">
                        <div class="logo-main">SUS</div>
                        <div class="logo-subtitle">PREMIUM TPMS</div>
                    </div>
                </a>

                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="dashboard.php">Dashboard</a></li>
                    </ul>
                </nav>
                
                <div class="auth-buttons">
                    <a href="signup.php" class="modern-btn" style="padding: 10px 20px; display: inline-block; width: auto; background: linear-gradient(135deg, var(--success), #059669);">
                        <i class="fas fa-user-plus" style="margin-right: 8px;"></i>Sign Up
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="auth-container">
        <div class="modern-form">
            <h2 class="form-title">
                <i class="fas fa-sign-in-alt" style="margin-right: 12px;"></i>Welcome Back
            </h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope" style="margin-right: 10px;"></i>Email Address
                    </label>
                    <input type="email" id="email" name="email" class="form-input" required placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock" style="margin-right: 10px;"></i>Password
                    </label>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="modern-btn">
                    <i class="fas fa-sign-in-alt" style="margin-right: 10px;"></i>Sign In
                </button>
            </form>
            
            <div class="form-footer">
                <p>Don't have an account? <a href="signup.php" class="form-link">Create one here</a></p>
            </div>
        </div>
    </div>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.modern-header');
            if (window.scrollY > 50) {
                header.style.background = 'rgba(10, 10, 10, 0.95)';
                header.style.backdropFilter = 'blur(30px) saturate(200%)';
            } else {
                header.style.background = 'rgba(10, 10, 10, 0.8)';
                header.style.backdropFilter = 'blur(20px) saturate(180%)';
            }
        });

        // Add interactive form effects
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.modern-form');
            const inputs = document.querySelectorAll('.form-input');
            
            // Form 3D effect on mousemove
            form.addEventListener('mousemove', (e) => {
                const rect = form.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const angleX = (y - centerY) / 25;
                const angleY = (centerX - x) / 25;
                
                form.style.transform = `perspective(1000px) rotateX(${angleX}deg) rotateY(${angleY}deg) translateY(-5px)`;
            });
            
            form.addEventListener('mouseleave', () => {
                form.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
            });

            // Input focus effects
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>