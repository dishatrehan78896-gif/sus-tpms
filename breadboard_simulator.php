<?php
class BreadboardSimulator {
    private $sensorData;
    private $vehicleInfo;
    
    public function __construct() {
        $this->initializeSensors();
        $this->initializeVehicleInfo();
    }
    
    private function initializeSensors() {
        // Initialize with realistic starting values
        $this->sensorData = [
            'front_left' => [
                'pressure' => 32.5,
                'temperature' => 28.0,
                'battery' => 95,
                'last_update' => time()
            ],
            'front_right' => [
                'pressure' => 33.2,
                'temperature' => 29.0,
                'battery' => 94,
                'last_update' => time()
            ],
            'rear_left' => [
                'pressure' => 31.8,
                'temperature' => 27.0,
                'battery' => 96,
                'last_update' => time()
            ],
            'rear_right' => [
                'pressure' => 32.5,
                'temperature' => 28.0,
                'battery' => 93,
                'last_update' => time()
            ]
        ];
    }
    
    private function initializeVehicleInfo() {
        $this->vehicleInfo = [
            'vehicle_name' => 'Toyota Camry 2023',
            'vin' => '4T1B11HK5JU253487',
            'firmware_version' => '1.2.1',
            'system_uptime' => time(),
            'last_system_check' => time()
        ];
    }
    
    public function readSensorData() {
        // Simulate real sensor readings with slight variations
        foreach ($this->sensorData as $position => &$data) {
            // Simulate pressure changes (±0.3 PSI)
            $pressureChange = (mt_rand(-30, 30) / 100);
            $newPressure = $data['pressure'] + $pressureChange;
            
            // Keep pressure within safe limits (28-38 PSI)
            if ($newPressure < 28.0) $newPressure = 28.0;
            if ($newPressure > 38.0) $newPressure = 38.0;
            
            // Simulate temperature changes (±1.5°C)
            $tempChange = (mt_rand(-15, 15) / 10);
            $newTemp = $data['temperature'] + $tempChange;
            
            // Keep temperature within realistic range (20-50°C)
            if ($newTemp < 20.0) $newTemp = 20.0;
            if ($newTemp > 50.0) $newTemp = 50.0;
            
            // Simulate battery drain (very slow)
            if (mt_rand(1, 100) === 1) { // 1% chance per read
                $data['battery'] = max(80, $data['battery'] - 1);
            }
            
            $data['pressure'] = round($newPressure, 1);
            $data['temperature'] = round($newTemp, 1);
            $data['last_update'] = time();
            
            // Simulate occasional sensor issues (2% chance)
            if (mt_rand(1, 50) === 1) {
                $data['pressure'] = 0.0; // Sensor error
            }
        }
        
        return $this->formatTireData();
    }
    
    private function formatTireData() {
        return [
            'front_left_pressure' => $this->sensorData['front_left']['pressure'],
            'front_right_pressure' => $this->sensorData['front_right']['pressure'],
            'rear_left_pressure' => $this->sensorData['rear_left']['pressure'],
            'rear_right_pressure' => $this->sensorData['rear_right']['pressure'],
            'front_left_temp' => $this->sensorData['front_left']['temperature'],
            'front_right_temp' => $this->sensorData['front_right']['temperature'],
            'rear_left_temp' => $this->sensorData['rear_left']['temperature'],
            'rear_right_temp' => $this->sensorData['rear_right']['temperature'],
            'vehicle_name' => $this->vehicleInfo['vehicle_name'],
            'timestamp' => date('Y-m-d H:i:s'),
            'battery_levels' => [
                'front_left' => $this->sensorData['front_left']['battery'],
                'front_right' => $this->sensorData['front_right']['battery'],
                'rear_left' => $this->sensorData['rear_left']['battery'],
                'rear_right' => $this->sensorData['rear_right']['battery']
            ],
            'system_status' => 'online',
            'readings_count' => $this->getReadingsCount()
        ];
    }
    
