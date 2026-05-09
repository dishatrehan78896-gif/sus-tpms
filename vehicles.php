<?php
require_once 'config.php';
requireLogin();
$user_id = $_SESSION['user_id'];
$vehicles = [];
$current_vehicle = null;
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $vehicles[] = $row;
}
$stmt->close();
if (!empty($vehicles)) {
    $current_vehicle = $vehicles[0];
    $stmt = $conn->prepare("SELECT * FROM tire_data WHERE user_id = ? AND vehicle_id = ? ORDER BY timestamp DESC LIMIT 1");
    $stmt->bind_param("ii", $user_id, $current_vehicle['id']);
    $stmt->execute();
    $tire_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Vehicles - SUS Premium TPMS</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .vehicles-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            align-items: start;
        }
        
        .sidebar {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 140px;
        }
        
        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .vehicle-card {
            transition: all 0.3s ease;
            cursor: pointer;
            height: fit-content;
        }
        
        .vehicle-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .vehicle-avatar {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--neon-blue), #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            transition: all 0.3s ease;
        }
        
        .vehicle-card:hover .vehicle-avatar {
            transform: scale(1.1) rotate(5deg);
        }
        
        .vehicle-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stat-item {
            text-align: center;
            padding: 0.5rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.02);
            transition: all 0.3s ease;
        }
        
        .vehicle-card:hover .stat-item {
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-2px);
        }
        
        .stat-value {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-primary);
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .add-vehicle-card {
            border: 2px dashed rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.02);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 250px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .add-vehicle-card:hover {
            border-color: var(--neon-blue);
            background: rgba(59, 130, 246, 0.08);
            transform: scale(1.02);
        }
        
        .quick-stats {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .quick-stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .quick-stat-item:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(5px);
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: var(--black-secondary);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2.5rem;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }
        .vehicle-actions {
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .vehicle-card:hover .vehicle-actions {
            opacity: 1;
        }
        @media (min-width: 1024px) {
            .vehicle-card {
                position: relative;
                overflow: hidden;
            }
            
            .vehicle-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
                transition: left 0.6s ease;
            }
            
            .vehicle-card:hover::before {
                left: 100%;
            }
        }
        @media (max-width: 1024px) {
            .vehicles-container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                position: static;
                order: 2;
            }
            
            .vehicles-grid {
                grid-template-columns: 1fr;
                order: 1;
            }
            
            .vehicle-actions {
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .vehicles-grid {
                grid-template-columns: 1fr;
            }
            
            .vehicle-stats {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .modal-content {
                padding: 1.5rem;
                margin: 1rem;
            }
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        .form-full-width {
            grid-column: 1 / -1;
        }
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .status-optimal {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .status-warning {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
    </style>
</head>
<body>
    <header class="modern-header">
        <div class="container">
            <div class="header-content">
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
                        <li><a href="vehicles.php" class="active">Vehicles</a></li>
                        <li><a href="tire-health.php">Tire Health</a></li>
                        <li><a href="reports.php">Reports</a></li>
                    </ul>
                </nav>
                
                <div class="auth-buttons">
                    <span style="color: var(--text-secondary); font-weight: 500; margin-right: 15px;">
                        <span class="pulse-dot"></span>Welcome, <?php echo $_SESSION['username']; ?>
                    </span>
                    <a href="logout.php" class="modern-btn" style="background: linear-gradient(135deg, var(--danger), #dc2626);">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <div id="addVehicleModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-plus" style="color: var(--neon-blue);"></i>Add New Vehicle
                </h3>
                <button onclick="hideAddVehicleModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.2rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addVehicleForm" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="form-grid">
                    <div>
                        <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 8px;">Make *</label>
                        <input type="text" name="make" class="form-input" placeholder="e.g., Toyota" required style="width: 100%;">
                    </div>
                    <div>
                        <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 8px;">Model *</label>
                        <input type="text" name="model" class="form-input" placeholder="e.g., Camry" required style="width: 100%;">
                    </div>
                    <div>
                        <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 8px;">Year *</label>
                        <input type="number" name="year" class="form-input" placeholder="2023" min="1990" max="2030" required style="width: 100%;">
                    </div>
                    <div>
                        <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 8px;">License Plate *</label>
                        <input type="text" name="license_plate" class="form-input" placeholder="ABC-1234" required style="width: 100%;">
                    </div>
                </div>
                
                <div class="form-full-width">
                    <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 8px;">Vehicle Type *</label>
                    <select name="type" class="form-input" required style="width: 100%;">
                        <option value="car">Car</option>
                        <option value="suv">SUV</option>
                        <option value="truck">Truck</option>
                        <option value="motorcycle">Motorcycle</option>
                    </select>
                </div>
                
                <div class="form-full-width">
                    <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 8px;">VIN (Optional)</label>
                    <input type="text" name="vin" class="form-input" placeholder="Vehicle Identification Number" style="width: 100%;">
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="button" class="modern-btn" style="flex: 1; background: linear-gradient(135deg, #6b7280, #4b5563);" onclick="hideAddVehicleModal()">Cancel</button>
                    <button type="submit" class="modern-btn" style="flex: 1;">
                        <i class="fas fa-plus" style="margin-right: 8px;"></i>Add Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddVehicleModal() {
            document.getElementById('addVehicleModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function hideAddVehicleModal() {
            document.getElementById('addVehicleModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function selectVehicle(vehicleId) {
            window.location.href = `dashboard.php?vehicle=${vehicleId}`;
        }
        
        function viewVehicleDetails(vehicleId) {
            window.location.href = `tire-health.php?vehicle=${vehicleId}`;
        }
        
        function editVehicle(vehicleId) {
            alert('Edit vehicle: ' + vehicleId);
        }
        
        function deleteVehicle(vehicleId) {
            if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
                alert('Vehicle ' + vehicleId + ' would be deleted');
            }
        }
        document.getElementById('addVehicleModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideAddVehicleModal();
            }
        });
        document.getElementById('addVehicleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Vehicle added successfully!');
            hideAddVehicleModal();
            setTimeout(() => location.reload(), 1000);
        });
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                showAddVehicleModal();
            }
        });
        document.querySelectorAll('.vehicle-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.zIndex = '10';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.zIndex = '1';
            });
        });
    </script>
</body>
</html>
