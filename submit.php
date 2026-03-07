<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['form_type'];
    $response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
    
    if ($type == "contact") {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['number']);
        $website = $conn->real_escape_string($_POST['website']);
        $message = $conn->real_escape_string($_POST['message']);
        
        $sql = "INSERT INTO contact_messages (name, email, phone, website, message) VALUES ('$name', '$email', '$phone', '$website', '$message')";
        if ($conn->query($sql) === TRUE) {
            $response = ['status' => 'success', 'message' => 'Message Sent successfully!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Error: ' . $conn->error];
        }
    }
    else if ($type == "flight") {
        $trip_type = $conn->real_escape_string($_POST['tripType'] ?? '');
        $from = $conn->real_escape_string($_POST['from'] ?? '');
        $to = $conn->real_escape_string($_POST['to'] ?? '');
        $depart_date = $conn->real_escape_string($_POST['depart_date'] ?? '');
        $return_date = $conn->real_escape_string($_POST['return_date'] ?? '');
        $adults = intval($_POST['adults'] ?? 1);
        $children = intval($_POST['children'] ?? 0);
        $infants = intval($_POST['infants'] ?? 0);
        $tclass = $conn->real_escape_string($_POST['travel_class'] ?? 'Economy');
        
        if ($from === $to && $from !== '') {
            $response = ['status' => 'error', 'message' => 'Origin and destination cannot be the same.'];
        } else {
            $sql = "INSERT INTO flights (trip_type, from_city, to_city, depart_date, return_date, adults, children, infants, travel_class) VALUES ('$trip_type', '$from', '$to', '$depart_date', '$return_date', $adults, $children, $infants, '$tclass')";
            if ($conn->query($sql) === TRUE) {
                $response = ['status' => 'success', 'message' => 'Flight Booking Request Sent!'];
            } else {
                $response = ['status' => 'error', 'message' => 'Error: ' . $conn->error];
            }
        }
    }
    else if ($type == "hotel") {
        $check_in = $conn->real_escape_string($_POST['check_in'] ?? '');
        $search = $conn->real_escape_string($_POST['search'] ?? '');
        $accom = $conn->real_escape_string($_POST['accommodations'] ?? '');
        
        $sql = "INSERT INTO hotels (check_in, hotel_search, accommodations) VALUES ('$check_in', '$search', '$accom')";
        if ($conn->query($sql) === TRUE) {
            $response = ['status' => 'success', 'message' => 'Hotel Booking Request Sent!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Error: ' . $conn->error];
        }
    }
    else if ($type == "cab") {
        $trip_type = $conn->real_escape_string($_POST['tripType'] ?? '');
        $pickup = $conn->real_escape_string($_POST['pickup'] ?? '');
        $from = $conn->real_escape_string($_POST['from'] ?? '');
        $to = $conn->real_escape_string($_POST['to'] ?? '');
        $pickup_date = $conn->real_escape_string($_POST['pickup_date'] ?? '');
        $pickup_time = $conn->real_escape_string($_POST['pickup_time'] ?? '');
        $return_date = $conn->real_escape_string($_POST['return_date'] ?? '');
        $return_time = $conn->real_escape_string($_POST['return_time'] ?? '');
        $hours = $conn->real_escape_string($_POST['hours'] ?? '');
        
        if ($from === $to && $from !== '' && $trip_type !== 'Hourly') {
            $response = ['status' => 'error', 'message' => 'Pickup and drop cities cannot be the same.'];
        } else {
            $sql = "INSERT INTO cabs (trip_type, pickup_type, from_city, to_city, pickup_date, pickup_time, return_date, return_time, hours) VALUES ('$trip_type', '$pickup', '$from', '$to', '$pickup_date', '$pickup_time', '$return_date', '$return_time', '$hours')";
            if ($conn->query($sql) === TRUE) {
                $response = ['status' => 'success', 'message' => 'Cab Booking Request Sent!'];
            } else {
                $response = ['status' => 'error', 'message' => 'Error: ' . $conn->error];
            }
        }
    }

    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
