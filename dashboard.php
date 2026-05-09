<?php
require_once 'config.php';
requireLogin();


$user_id = $_SESSION['user_id'];
$tire_data = [];

$stmt = $conn->prepare("SELECT * FROM tire_data WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $tire_data = $result->fetch_assoc();
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
<!-- Add this in your dashboard header section -->
<div style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
    <button id="voiceAssistantBtn" class="modern-btn" style="padding: 15px; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-microphone" id="micIcon"></i>
    </button>
    <div id="voiceStatus" style="position: absolute; bottom: 70px; right: 0; background: rgba(0,0,0,0.8); padding: 8px 12px; border-radius: 8px; font-size: 0.8rem; display: none;">
        Click to speak
    </div>
</div>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SUS Premium TPMS</title>
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

        .dashboard-section {
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

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .main-display {
            grid-column: 1 / -1;
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

        .tire-gauge {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            height: 8px;
            overflow: hidden;
            position: relative;
        }

        .gauge-fill {
            height: 100%;
            border-radius: 20px;
            transition: width 0.5s ease;
        }

        .pressure-good { background: linear-gradient(90deg, var(--success), #10b981); }
        .pressure-warning { background: linear-gradient(90deg, var(--warning), #f59e0b); }
        .pressure-critical { background: linear-gradient(90deg, var(--danger), #ef4444); }

        .tire-status {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .tire {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .tire:hover {
            border-color: var(--neon-blue);
            transform: translateY(-2px);
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

        .stat-number {
            font-size: 3rem;
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

        .alert-item {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 0.8rem;
            border-left: 4px solid;
            backdrop-filter: blur(10px);
        }

        .alert-critical {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: var(--danger);
            color: var(--danger);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-left-color: var(--warning);
            color: var(--warning);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left-color: var(--success);
            color: var(--success);
        }

        .dashboard-header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .tire-status {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<section class="dashboard-section" style="padding: 140px 0 60px 0;">
    <div class="container">
        <!-- Dashboard Header -->
        <div class="dashboard-header modern-card main-display" style="margin-bottom: 2rem;">
            <div class="flex justify-between items-center">
                <h2 class="section-title" style="margin: 0; text-align: left; font-size: 2.2rem;">
                    <i class="fas fa-gauge-high" style="margin-right: 15px;"></i>Dashboard
                </h2>
                <div class="flex gap-4">
                    <a href="tire-health.php" class="modern-btn">
                        <i class="fas fa-chart-line"></i> Analytics
                    </a>
                    <button class="modern-btn" style="background: linear-gradient(135deg, #6b7280, #4b5563);" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

       
        <div class="dashboard-grid">
            <div class="modern-card" onclick="showVehicleDetails()" style="cursor: pointer;">
                <h3 class="flex items-center gap-2" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-car-side" style="color: var(--neon-blue);"></i> Vehicle Status
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <p style="color: var(--text-secondary); margin-bottom: 5px;"><strong>Vehicle:</strong></p>
                        <p style="color: var(--text-primary); font-size: 1.2rem; margin: 0; font-weight: 600;">
                            <?php echo isset($tire_data['vehicle_name']) ? $tire_data['vehicle_name'] : 'Toyota Camry 2023'; ?>
                        </p>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); margin-bottom: 5px;"><strong>Last Updated:</strong></p>
                        <p style="color: var(--text-primary); font-size: 1.1rem; margin: 0; font-weight: 500;">
                            <?php echo isset($tire_data['timestamp']) ? date('M j, Y g:i A', strtotime($tire_data['timestamp'])) : date('M j, Y g:i A'); ?>
                        </p>
                    </div>
                </div>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <button class="modern-btn" style="width: 100%; padding: 10px;" onclick="event.stopPropagation(); showVehicleDetails()">
                        <i class="fas fa-info-circle"></i> View Details
                    </button>
                </div>
            </div>

            <div class="modern-card" onclick="showHealthReport()" style="cursor: pointer;">
                <h3 class="flex items-center gap-2" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-heart-pulse" style="color: var(--neon-blue);"></i> Overall Health
                </h3>
                <?php if (!empty($tire_data)): ?>
                    <?php
                    $avg_pressure = ($tire_data['front_left_pressure'] + $tire_data['front_right_pressure'] + 
                                    $tire_data['rear_left_pressure'] + $tire_data['rear_right_pressure']) / 4;
                    $avg_temp = ($tire_data['front_left_temp'] + $tire_data['front_right_temp'] + 
                                $tire_data['rear_left_temp'] + $tire_data['rear_right_temp']) / 4;
                    
                    $status_class = 'status-good';
                    $status_text = 'Excellent';
                    if ($avg_pressure < 28 || $avg_pressure > 38 || $avg_temp > 45) {
                        $status_class = 'status-critical';
                        $status_text = 'Critical';
                    } elseif ($avg_pressure < 30 || $avg_pressure > 35 || $avg_temp > 40) {
                        $status_class = 'status-warning';
                        $status_text = 'Attention Needed';
                    }
                    ?>
                    <div style="text-align: center; margin-bottom: 15px;">
                        <span class="status-indicator <?php echo $status_class; ?>"></span>
                        <span style="color: var(--text-primary); font-size: 1.3rem; font-weight: 600;"><?php echo $status_text; ?></span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <p style="color: var(--text-secondary); margin-bottom: 8px;">Avg Pressure</p>
                            <div class="stat-number"><?php echo number_format($avg_pressure, 1); ?> PSI</div>
                        </div>
                        <div>
                            <p style="color: var(--text-secondary); margin-bottom: 8px;">Avg Temperature</p>
                            <div class="stat-number"><?php echo number_format($avg_temp, 1); ?> °C</div>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-secondary); text-align: center; font-style: italic; padding: 2rem;">
                        Awaiting sensor data...
                    </p>
                <?php endif; ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <button class="modern-btn" style="width: 100%; padding: 10px; background: linear-gradient(135deg, var(--success), #059669);" onclick="event.stopPropagation(); showHealthReport()">
                        <i class="fas fa-file-medical"></i> Health Report
                    </button>
                </div>
            </div>
            <div class="modern-card" onclick="showAlerts()" style="cursor: pointer;">
                <h3 class="flex items-center gap-2" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-triangle-exclamation" style="color: var(--neon-blue);"></i> System Alerts
                </h3>
                <?php if (!empty($tire_data)): ?>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <?php
                        $tires = [
                            'Front Left' => ['pressure' => $tire_data['front_left_pressure'], 'temp' => $tire_data['front_left_temp']],
                            'Front Right' => ['pressure' => $tire_data['front_right_pressure'], 'temp' => $tire_data['front_right_temp']],
                            'Rear Left' => ['pressure' => $tire_data['rear_left_pressure'], 'temp' => $tire_data['rear_left_temp']],
                            'Rear Right' => ['pressure' => $tire_data['rear_right_pressure'], 'temp' => $tire_data['rear_right_temp']]
                        ];
                        
                        $alerts = [];
                        foreach ($tires as $position => $data) {
                            if ($data['pressure'] < 28) {
                                $alerts[] = ["type" => "critical", "message" => "Low pressure in $position tire (" . $data['pressure'] . " PSI)"];
                            } elseif ($data['pressure'] > 38) {
                                $alerts[] = ["type" => "critical", "message" => "High pressure in $position tire (" . $data['pressure'] . " PSI)"];
                            } elseif ($data['pressure'] < 30 || $data['pressure'] > 35) {
                                $alerts[] = ["type" => "warning", "message" => "Check pressure in $position tire (" . $data['pressure'] . " PSI)"];
                            }
                            
                            if ($data['temp'] > 45) {
                                $alerts[] = ["type" => "critical", "message" => "High temperature in $position tire (" . $data['temp'] . "°C)"];
                            } elseif ($data['temp'] > 40) {
                                $alerts[] = ["type" => "warning", "message" => "Elevated temperature in $position tire (" . $data['temp'] . "°C)"];
                            }
                        }
                        
                        if (empty($alerts)) {
                            echo '<div class="alert-item alert-success">
                                    <i class="fas fa-check-circle"></i> All systems optimal
                                  </div>';
                        } else {
                            foreach ($alerts as $alert) {
                                $alert_class = $alert["type"] == "critical" ? "alert-critical" : "alert-warning";
                                echo '<div class="alert-item ' . $alert_class . '">
                                        <i class="fas fa-exclamation-triangle"></i> ' . $alert["message"] . '
                                      </div>';
                            }
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-secondary); text-align: center; font-style: italic; padding: 2rem;">
                        No alerts to display
                    </p>
                <?php endif; ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <button class="modern-btn" style="width: 100%; padding: 10px; background: linear-gradient(135deg, var(--warning), #d97706);" onclick="event.stopPropagation(); showAlerts()">
                        <i class="fas fa-bell"></i> View All Alerts
                    </button>
                </div>
            </div>
            <div class="modern-card" onclick="showStatistics()" style="cursor: pointer;">
                <h3 class="flex items-center gap-2" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-tachometer-alt" style="color: var(--neon-blue);"></i> Quick Stats
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: 800; color: var(--neon-blue);">32.5</div>
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Avg Pressure</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: 800; color: var(--success);">28°</div>
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Avg Temp</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: 800; color: var(--warning);">92%</div>
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Tire Life</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: 800; color: var(--text-primary);">0</div>
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Alerts</div>
                    </div>
                </div>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <button class="modern-btn" style="width: 100%; padding: 10px; background: linear-gradient(135deg, #8b5cf6, #7c3aed);" onclick="event.stopPropagation(); showStatistics()">
                        <i class="fas fa-chart-bar"></i> Detailed Stats
                    </button>
                </div>
            </div>
        </div>
        <div class="modern-card main-display">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 class="flex items-center gap-2">
                    <i class="fas fa-tachometer-alt" style="color: var(--neon-blue);"></i> Detailed Tire Analysis
                </h3>
                <button class="modern-btn" onclick="exportTireData()">
                    <i class="fas fa-download"></i> Export Data
                </button>
            </div>
            <div class="tire-status">
                <?php if (!empty($tire_data)): ?>
                    <?php
                    $tire_positions = [
                        'Front Left' => ['pressure' => $tire_data['front_left_pressure'], 'temp' => $tire_data['front_left_temp']],
                        'Front Right' => ['pressure' => $tire_data['front_right_pressure'], 'temp' => $tire_data['front_right_temp']],
                        'Rear Left' => ['pressure' => $tire_data['rear_left_pressure'], 'temp' => $tire_data['rear_left_temp']],
                        'Rear Right' => ['pressure' => $tire_data['rear_right_pressure'], 'temp' => $tire_data['rear_right_temp']]
                    ];
                    
                    foreach ($tire_positions as $position => $data):
                        $pressure_class = 'pressure-good';
                        if ($data['pressure'] < 28 || $data['pressure'] > 38) {
                            $pressure_class = 'pressure-critical';
                        } elseif ($data['pressure'] < 30 || $data['pressure'] > 35) {
                            $pressure_class = 'pressure-warning';
                        }
                        
                        $pressure_percentage = min(100, max(0, ($data['pressure'] - 20) / 20 * 100));
                    ?>
                    <div class="tire" onclick="showTireDetails('<?php echo $position; ?>')" style="cursor: pointer;">
                        <h4 style="color: var(--text-secondary); margin-bottom: 15px; font-size: 1.1rem;"><?php echo $position; ?></h4>
                        <div style="color: var(--neon-blue); font-size: 2rem; font-weight: 700; margin-bottom: 10px;">
                            <?php echo $data['pressure']; ?> PSI
                        </div>
                        <div class="tire-gauge" style="margin-bottom: 10px;">
                            <div class="gauge-fill <?php echo $pressure_class; ?>" style="width: <?php echo $pressure_percentage; ?>%;"></div>
                        </div>
                        <div style="color: var(--text-secondary); font-size: 1rem;">
                            <i class="fas fa-temperature-three-quarters"></i> <?php echo $data['temp']; ?> °C
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                        <i class="fas fa-tire" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                        <p style="color: var(--text-secondary); font-style: italic;">No tire data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>


<script>
function showVehicleDetails() {
    alert('Showing vehicle details...');
}

function showHealthReport() {
    alert('Generating health report...');
}

function showAlerts() {
    alert('Showing all alerts...');
    
}

function showStatistics() {
    alert('Showing detailed statistics...');
   
}

function showTireDetails(position) {
    alert('Showing details for ' + position + ' tire');
   
}

function refreshData() {
   
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
    btn.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 1500);
}

function exportTireData() {
    alert('Exporting tire data to CSV...');
}


document.addEventListener('DOMContentLoaded', function() {
  
    const cards = document.querySelectorAll('.modern-card');
    cards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
});
</script>
    <script>
       
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

class VoiceAssistant {
    constructor() {
        this.isListening = false;
        this.recognition = null;
        this.synth = window.speechSynthesis;
        this.initSpeechRecognition();
    }

    initSpeechRecognition() {
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            
            this.recognition.continuous = false;
            this.recognition.interimResults = false;
            this.recognition.lang = 'en-US';

            this.recognition.onstart = () => {
                this.isListening = true;
                this.updateUI(true);
                this.showVoiceStatus("Listening... Speak now");
            };

            this.recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript.toLowerCase();
                this.showVoiceStatus(`You said: "${transcript}"`);
                this.processCommand(transcript);
            };

            this.recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                this.showVoiceStatus("Error listening. Please try again.");
                this.speak("Sorry, I didn't catch that. Please try again.");
            };

            this.recognition.onend = () => {
                this.isListening = false;
                this.updateUI(false);
                setTimeout(() => this.hideVoiceStatus(), 3000);
            };
        } else {
            this.showVoiceStatus("Speech recognition not supported in your browser");
        }
    }

    processCommand(transcript) {
        console.log('Voice command:', transcript);
        
        const tireData = this.getCurrentTireData();
        
        if (transcript.includes('pressure') || transcript.includes('psi')) {
            this.handlePressureQuery(transcript, tireData);
        }
        else if (transcript.includes('temperature') || transcript.includes('temp') || transcript.includes('hot')) {
            this.handleTemperatureQuery(transcript, tireData);
        }
        else if (transcript.includes('status') || transcript.includes('how are') || transcript.includes('condition')) {
            this.handleStatusQuery(tireData);
        }
        else if (transcript.includes('alert') || transcript.includes('warning') || transcript.includes('problem')) {
            this.handleAlertQuery(tireData);
        }
        else if (transcript.includes('hello') || transcript.includes('hi') || transcript.includes('hey')) {
            this.speak("Hello! I'm your TPMS assistant. You can ask me about tire pressure, temperature, or overall status.");
        }
        else if (transcript.includes('thank')) {
            this.speak("You're welcome! Let me know if you need anything else.");
        }
        else if (transcript.includes('help')) {
            this.speak("I can help you with: checking tire pressure, temperature, overall status, or alerts. Just ask!");
        }
        else {
            this.speak("I'm not sure how to help with that. Try asking about tire pressure, temperature, or status.");
        }
    }

    getCurrentTireData() {
        try {
            const tireElements = document.querySelectorAll('.tire');
            const data = {};
            
            tireElements.forEach((tire, index) => {
                const pressureElement = tire.querySelector('.stat-number') || tire.querySelector('div[style*="color: var(--neon-blue)"]') || tire.querySelector('div:first-child');
                const tempElement = tire.querySelector('div[style*="temperature"]') || tire.querySelector('div:last-child');
                
                if (pressureElement) {
                    const pressureText = pressureElement.textContent;
                    const pressureMatch = pressureText.match(/(\d+\.?\d*)\s*PSI/);
                    if (pressureMatch) {
                        data[`tire_${index}_pressure`] = parseFloat(pressureMatch[1]);
                    }
                }
                
                if (tempElement) {
                    const tempText = tempElement.textContent;
                    const tempMatch = tempText.match(/(\d+\.?\d*)\s*°C/);
                    if (tempMatch) {
                        data[`tire_${index}_temp`] = parseFloat(tempMatch[1]);
                    }
                }
            });
            
            return data;
        } catch (error) {
            console.error('Error getting tire data:', error);
            return {};
        }
    }

    handlePressureQuery(transcript, tireData) {
        if (Object.keys(tireData).length === 0) {
            this.speak("I can't access the current tire pressure data. Please check if the dashboard is displaying information.");
            return;
        }

        const pressures = Object.values(tireData).filter(val => !isNaN(val) && val > 10 && val < 50);
        if (pressures.length > 0) {
            const avgPressure = pressures.reduce((a, b) => a + b, 0) / pressures.length;
            
            let status = "optimal";
            if (avgPressure < 28) status = "critically low";
            else if (avgPressure < 30) status = "low";
            else if (avgPressure > 38) status = "critically high";
            else if (avgPressure > 35) status = "high";
            
            this.speak(`Your average tire pressure is ${avgPressure.toFixed(1)} PSI. This is ${status}.`);
        } else {
            this.speak("I couldn't read the pressure values from the dashboard.");
        }
    }
    handleTemperatureQuery(transcript, tireData) {
        if (Object.keys(tireData).length === 0) {
            this.speak("Temperature data is not available right now.");
            return;
        }

        const temps = Object.values(tireData).filter(val => !isNaN(val) && val > -10 && val < 100);
        if (temps.length > 0) {
            const avgTemp = temps.reduce((a, b) => a + b, 0) / temps.length;
            
            let status = "normal";
            if (avgTemp > 45) status = "critically high";
            else if (avgTemp > 40) status = "high";
            
            this.speak(`Average tire temperature is ${avgTemp.toFixed(1)} degrees Celsius. This is ${status}.`);
        } else {
            this.speak("Based on the dashboard display, your tire temperatures appear to be within normal range.");
        }
    }

    handleStatusQuery(tireData) {
        if (Object.keys(tireData).length === 0) {
            this.speak("I can't determine the current status. Please ensure your tire sensors are connected.");
            return;
        }

        const pressures = Object.values(tireData).filter(val => !isNaN(val) && val > 10 && val < 50);
        if (pressures.length > 0) {
            const avgPressure = pressures.reduce((a, b) => a + b, 0) / pressures.length;
            
            let status = "good";
            if (avgPressure < 28 || avgPressure > 38) status = "needs immediate attention";
            else if (avgPressure < 30 || avgPressure > 35) status = "should be checked";
            
            this.speak(`Overall tire status is ${status}. Average pressure is ${avgPressure.toFixed(1)} PSI.`);
        } else {
            this.speak("The system appears to be online. Check the dashboard for detailed status information.");
        }
    }

    handleAlertQuery(tireData) {
        const pressures = Object.values(tireData).filter(val => !isNaN(val) && val > 10 && val < 50);
        
        if (pressures.length > 0) {
            const criticalPressures = pressures.filter(p => p < 28 || p > 38);
            if (criticalPressures.length > 0) {
                this.speak("Warning! Some tires have critical pressure levels. Please check the dashboard immediately.");
            } else {
                this.speak("No critical alerts at this time. All systems appear normal.");
            }
        } else {
            this.speak("I cannot check for alerts right now. Please check the dashboard manually.");
        }
    }

    speak(text) {
        if (this.synth.speaking) {
            this.synth.cancel();
        }

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.rate = 0.9;
        utterance.pitch = 1;
        utterance.volume = 0.8;
        
        utterance.onstart = () => {
            this.showVoiceStatus("Speaking...");
        };
        
        utterance.onend = () => {
            setTimeout(() => this.hideVoiceStatus(), 2000);
        };

        this.synth.speak(utterance);
    }

    startListening() {
        if (this.recognition) {
            try {
                this.recognition.start();
            } catch (error) {
                this.showVoiceStatus("Please allow microphone access");
                console.error('Recognition start error:', error);
            }
        } else {
            this.showVoiceStatus("Speech recognition not available");
        }
    }

    stopListening() {
        if (this.recognition && this.isListening) {
            this.recognition.stop();
        }
    }

    updateUI(listening) {
        const btn = document.getElementById('voiceAssistantBtn');
        const icon = document.getElementById('micIcon');
        
        if (listening) {
            btn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
            btn.style.boxShadow = '0 4px 20px rgba(239, 68, 68, 0.6)';
            icon.className = 'fas fa-stop';
        } else {
            btn.style.background = 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
            btn.style.boxShadow = '0 4px 20px rgba(59, 130, 246, 0.4)';
            icon.className = 'fas fa-microphone';
        }
    }

    showVoiceStatus(message) {
        const statusEl = document.getElementById('voiceStatus');
        statusEl.textContent = message;
        statusEl.style.display = 'block';
    }

    hideVoiceStatus() {
        const statusEl = document.getElementById('voiceStatus');
        statusEl.style.display = 'none';
    }
}

let voiceAssistant;

document.addEventListener('DOMContentLoaded', function() {
    voiceAssistant = new VoiceAssistant();
    
    const voiceBtn = document.getElementById('voiceAssistantBtn');
    if (voiceBtn) {
        voiceBtn.addEventListener('click', function() {
            if (voiceAssistant.isListening) {
                voiceAssistant.stopListening();
            } else {
                voiceAssistant.startListening();
            }
        });
    }
});
</script>
<script>
<script>
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

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    showInstallPromotion();
});
window.addEventListener('online', function() {
    document.body.classList.remove('offline');
    showNotification('Connection restored', 'success');
});

window.addEventListener('offline', function() {
    document.body.classList.add('offline');
    showNotification('You are currently offline', 'warning');
});

function showNotification(message, type) {
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

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    showInstallPromotion();
});

window.addEventListener('online', function() {
    document.body.classList.remove('offline');
    showNotification('Connection restored', 'success');
});

window.addEventListener('offline', function() {
    document.body.classList.add('offline');
    showNotification('You are currently offline', 'warning');
});

function showNotification(message, type) {
    
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
</script><script>
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


window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    showInstallPromotion();
});
window.addEventListener('online', function() {
    document.body.classList.remove('offline');
    showNotification('Connection restored', 'success');
});

window.addEventListener('offline', function() {
    document.body.classList.add('offline');
    showNotification('You are currently offline', 'warning');
});

function showNotification(message, type) {
   
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

window.addEventListener('beforeinstallprompt', (e) => {
    
    e.preventDefault();
    
    deferredPrompt = e;
    
    showInstallPromotion();
});

window.addEventListener('online', function() {
    document.body.classList.remove('offline');
    showNotification('Connection restored', 'success');
});

window.addEventListener('offline', function() {
    document.body.classList.add('offline');
    showNotification('You are currently offline', 'warning');
});

function showNotification(message, type) {
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
function toggleMobileMenu() {
    const nav = document.getElementById('mainNav');
    const btn = document.getElementById('mobileMenuBtn');
    
    if (nav.style.display === 'block') {
        nav.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-bars"></i>';
    } else {
        nav.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-times"></i>';
    }
}

document.addEventListener('click', function(event) {
    const nav = document.getElementById('mainNav');
    const btn = document.getElementById('mobileMenuBtn');
    
    if (!event.target.closest('#mainNav') && !event.target.closest('#mobileMenuBtn')) {
        nav.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-bars"></i>';
    }
});

function isTouchDevice() {
    return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
}

if (isTouchDevice()) {
    document.body.classList.add('touch-device');
}
let lastTouchEnd = 0;
document.addEventListener('touchend', function (event) {
    const now = (new Date()).getTime();
    if (now - lastTouchEnd <= 300) {
        event.preventDefault();
    }
    lastTouchEnd = now;
}, false);

function adjustFontSize() {
    const width = window.innerWidth;
    if (width < 768) {
        document.documentElement.style.fontSize = '14px';
    } else {
        document.documentElement.style.fontSize = '16px';
    }
}

window.addEventListener('resize', adjustFontSize);
adjustFontSize(); 

let startX = null;

document.addEventListener('touchstart', (e) => {
    startX = e.touches[0].clientX;
});

document.addEventListener('touchend', (e) => {
    if (!startX) return;
    
    const endX = e.changedTouches[0].clientX;
    const diffX = startX - endX;
    
    if (Math.abs(diffX) > 50) { 
        if (diffX > 0) {
            console.log('Swiped left');
        } else {
            console.log('Swiped right');
        }
    }
    
    startX = null;
});
</script>
</body>
</html>
