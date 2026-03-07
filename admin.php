<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Add New Hotel logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_hotel') {
    $name = $conn->real_escape_string($_POST['name']);
    $loc = $conn->real_escape_string($_POST['location']);
    $price = $conn->real_escape_string($_POST['price']);
    $accom = $conn->real_escape_string($_POST['accommodations']);
    $desc = $conn->real_escape_string($_POST['description']);
    $image = 'assets/images/tour-3-550x590.jpg'; // static placeholder image

    $conn->query("INSERT INTO app_hotels (name, location, price, accommodations, image, description) VALUES ('$name', '$loc', '$price', '$accom', '$image', '$desc')");
    header("Location: admin.php?success=Hotel+Added+Successfully");
    exit;
}

// Toggle Hotel Availability
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_hotel') {
    $hotel_id = (int)$_POST['hotel_id'];
    $current_status = (int)$_POST['current_status'];
    $new_status = $current_status ? 0 : 1;
    $conn->query("UPDATE app_hotels SET availability=$new_status WHERE id=$hotel_id");
    header("Location: admin.php?success=Availability+Updated");
    exit;
}

// Update Hotel Dates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_dates') {
    $hotel_id = (int)$_POST['hotel_id'];
    $dates = $conn->real_escape_string($_POST['available_dates']);
    $conn->query("UPDATE app_hotels SET available_dates='$dates' WHERE id=$hotel_id");
    header("Location: admin.php?success=Calendar+Dates+Updated");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Travelo Admin Dashboard</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/fonts/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: #1a1c22;
            color: #b8c2cc;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 30px 25px;
            background-color: #121418;
            text-align: center;
            border-bottom: 1px solid #2a2c33;
        }

        .sidebar-header img {
            max-width: 140px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            flex-grow: 1;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #b8c2cc;
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar-menu a i {
            margin-right: 15px;
            font-size: 18px;
            width: 25px;
            text-align: center;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: #fff;
            background-color: #2a2c33;
        }
        
        .sidebar-menu a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: #F7921E;
        }

        .logout-wrapper {
            padding: 20px 25px;
            border-top: 1px solid #2a2c33;
        }
        
        .btn-logout {
            background-color: #e74c3c;
            color: white;
            width: 100%;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            display: block;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-logout:hover {
            background-color: #c0392b;
            color: white;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: 260px;
            padding: 40px;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: #fff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }

        .page-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 24px;
            color: #2c3e50;
        }

        .user-info {
            display: flex;
            align-items: center;
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .user-info i {
            margin-right: 8px;
            font-size: 20px;
            color: #133a25;
        }

        /* Cards */
        .data-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 40px;
            overflow: hidden;
            display: none; /* hidden by default for tab switching */
        }
        
        .data-card.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            background-color: #fff;
            padding: 20px 30px;
            border-bottom: 1px solid #edf2f6;
            display: flex;
            align-items: center;
        }

        .card-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #34495e;
            display: flex;
            align-items: center;
        }

        .card-header h4 i {
            color: #F7921E;
            margin-right: 12px;
            font-size: 20px;
            background: rgba(247, 146, 30, 0.1);
            padding: 10px;
            border-radius: 8px;
        }

        /* Table */
        .table-responsive {
            padding: 0 30px 30px;
        }
        
        .table {
            margin-top: 20px;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: #f8f9fa;
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            border: none;
            padding: 15px;
            border-bottom: 2px solid #edf2f6;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #2c3e50;
            border-bottom: 1px solid #edf2f6;
            font-size: 14px;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover td {
            background-color: #fcfdfe;
        }

        .pill {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #e1f5fe;
            color: #0288d1;
            display: inline-block;
        }

        .pill.economy { background: #e8f5e9; color: #2e7d32; }
        .pill.business { background: #fff3e0; color: #ef6c00; }
        .pill.transfer { background: #f3e5f5; color: #7b1fa2; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3 style="color:white; font-weight:700; margin:0;"><span style="color:#F7921E;">T</span>ravelo</h3>
            <span style="font-size: 12px; color: #7f8c8d;">Admin Panel</span>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="#" class="nav-link active" data-target="flights"><i class="fas fa-plane-departure"></i> Flight Bookings</a></li>
            <li><a href="#" class="nav-link" data-target="cabs"><i class="fas fa-car-side"></i> Cab Bookings</a></li>
            <li><a href="#" class="nav-link" data-target="hotels"><i class="fas fa-hotel"></i> Hotel Bookings</a></li>
            <li><a href="#" class="nav-link" data-target="manage-hotels"><i class="fas fa-building"></i> Manage Hotels</a></li>
            <li><a href="#" class="nav-link" data-target="contacts"><i class="fas fa-envelope-open-text"></i> Messages</a></li>
            <li><a href="index.html" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
        </ul>

        <div class="logout-wrapper">
            <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h2 id="page-title">Flight Bookings</h2>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
            </div>
        </div>

        <!-- Flights Card -->
        <div class="data-card active" id="flights-card">
            <div class="card-header">
                <h4><i class="fas fa-plane"></i> Recent Flight Bookings</h4>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Trip</th><th>Route</th><th>Dates</th><th>Passengers</th><th>Class</th><th>Requested</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM flights ORDER BY id DESC LIMIT 50");
                        while($row = $res->fetch_assoc()) {
                            $trip_class = ($row['trip_type'] == 'OneWay') ? 'pill' : 'pill transfer';
                            $cab_class = strtolower($row['travel_class']) == 'business' ? 'pill business' : 'pill economy';
                            echo "<tr>";
                            echo "<td><span class='{$trip_class}'>{$row['trip_type']}</span></td>";
                            echo "<td><strong>{$row['from_city']}</strong><br>to <strong>{$row['to_city']}</strong></td>";
                            echo "<td>D: {$row['depart_date']}<br>R: {$row['return_date']}</td>";
                            echo "<td>{$row['adults']}A, {$row['children']}C, {$row['infants']}I</td>";
                            echo "<td><span class='{$cab_class}'>{$row['travel_class']}</span></td>";
                            echo "<td><span style='color:#95a5a6; font-size:12px;'>".date('M j, Y g:i A', strtotime($row['booking_date']))."</span></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cabs Card -->
        <div class="data-card" id="cabs-card">
            <div class="card-header">
                <h4><i class="fas fa-car"></i> Recent Cab Bookings</h4>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th><th>Route/Details</th><th>Pickup Time</th><th>Return/Hours</th><th>Requested</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM cabs ORDER BY id DESC LIMIT 50");
                        while($row = $res->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><span class='pill'>{$row['trip_type']}</span><br><span style='font-size:12px; color:#7f8c8d;'>{$row['pickup_type']}</span></td>";
                            echo "<td><strong>{$row['from_city']}</strong><br>to <strong>{$row['to_city']}</strong></td>";
                            echo "<td>{$row['pickup_date']}<br>{$row['pickup_time']}</td>";
                            echo "<td>{$row['return_date']} {$row['return_time']}<br>{$row['hours']}</td>";
                            echo "<td><span style='color:#95a5a6; font-size:12px;'>".date('M j, Y g:i A', strtotime($row['booking_date']))."</span></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hotels Card -->
        <div class="data-card" id="hotels-card">
            <div class="card-header">
                <h4><i class="fas fa-hotel"></i> Recent Hotel Bookings</h4>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Check-in Date</th><th>Hotel</th><th>Phone</th><th>Status</th><th>Requested On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM hotels ORDER BY id DESC LIMIT 50");
                        while($row = $res->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>{$row['check_in']}</strong></td>";
                            echo "<td>{$row['hotel_search']}<br><span class='pill economy'>{$row['accommodations']}</span></td>";
                            echo "<td>{$row['phone']}</td>";
                            echo "<td>{$row['status']}</td>";
                            echo "<td><span style='color:#95a5a6; font-size:12px;'>".date('M j, Y g:i A', strtotime($row['booking_date']))."</span></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Contacts Card -->
        <div class="data-card" id="contacts-card">
            <div class="card-header">
                <h4><i class="fas fa-envelope"></i> Contact Messages</h4>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User Details</th><th>Contact Info</th><th>Message</th><th>Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 50");
                        while($row = $res->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>{$row['name']}</strong></td>";
                            echo "<td><a href='mailto:{$row['email']}'>{$row['email']}</a><br>{$row['phone']}</td>";
                            echo "<td style='max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;' title='".htmlspecialchars($row['message'])."'>{$row['message']}</td>";
                            echo "<td><span style='color:#95a5a6; font-size:12px;'>".date('M j, Y g:i A', strtotime($row['date_sent']))."</span></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Manage Hotels Card -->
        <div class="data-card" id="manage-hotels-card">
            <div class="card-header">
                <h4><i class="fas fa-building"></i> Manage Hotels Data</h4>
            </div>
            <div class="table-responsive" style="padding: 30px;">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php endif; ?>

                <h5>Add New Hotel</h5>
                <form action="admin.php" method="POST" class="mb-5 row g-3">
                    <input type="hidden" name="action" value="add_hotel">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="name" placeholder="Hotel Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="location" placeholder="Location City" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="price" placeholder="Price (INR)" required>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="accommodations" required>
                            <option value="">Accommodation</option>
                            <option value="Classic Tent">Classic Tent</option>
                            <option value="Forest Camping">Forest Camping</option>
                            <option value="Small Trailer">Small Trailer</option>
                            <option value="Tree House Tent">Tree House Tent</option>
                            <option value="Tent Camping">Tent Camping</option>
                            <option value="Couple Tent">Couple Tent</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <textarea class="form-control" name="description" placeholder="Description" rows="1" required></textarea>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100" style="background-color: #F7921E; border:none; padding:10px;">Add Hotel</button>
                    </div>
                </form>

                <hr>

                <h5 class="mt-4">Existing Hotels Database</h5>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Image</th><th>Name & Location</th><th>Details</th><th>Description</th><th>Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM app_hotels ORDER BY id DESC");
                        if ($res && $res->num_rows > 0) {
                            while($row = $res->fetch_assoc()) {
                                $avail_badge = $row['availability'] ? '<span class="badge bg-success">Available</span>' : '<span class="badge bg-danger">Hidden</span>';
                                $btn_text = $row['availability'] ? 'Hide' : 'Show';
                                $btn_class = $row['availability'] ? 'btn-outline-danger' : 'btn-outline-success';
                                
                                echo "<tr>";
                                echo "<td><img src='{$row['image']}' alt='hotel' style='border-radius:6px; max-width:60px;'></td>";
                                echo "<td><strong>{$row['name']}</strong><br><span style='font-size:12px;color:#7f8c8d;'>{$row['location']}</span></td>";
                                echo "<td><span class='pill economy'>{$row['accommodations']}</span><br><strong>₹{$row['price']}</strong></td>";
                                echo "<td style='font-size:13px; max-width:250px; line-height: 1.4;'>".htmlspecialchars($row['description'])."</td>";
                                echo "<td>
                                    {$avail_badge}
                                    <form method='POST' style='margin-top:5px;'>
                                        <input type='hidden' name='action' value='toggle_hotel'>
                                        <input type='hidden' name='hotel_id' value='{$row['id']}'>
                                        <input type='hidden' name='current_status' value='{$row['availability']}'>
                                        <button type='submit' class='btn {$btn_class} btn-sm' style='font-size:11px; padding:2px 6px;'>Toggle {$btn_text}</button>
                                    </form>
                                    <hr style='margin:10px 0;'>
                                    <form method='POST'>
                                        <input type='hidden' name='action' value='update_dates'>
                                        <input type='hidden' name='hotel_id' value='{$row['id']}'>
                                        <label style='font-size:11px; color:#7f8c8d;'>Availability Calendar:</label>
                                        <input type='text' name='available_dates' class='form-control form-control-sm flatpickr-multiple mb-1' placeholder='Pick Dates' value='{$row['available_dates']}' style='font-size:11px; background:#fff;'>
                                        <div style='font-size:10px; color:#27ae60; margin-bottom:5px; max-height:40px; overflow:hidden;'>
                                            <i class='fas fa-calendar-check'></i> Selected: " . (empty($row['available_dates']) ? 'None' : $row['available_dates']) . "
                                        </div>
                                        <button type='submit' class='btn btn-primary btn-sm w-100' style='font-size:11px; background:#F7921E; border:none;'>Save Calendar Dates</button>
                                    </form>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No hotels found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- JS for Tabs -->
    <script>
        const navLinks = document.querySelectorAll('.nav-link');
        const dataCards = document.querySelectorAll('.data-card');
        const pageTitle = document.getElementById('page-title');

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active link
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Update Page Title
                pageTitle.textContent = this.textContent.trim();

                // Hide all cards, show target
                const targetId = this.getAttribute('data-target') + '-card';
                dataCards.forEach(card => card.classList.remove('active'));
                document.getElementById(targetId).classList.add('active');
            });
        });
    </script>
</body>
</html>
