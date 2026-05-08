<?php
require_once 'config.php';
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
    <title>SUS - Suboptimal Underinflation State | Premium Tire Pressure Monitoring</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            line-height: 1.6;
            font-weight: 400;
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

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Modern Header */
        .steel-header {
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

        .steel-header::before {
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

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        /* Modern Logo */
        .logo-container {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            padding: 10px 20px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .logo-container:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--neon-blue);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .logo-symbol {
            position: relative;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .outer-ring {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: transparent;
            border-radius: 50%;
            animation: rotate 6s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .logo-main {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1;
            letter-spacing: 0.5px;
        }

        .logo-subtitle {
            font-size: 8px;
            color: var(--neon-blue);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2px;
            font-weight: 500;
        }

        /* Navigation */
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
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            position: relative;
            font-size: 0.95rem;
        }

        nav a:hover {
            color: var(--accent-primary);
            background: rgba(255, 255, 255, 0.05);
        }

        nav a.active {
            color: var(--accent-primary);
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* Auth Buttons */
        .auth-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        /* Modern Buttons */
        .steel-btn {
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 0.95rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .steel-btn::before {
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

        .steel-btn:hover::before {
            left: 100%;
        }

        .steel-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        /* Hero Section */
        .hero-section {
            padding: 160px 0 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            background: 
                radial-gradient(ellipse at center, rgba(59, 130, 246, 0.1) 0%, transparent 70%),
                linear-gradient(135deg, rgba(10, 10, 10, 0.9) 0%, rgba(26, 26, 26, 0.8) 100%);
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            letter-spacing: -0.5px;
        }

        .hero-subtitle {
            font-size: 1.4rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto 3rem;
            font-weight: 400;
            line-height: 1.6;
        }

        /* Modern Cards */
        .steel-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .steel-card::before {
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

        .steel-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .feature-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1.5rem;
        }

        .light-text {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .steel-text {
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* Section Titles */
        .section-title {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-align: center;
            margin-bottom: 4rem;
            letter-spacing: -1px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            margin-bottom: 5rem;
        }

        /* Statistics */
        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 1.1rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        /* Footer */
        .footer-section {
            background: rgba(10, 10, 10, 0.9);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 4rem 0;
            margin-top: 6rem;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.2); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .feature-grid {
                grid-template-columns: 1fr;
            }
            
            .auth-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="steel-header">
        <div class="container">
            <div class="header-content">
                <!-- Modern Logo -->
                <a href="index.php" class="logo-container">
                    <div class="logo-symbol">
                        <div class="outer-ring"></div>
                        <div class="inner-design" style="width: 25px; height: 25px; background: rgba(10, 10, 10, 0.8); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <div class="pressure-indicator" style="width: 14px; height: 14px; background: linear-gradient(135deg, var(--neon-blue), #1d4ed8); border-radius: 50%; position: relative; overflow: hidden;">
                                <div class="pressure-fill" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 70%; background: rgba(10, 10, 10, 0.8); border-radius: 0 0 7px 7px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="logo-text">
                        <div class="logo-main">SUS</div>
                        <div class="logo-subtitle">TIRE PRESSURE MONITORING</div>
                    </div>
                </a>

                <nav>
                    <ul>
                        <li><a href="index.php" class="active">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
                
                <div class="auth-buttons">
                    <?php if (isLoggedIn()): ?>
                        <span style="color: var(--text-secondary); font-weight: 500; margin-right: 15px;">
                            <span class="pulse-dot"></span>Welcome, <?php echo $_SESSION['username']; ?>
                        </span>
                        <a href="dashboard.php" class="steel-btn">Dashboard</a>
                        <a href="logout.php" class="steel-btn" style="background: linear-gradient(135deg, var(--danger), #dc2626);">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="steel-btn">Login</a>
                        <a href="signup.php" class="steel-btn" style="background: linear-gradient(135deg, var(--success), #059669);">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <section class="hero-section">
        <div class="container">
		<!-- === ADD VOICE INTRODUCTION HERE === -->
<div class="voice-intro-container" style="position: fixed; top: 100px; right: 30px; z-index: 1000;">
    <div class="voice-controls" style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
        <button id="playIntroBtn" class="voice-icon-btn" style="
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
        ">
            <i class="fas fa-headphones" id="playIcon" style="font-size: 1.2rem;"></i>
        </button>
        
        <button id="stopIntroBtn" class="voice-icon-btn" style="
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        ">
            <i class="fas fa-stop" style="font-size: 0.9rem;"></i>
        </button>
    </div>
    
    <div id="voiceStatus" style="
        margin-top: 10px;
        padding: 8px 12px;
        background: rgba(10, 10, 10, 0.95);
        border-radius: 8px;
        border: 1px solid rgba(59, 130, 246, 0.3);
        display: none;
        color: var(--text-primary);
        font-size: 0.8rem;
        text-align: center;
        backdrop-filter: blur(10px);
        max-width: 200px;
        word-wrap: break-word;
    "></div>
</div>
        <!-- === END VOICE INTRODUCTION === -->
            <h1 class="hero-title">
                Intelligent Tire Pressure<br>Monitoring System
            </h1>
            <p class="hero-subtitle">
                Advanced IoT technology for real-time tire pressure and temperature monitoring. 
                Enhance vehicle safety, improve fuel efficiency, and extend tire lifespan with our premium solution.
            </p>
            <?php if (!isLoggedIn()): ?>
                <a href="signup.php" class="steel-btn" style="padding: 16px 40px; font-size: 1.1rem;">
                    <i class="fas fa-rocket" style="margin-right: 10px;"></i>Start Your Journey
                </a>
            <?php else: ?>
                <a href="dashboard.php" class="steel-btn" style="padding: 16px 40px; font-size: 1.1rem;">
                    <i class="fas fa-gauge-high" style="margin-right: 10px;"></i>Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
    </section>

    <section id="features" class="container" style="padding: 100px 0;">
        <h2 class="section-title">Why Choose SUS TPMS?</h2>
        
        <div class="feature-grid">
            <div class="steel-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="light-text">Real-time Monitoring</h3>
                <p class="steel-text">
                    Instant access to tire pressure and temperature data with our advanced IoT sensors. 
                    Monitor your vehicle's health from anywhere, anytime.
                </p>
            </div>
            
            <div class="steel-card">
                <div class="feature-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <h3 class="light-text">Smart Alerts</h3>
                <p class="steel-text">
                    Receive immediate notifications when tire parameters exceed safe thresholds. 
                    Proactive alerts prevent potential issues before they become critical.
                </p>
            </div>
            
            <div class="steel-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="light-text">Data Analytics</h3>
                <p class="steel-text">
                    Comprehensive historical data and trend analysis to optimize your vehicle's performance 
                    and maintenance schedule.
                </p>
            </div>
            
            <div class="steel-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="light-text">Enhanced Safety</h3>
                <p class="steel-text">
                    Significantly reduce the risk of tire-related accidents with continuous monitoring 
                    and early warning systems.
                </p>
            </div>
        </div>

        <!-- Statistics Section -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 3rem; margin-top: 5rem;">
            <div style="text-align: center;">
                <div class="stat-number">99.9%</div>
                <div class="stat-label">System Uptime</div>
            </div>
            <div style="text-align: center;">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Monitoring</div>
            </div>
            <div style="text-align: center;">
                <div class="stat-number">50ms</div>
                <div class="stat-label">Response Time</div>
            </div>
            <div style="text-align: center;">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Active Users</div>
            </div>
        </div>
    </section>

    <section id="about" style="background: rgba(10, 10, 10, 0.7); backdrop-filter: blur(20px); padding: 100px 0; border-top: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="container">
            <h2 class="section-title">About SUS Technology</h2>
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <p class="steel-text" style="font-size: 1.3rem; margin-bottom: 2rem; line-height: 1.8;">
                    SUS (Smart Universal System) represents the pinnacle of IoT-based Tire Pressure Monitoring technology. 
                    Our system combines cutting-edge sensors with sophisticated analytics to provide unparalleled 
                    insights into your vehicle's tire health.
                </p>
                <p class="steel-text" style="font-size: 1.3rem; line-height: 1.8;">
                    Designed for both individual vehicle owners and fleet managers, SUS delivers enterprise-grade 
                    monitoring capabilities with an intuitive, user-friendly interface that puts critical information 
                    at your fingertips.
                </p>
            </div>
        </div>
    </section>

    <footer id="contact" class="footer-section">
        <div class="container">
            <div style="text-align: center;">
                <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 1rem;">
                    © 2024 SUS - Smart Universal System. All rights reserved.
                </p>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                    <i class="fas fa-envelope" style="margin-right: 8px; color: var(--neon-blue);"></i>info@sus-tpms.com | 
                    <i class="fas fa-phone" style="margin: 0 8px; color: var(--neon-blue);"></i>Support: support@sus-tpms.com
                </p>
                <div style="display: flex; justify-content: center; gap: 20px;">
                    <a href="#" style="color: var(--text-secondary); text-decoration: none; transition: color 0.3s ease; font-size: 1.2rem;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color: var(--text-secondary); text-decoration: none; transition: color 0.3s ease; font-size: 1.2rem;"><i class="fab fa-linkedin"></i></a>
                    <a href="#" style="color: var(--text-secondary); text-decoration: none; transition: color 0.3s ease; font-size: 1.2rem;"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </footer>

        <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add intersection observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all steel cards for animation
        document.querySelectorAll('.steel-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.steel-header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(10, 10, 10, 0.95)';
                header.style.backdropFilter = 'blur(30px) saturate(200%)';
            } else {
                header.style.background = 'rgba(10, 10, 10, 0.8)';
                header.style.backdropFilter = 'blur(20px) saturate(180%)';
            }
        });
    </script>
	<script>
class VoiceIntroduction {
    constructor() {
        this.synth = window.speechSynthesis;
        this.isSpeaking = false;
        this.currentUtterance = null;
        this.introMessages = [
            "Welcome to SUS Premium Tire Pressure Monitoring System.",
            "Our advanced TPMS technology helps you monitor your vehicle's tire health in real-time.",
            "The system continuously tracks tire pressure and temperature across all four wheels.",
            "You'll receive instant alerts for low pressure, high pressure, or abnormal temperature conditions.",
            "Maintaining proper tire pressure improves fuel efficiency, extends tire life, and enhances safety.",
            "On your dashboard, you can view real-time pressure readings, temperature data, and overall tire health status.",
            "The system provides detailed analytics and historical data to help you understand your tire performance over time.",
            "You can set custom alerts for your specific vehicle requirements and receive notifications for maintenance needs.",
            "Our goal is to ensure your vehicle operates at peak efficiency while keeping you and your passengers safe.",
            "Thank you for choosing SUS Premium TPMS. Let's keep your journey safe and efficient!"
        ];
    }

    playIntroduction() {
        if (this.isSpeaking) {
            this.stopIntroduction();
            return;
        }

        this.isSpeaking = true;
        this.updateUI(true);
        this.showStatus("Starting introduction...");

        this.speakMessages(0);
    }

    speakMessages(index) {
        if (index >= this.introMessages.length || !this.isSpeaking) {
            this.isSpeaking = false;
            this.updateUI(false);
            this.showStatus("Introduction completed");
            setTimeout(() => this.hideStatus(), 3000);
            return;
        }

        const message = this.introMessages[index];
        this.showStatus(`Playing: ${message.substring(0, 50)}...`);

        this.currentUtterance = new SpeechSynthesisUtterance(message);
        this.currentUtterance.rate = 0.9;
        this.currentUtterance.pitch = 1;
        this.currentUtterance.volume = 0.8;

        this.currentUtterance.onend = () => {
            setTimeout(() => {
                if (this.isSpeaking) {
                    this.speakMessages(index + 1);
                }
            }, 500);
        };

        this.currentUtterance.onerror = (event) => {
            console.error('Speech synthesis error:', event);
            this.showStatus("Error playing introduction");
            this.isSpeaking = false;
            this.updateUI(false);
        };

        this.synth.speak(this.currentUtterance);
    }

    stopIntroduction() {
        this.isSpeaking = false;
        if (this.synth.speaking) {
            this.synth.cancel();
        }
        this.updateUI(false);
        this.showStatus("Introduction stopped");
        setTimeout(() => this.hideStatus(), 2000);
    }

    updateUI(speaking) {
        const playBtn = document.getElementById('playIntroBtn');
        const stopBtn = document.getElementById('stopIntroBtn');
        const playIcon = document.getElementById('playIcon');

        if (speaking) {
            playBtn.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
            playIcon.className = 'fas fa-pause';
            stopBtn.style.display = 'inline-block';
        } else {
            playBtn.style.background = 'linear-gradient(135deg, var(--success), #059669)';
            playIcon.className = 'fas fa-play';
            stopBtn.style.display = 'none';
        }
    }

    showStatus(message) {
        const statusEl = document.getElementById('voiceStatus');
        statusEl.textContent = message;
        statusEl.style.display = 'block';
    }

    hideStatus() {
        const statusEl = document.getElementById('voiceStatus');
        statusEl.style.display = 'none';
    }

    // Auto-play on page load (optional)
    autoPlayOnLoad() {
        // Wait for page to load completely
        setTimeout(() => {
            this.showStatus("");
        }, 2000);
    }
}

// Initialize Voice Introduction
let voiceIntro;

document.addEventListener('DOMContentLoaded', function() {
    voiceIntro = new VoiceIntroduction();
    
    const playBtn = document.getElementById('playIntroBtn');
    const stopBtn = document.getElementById('stopIntroBtn');

    if (playBtn) {
        playBtn.addEventListener('click', function() {
            voiceIntro.playIntroduction();
        });
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', function() {
            voiceIntro.stopIntroduction();
        });
    }

    // Start auto-play suggestion
    voiceIntro.autoPlayOnLoad();
});
</script>
<script>
<script>
// Register Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/service-worker.js')
            .then(function(registration) {
                console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch(function(error) {
                console.log('Service Worker registration failed:', error);
            });
    });
}

// Check if app is installed
window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent the mini-infobar from appearing on mobile
    e.preventDefault();
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    // Show install button
    showInstallPromotion();
});

// Online/Offline detection
window.addEventListener('online', function() {
    document.body.classList.remove('offline');
    showNotification('Connection restored', 'success');
});

window.addEventListener('offline', function() {
    document.body.classList.add('offline');
    showNotification('You are currently offline', 'warning');
});

function showNotification(message, type) {
    // Create a notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background: ${type === 'success' ? '#10b981' : '#f59e0b'};
        color: white;
        border-radius: 8px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
<script>
// Register Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/service-worker.js')
            .then(function(registration) {
                console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch(function(error) {
                console.log('Service Worker registration failed:', error);
            });
    });
}

// Check if app is installed
window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent the mini-infobar from appearing on mobile
    e.preventDefault();
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    // Show install button
    showInstallPromotion();
});

// Online/Offline detection
window.addEventListener('online', function() {
    document.body.classList.remove('offline');
    showNotification('Connection restored', 'success');
});

window.addEventListener('offline', function() {
    document.body.classList.add('offline');
    showNotification('You are currently offline', 'warning');
});

function showNotification(message, type) {
    // Create a notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background: ${type === 'success' ? '#10b981' : '#f59e0b'};
        color: white;
        border-radius: 8px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
</body>
</html>