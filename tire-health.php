<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$tire_data = [];

// Fetch all tire data for the user
$stmt = $conn->prepare("SELECT * FROM tire_data WHERE user_id = ? ORDER BY timestamp DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $tire_data[] = $row;
}
$stmt->close();
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
    <title>Tire Health Analytics - SUS Premium TPMS</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .tirehealth-section {
            padding: 140px 0 60px 0;
            min-height: 100vh;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-align: center;
            margin-bottom: 3rem;
            letter-spacing: -0.5px;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .modern-card::before {
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

        .modern-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .main-display {
            grid-column: 1 / -1;
        }

        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            overflow: hidden;
        }

        .data-table th {
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            color: white;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
            font-weight: 500;
        }

        .data-table tr:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .modern-btn {
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
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

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 10px currentColor;
        }

        .status-good { 
            background: var(--success); 
            box-shadow: 0 0 15px var(--success); 
        }

        .status-warning { 
            background: var(--warning); 
            box-shadow: 0 0 15px var(--warning); 
        }

        .status-critical { 
            background: var(--danger); 
            box-shadow: 0 0 15px var(--danger); 
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .tire-health-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .tire-health-card {
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .tire-health-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .time-filter {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .time-filter-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .time-filter-btn:hover,
        .time-filter-btn.active {
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            color: white;
            border-color: var(--neon-blue);
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

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-glow);
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

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .header-content {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            nav ul {
                gap: 1rem;
            }
            
            .time-filter {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<!-- Mobile-Optimized Charts Section -->
<div class="modern-card main-display">
    <h3 style="color: var(--text-primary); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-chart-line" style="color: var(--neon-blue);"></i> Analytics
    </h3>
    
    <div class="dashboard-grid">
        <!-- Pressure Trends Card -->
        <div class="modern-card">
            <h4 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-gauge-high" style="color: var(--neon-blue);"></i> Pressure Trends
            </h4>
            <div class="chart-container" style="height: 250px; position: relative;">
                <canvas id="pressureChart"></canvas>
            </div>
            <div style="margin-top: 15px; text-align: center;">
                <div style="display: flex; justify-content: space-around; font-size: 0.8rem; color: var(--text-secondary);">
                    <span>Current: 32.5 PSI</span>
                    <span>Avg: 32.1 PSI</span>
                </div>
            </div>
        </div>
        
        <!-- Temperature Card -->
        <div class="modern-card">
            <h4 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-thermometer-half" style="color: var(--success);"></i> Temperature
            </h4>
            <div class="chart-container" style="height: 250px; position: relative;">
                <canvas id="temperatureChart"></canvas>
            </div>
            <div style="margin-top: 15px; text-align: center;">
                <div style="display: flex; justify-content: space-around; font-size: 0.8rem; color: var(--text-secondary);">
                    <span>Current: 28°C</span>
                    <span>Avg: 27°C</span>
                </div>
            </div>
        </div>
        
        <!-- Tire Wear Card -->
        <div class="modern-card">
            <h4 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-road" style="color: var(--warning);"></i> Tire Wear
            </h4>
            <div class="chart-container" style="height: 250px; position: relative;">
                <canvas id="wearChart"></canvas>
            </div>
            <div style="margin-top: 15px; text-align: center;">
                <div style="display: flex; justify-content: space-around; font-size: 0.8rem; color: var(--text-secondary);">
                    <span>Front: 92%</span>
                    <span>Rear: 88%</span>
                </div>
            </div>
        </div>
        
        <!-- Health Score Card -->
        <div class="modern-card">
            <h4 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-heart-pulse" style="color: var(--danger);"></i> Health Score
            </h4>
            <div style="height: 250px; display: flex; align-items: center; justify-content: center;">
                <div style="text-align: center;">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: conic-gradient(var(--success) 0% 87%, var(--text-muted) 87% 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--black-secondary); display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">87%</span>
                        </div>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Overall Health</div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                        <li><a href="tire-health.php" class="active">Tire Health</a></li>
                    </ul>
                </nav>
                
                <div class="auth-buttons">
                    <?php if (isLoggedIn()): ?>
                        <span style="color: var(--text-secondary); font-weight: 500; margin-right: 15px;">
                            <span class="pulse-dot"></span>Welcome, <?php echo $_SESSION['username']; ?>
                        </span>
                        <a href="logout.php" class="modern-btn" style="background: linear-gradient(135deg, var(--danger), #dc2626);">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="modern-btn">Login</a>
                        <a href="signup.php" class="modern-btn" style="background: linear-gradient(135deg, var(--success), #059669);">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <section class="tirehealth-section">
        <div class="container">
            <div class="dashboard-header" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 2rem; margin-bottom: 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2 class="section-title" style="margin: 0; text-align: left; font-size: 2.2rem;">
                        <i class="fas fa-chart-line" style="margin-right: 15px;"></i>Tire Health Analytics
                    </h2>
                    <a href="dashboard.php" class="modern-btn">
                        <i class="fas fa-gauge-high" style="margin-right: 8px;"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Time Filter -->
            <div class="time-filter">
                <button class="time-filter-btn active">Last 24 Hours</button>
                <button class="time-filter-btn">Last 7 Days</button>
                <button class="time-filter-btn">Last 30 Days</button>
                <button class="time-filter-btn">All Time</button>
            </div>

            <div class="dashboard-grid">
                <!-- Overall Statistics -->
                <div class="modern-card">
                    <h3 style="color: var(--text-primary); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-chart-bar" style="color: var(--neon-blue);"></i> Overall Statistics
                    </h3>
                    <?php if (!empty($tire_data)): ?>
                        <?php
                        $latest_data = $tire_data[0];
                        $avg_pressure = ($latest_data['front_left_pressure'] + $latest_data['front_right_pressure'] + 
                                        $latest_data['rear_left_pressure'] + $latest_data['rear_right_pressure']) / 4;
                        $avg_temp = ($latest_data['front_left_temp'] + $latest_data['front_right_temp'] + 
                                    $latest_data['rear_left_temp'] + $latest_data['rear_right_temp']) / 4;
                        ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div style="text-align: center;">
                                <div class="stat-number"><?php echo number_format($avg_pressure, 1); ?></div>
                                <div class="stat-label">Avg Pressure (PSI)</div>
                            </div>
                            <div style="text-align: center;">
                                <div class="stat-number"><?php echo number_format($avg_temp, 1); ?></div>
                                <div class="stat-label">Avg Temperature (°C)</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--text-secondary); text-align: center; font-style: italic; padding: 2rem;">
                            No tire data available
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Health Score -->
                <div class="modern-card">
                    <h3 style="color: var(--text-primary); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-heart-pulse" style="color: var(--neon-blue);"></i> Health Score
                    </h3>
                    <?php if (!empty($tire_data)): ?>
                        <?php
                        $health_score = 95; // This would be calculated based on various factors
                        $score_color = $health_score >= 80 ? 'var(--success)' : ($health_score >= 60 ? 'var(--warning)' : 'var(--danger)');
                        ?>
                        <div style="text-align: center;">
                            <div class="stat-number" style="color: <?php echo $score_color; ?>;"><?php echo $health_score; ?>%</div>
                            <div class="stat-label">Overall Tire Health</div>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--text-secondary); text-align: center; font-style: italic; padding: 2rem;">
                            Calculating health score...
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Data Points -->
                <div class="modern-card">
                    <h3 style="color: var(--text-primary); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-database" style="color: var(--neon-blue);"></i> Data Collection
                    </h3>
                    <div style="text-align: center;">
                        <div class="stat-number"><?php echo count($tire_data); ?></div>
                        <div class="stat-label">Data Points Recorded</div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="modern-card main-display">
                <h3 style="color: var(--text-primary); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-chart-line" style="color: var(--neon-blue);"></i> Pressure & Temperature Trends
                </h3>
                <div class="chart-container">
                    <canvas id="healthChart"></canvas>
                </div>
            </div>

            <!-- Historical Data Table -->
            <div class="modern-card main-display">
                <h3 style="color: var(--text-primary); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-table" style="color: var(--neon-blue);"></i> Historical Data
                </h3>
                <?php if (!empty($tire_data)): ?>
                    <div style="overflow-x: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Front Left</th>
                                    <th>Front Right</th>
                                    <th>Rear Left</th>
                                    <th>Rear Right</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($tire_data, 0, 10) as $data): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y g:i A', strtotime($data['timestamp'])); ?></td>
                                        <td><?php echo $data['front_left_pressure']; ?> PSI</td>
                                        <td><?php echo $data['front_right_pressure']; ?> PSI</td>
                                        <td><?php echo $data['rear_left_pressure']; ?> PSI</td>
                                        <td><?php echo $data['rear_right_pressure']; ?> PSI</td>
                                        <td>
                                            <?php
                                            $avg = ($data['front_left_pressure'] + $data['front_right_pressure'] + 
                                                   $data['rear_left_pressure'] + $data['rear_right_pressure']) / 4;
                                            if ($avg >= 30 && $avg <= 35) {
                                                echo '<span class="status-indicator status-good"></span> Optimal';
                                            } elseif ($avg >= 28 && $avg < 30 || $avg > 35 && $avg <= 38) {
                                                echo '<span class="status-indicator status-warning"></span> Check';
                                            } else {
                                                echo '<span class="status-indicator status-critical"></span> Critical';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-secondary); text-align: center; font-style: italic; padding: 2rem;">
                        No historical data available
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.modern-header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(10, 10, 10, 0.95)';
                header.style.backdropFilter = 'blur(30px) saturate(200%)';
            } else {
                header.style.background = 'rgba(10, 10, 10, 0.8)';
                header.style.backdropFilter = 'blur(20px) saturate(180%)';
            }
        });

        // Time filter functionality
        document.querySelectorAll('.time-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.time-filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Here you would typically filter the data based on the selected time range
            });
        });

        // Initialize Chart
        const ctx = document.getElementById('healthChart').getContext('2d');
        const healthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Average Pressure (PSI)',
                        data: [32, 33, 31, 34, 32, 33],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Average Temperature (°C)',
                        data: [25, 28, 26, 30, 27, 29],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#b0b0b0'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#b0b0b0'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#b0b0b0'
                        }
                    }
                }
            }
        });

        // Add interactive card effects
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.modern-card');
            cards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const angleX = (y - centerY) / 20;
                    const angleY = (centerX - x) / 20;
                    
                    card.style.transform = `perspective(1000px) rotateX(${angleX}deg) rotateY(${angleY}deg) translateY(-8px)`;
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
                });
            });
        });
    </script>
	<script>
// Initialize mobile-optimized charts
document.addEventListener('DOMContentLoaded', function() {
    // Pressure Chart
    const pressureCtx = document.getElementById('pressureChart')?.getContext('2d');
    if (pressureCtx) {
        new Chart(pressureCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Pressure (PSI)',
                    data: [32.1, 32.3, 32.0, 32.5, 32.2, 32.4, 32.5],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 31,
                        max: 34
                    }
                }
            }
        });
    }

    // Temperature Chart
    const tempCtx = document.getElementById('temperatureChart')?.getContext('2d');
    if (tempCtx) {
        new Chart(tempCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Temperature (°C)',
                    data: [26, 28, 27, 29, 28, 27, 28],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Wear Chart
    const wearCtx = document.getElementById('wearChart')?.getContext('2d');
    if (wearCtx) {
        new Chart(wearCtx, {
            type: 'doughnut',
            data: {
                labels: ['Front Left', 'Front Right', 'Rear Left', 'Rear Right'],
                datasets: [{
                    data: [92, 91, 88, 89],
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#8b5cf6'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
</body>
</html>