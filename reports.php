<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$report_type = $_GET['type'] ?? 'weekly';
$vehicle_id = $_GET['vehicle'] ?? null;

// Fetch user's vehicles for dropdown
$vehicles = [];
$stmt = $conn->prepare("SELECT id, make, model, license_plate FROM vehicles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $vehicles[] = $row;
}
$stmt->close();

// Generate sample report data
function generateReportData($type, $vehicle_id = null) {
    $periods = [
        'weekly' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'monthly' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        'quarterly' => ['Jan-Mar', 'Apr-Jun', 'Jul-Sep', 'Oct-Dec']
    ];
    
    $data = [
        'summary' => [
            'total_miles' => rand(100, 1000),
            'avg_pressure' => round(30 + (rand(-20, 20) / 10), 1),
            'avg_temperature' => round(25 + (rand(-10, 10) / 2), 1),
            'alerts_count' => rand(0, 5),
            'health_score' => rand(80, 98),
            'fuel_savings' => round(rand(50, 200) / 10, 1),
            'co2_reduction' => rand(100, 500)
        ],
        'charts' => [
            'pressure_trend' => array_map(fn() => round(30 + (rand(-15, 15) / 10), 1), range(1, count($periods[$type]))),
            'temperature_trend' => array_map(fn() => round(25 + (rand(-8, 8) / 2), 1), range(1, count($periods[$type]))),
            'wear_distribution' => [
                'front_left' => rand(85, 95),
                'front_right' => rand(85, 95),
                'rear_left' => rand(80, 90),
                'rear_right' => rand(80, 90)
            ]
        ],
        'periods' => $periods[$type],
        'insights' => [
            'Pressure maintained within optimal range (30-35 PSI)',
            'Temperature fluctuations are normal for driving conditions',
            'Tire wear is even across all positions',
            'No critical alerts during this period',
            'Fuel efficiency improved by 3.2% through proper tire maintenance'
        ],
        'recommendations' => [
            'Continue current maintenance schedule',
            'Next rotation due in approximately 1,200 miles',
            'Monitor rear tire wear pattern',
            'Consider seasonal pressure adjustments'
        ]
    ];
    
    return $data;
}

$report_data = generateReportData($report_type, $vehicle_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - SUS Premium TPMS</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* PC Optimized Report Styles */
        .reports-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
            align-items: start;
        }
        
        .reports-sidebar {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 140px;
        }
        
        .reports-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .report-filters {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .filter-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 0.7rem 1rem;
            color: var(--text-primary);
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .filter-select:hover {
            border-color: rgba(59, 130, 246, 0.3);
            background: rgba(255, 255, 255, 0.08);
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .summary-card {
            text-align: center;
            padding: 2rem 1.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            border-color: rgba(59, 130, 246, 0.2);
        }
        
        .summary-value {
            font-size: 2.2rem;
            font-weight: 800;
            margin: 0.5rem 0;
            background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .summary-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .chart-container {
            height: 350px;
            position: relative;
        }
        
        .insights-list, .recommendations-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .insights-list li, .recommendations-list li {
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            transition: all 0.3s ease;
        }
        
        .insights-list li:hover, .recommendations-list li:hover {
            background: rgba(255, 255, 255, 0.03);
            padding-left: 0.5rem;
            border-radius: 8px;
        }
        
        .insights-list li:before {
            content: '💡';
            font-size: 1.1rem;
        }
        
        .recommendations-list li:before {
            content: '✅';
            font-size: 1.1rem;
        }
        
        .export-options {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .nav-tabs {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .nav-tab {
            padding: 1rem;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .nav-tab:hover, .nav-tab.active {
            background: rgba(59, 130, 246, 0.1);
            color: var(--neon-blue);
            border-left: 3px solid var(--neon-blue);
        }
        
        /* Enhanced chart containers */
        .chart-card {
            transition: all 0.3s ease;
        }
        
        .chart-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        /* Comparison metrics */
        .comparison-metrics {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .metric-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            transition: all 0.3s ease;
        }