    public function simulateLowPressureAlert() {
        // Force low pressure in one tire for testing
        $tire = array_rand($this->sensorData);
        $this->sensorData[$tire]['pressure'] = 26.5;
        return "Low pressure alert simulated for " . str_replace('_', ' ', $tire);
    }
    
    public function simulateHighTemperatureAlert() {
        // Force high temperature in one tire for testing
        $tire = array_rand($this->sensorData);
        $this->sensorData[$tire]['temperature'] = 47.0;
        return "High temperature alert simulated for " . str_replace('_', ' ', $tire);
    }
    
    public function resetSensors() {
        $this->initializeSensors();
        return "All sensors reset to normal values";
    }
    
    public function getSystemInfo() {
        $uptime = time() - $this->vehicleInfo['system_uptime'];
        
        return [
            'vehicle_info' => $this->vehicleInfo,
            'system_uptime' => $this->formatUptime($uptime),
            'memory_usage' => memory_get_usage(true),
            'sensor_status' => 'simulated_breadboard',
            'php_version' => PHP_VERSION,
            'last_data_update' => date('Y-m-d H:i:s'),
            'active_alerts' => $this->checkAlerts()
        ];
    }
    
    private function formatUptime($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
    
    private function checkAlerts() {
        $alerts = [];
        
        foreach ($this->sensorData as $position => $data) {
            if ($data['pressure'] < 28.0) {
                $alerts[] = "LOW_PRESSURE:" . $position . ":" . $data['pressure'] . "PSI";
            }
            if ($data['pressure'] > 38.0) {
                $alerts[] = "HIGH_PRESSURE:" . $position . ":" . $data['pressure'] . "PSI";
            }
            if ($data['temperature'] > 45.0) {
                $alerts[] = "HIGH_TEMP:" . $position . ":" . $data['temperature'] . "°C";
            }
            if ($data['battery'] < 85) {
                $alerts[] = "LOW_BATTERY:" . $position . ":" . $data['battery'] . "%";
            }
        }
        
        return $alerts;
    }
    
    private function getReadingsCount() {
        // Simulate increasing readings count
        static $count = 0;
        $count++;
        return $count;
    }
}

// API Handler for Breadboard Simulator
class BreadboardAPI {
    private $simulator;
    
    public function __construct() {
        $this->simulator = new BreadboardSimulator();
    }
    
    public function handleRequest() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        
        $action = $_GET['action'] ?? 'get-data';
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($action) {
                case 'get-data':
                    $response = $this->simulator->readSensorData();
                    break;
                    
                case 'system-info':
                    $response = $this->simulator->getSystemInfo();
                    break;
                    
                case 'simulate-low-pressure':
                    if ($method === 'POST') {
                        $response = ['message' => $this->simulator->simulateLowPressureAlert()];
                    } else {
                        throw new Exception('Method not allowed');
                    }
                    break;
                    
                case 'simulate-high-temp':
                    if ($method === 'POST') {
                        $response = ['message' => $this->simulator->simulateHighTemperatureAlert()];
                    } else {
                        throw new Exception('Method not allowed');
                    }
                    break;
                    
                case 'reset-sensors':
                    if ($method === 'POST') {
                        $response = ['message' => $this->simulator->resetSensors()];
                    } else {
                        throw new Exception('Method not allowed');
                    }
                    break;
                    
                case 'health':
                    $response = [
                        'status' => 'healthy',
                        'service' => 'Breadboard TPMS Simulator',
                        'timestamp' => date('c')
                    ];
                    break;
                    
                default:
                    throw new Exception('Unknown action');
            }
            
            $response['success'] = true;
            echo json_encode($response, JSON_PRETTY_PRINT);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}

// Standalone API server simulation
if (php_sapi_name() === 'cli') {
    echo "Breadboard TPMS Simulator Running...\n";
    echo "Access endpoints via:\n";
    echo "- http://localhost/your-app/breadboard_api.php?action=get-data\n";
    echo "- http://localhost/your-app/breadboard_api.php?action=system-info\n";
} else {
    // Handle web request
    $api = new BreadboardAPI();
    $api->handleRequest();
}
?>