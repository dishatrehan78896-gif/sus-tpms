<?php
require_once 'config.php';
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
header("Location: login.php");
exit();
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
    <title>Logging Out - SUS Premium TPMS</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
            margin: 0;
            display: flex;
            flex-direction: column;
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

        .logout-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 20px 60px;
            min-height: 100vh;
        }

        .logout-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .logout-card::before {
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

        .logout-icon {
            font-size: 4rem;
            color: var(--neon-blue);
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .logout-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .logout-message {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .modern-btn {
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            border: none;
            color: white;
            font-weight: 600;
            padding: 14px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
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

        nav a:hover, nav a.active {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.05);
        }

        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid var(--neon-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 2rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .logout-card {
                padding: 2rem;
                margin: 0 1rem;
            }
            
            .logout-title {
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
                        <li><a href="index.php" class="active">Home</a></li>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="tire-health.php">Tire Health</a></li>
                    </ul>
                </nav>
                
                <div class="auth-buttons">
                    <a href="login.php" class="modern-btn">Login</a>
                    <a href="signup.php" class="modern-btn" style="background: linear-gradient(135deg, var(--success), #059669);">Sign Up</a>
                </div>
            </div>
        </div>
    </header>

    <div class="logout-container">
        <div class="logout-card">
            <div class="loading-spinner"></div>
            <div class="logout-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h1 class="logout-title">Logging Out</h1>
            <p class="logout-message">
                You are being securely logged out of your SUS Premium TPMS account. 
                Please wait while we redirect you to the login page.
            </p>
            <p class="logout-message" style="font-size: 0.9rem; color: var(--text-muted);">
                If you are not redirected automatically, 
                <a href="login.php" class="modern-btn" style="padding: 8px 16px; margin-left: 10px;">Click Here</a>
            </p>
        </div>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 3000);

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
    </script>
</body>
</html>
