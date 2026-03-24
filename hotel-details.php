<?php
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT * FROM app_hotels WHERE id = $id AND availability = 1");
$hotel = $res->fetch_assoc();

if (!$hotel) {
    header("Location: hotel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="zxx">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?php echo $hotel['name']; ?> - Travelo</title>
        
        <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/png">
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="assets/fonts/flaticon/flaticon_gowilds.css">
        <link rel="stylesheet" href="assets/fonts/fontawesome/css/all.min.css">
        <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/vendor/magnific-popup/dist/magnific-popup.css">
        <link rel="stylesheet" href="assets/vendor/slick/slick.css">
        <link rel="stylesheet" href="assets/vendor/jquery-ui/jquery-ui.min.css">
        <link rel="stylesheet" href="assets/vendor/nice-select/css/nice-select.css">
        <link rel="stylesheet" href="assets/vendor/animate.css">
        <link rel="stylesheet" href="assets/css/default.css">
        <link rel="stylesheet" href="assets/css/style.css">
        
        <!-- Flatpickr -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/material_orange.css">

        <style>
            .hotel-hero { height: 450px; border-radius: 20px; overflow: hidden; margin-bottom: 40px; }
            .price-tag { background: #F7921E; color: white; padding: 15px 30px; border-radius: 50px; font-weight: 700; font-size: 24px; display: inline-block; }
            .calendar-card { background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
            .booking-sidebar { background: #F8F9FA; border-radius: 15px; padding: 30px; position: sticky; top: 100px; }
            .btn-book-now { background: #F7921E; color: white; border: none; padding: 18px; width: 100%; border-radius: 10px; font-weight: 700; font-size: 18px; transition: 0.3s; }
            .btn-book-now:hover { background: #e6851b; transform: translateY(-2px); color: white; }
            .hotel-desc { font-size: 16px; line-height: 1.8; color: #666; }
            #inline-calendar .flatpickr-calendar { box-shadow: none; border: none; width: 100%; }
        </style>
    </head>
    <body>
<?php include 'navbar.php'; ?>


        <section class="page-banner overlay pt-170 pb-170 bg_cover" style="background-image: url(assets/images/abt-bg.jpg);">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="page-banner-content text-center text-white">
                            <h1 class="page-title text-white"><?php echo $hotel['name']; ?></h1>
                            <ul class="breadcrumb-link text-white">
                                <li><a href="index.php">Home</a></li>
                                <li><a href="hotel.php">Hotels</a></li>
                                <li class="active"><?php echo $hotel['location']; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="hotel-details-section pt-100 pb-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="hotel-hero shadow-lg">
                            <img src="<?php echo $hotel['image']; ?>" alt="Hotel" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="badge bg-green text-white px-3 py-2 mb-2"><?php echo $hotel['accommodations']; ?></span>
                                <h2 class="fw-bold"><?php echo $hotel['name']; ?></h2>
                                <p class="text-muted"><i class="fas fa-map-marker-alt text-warning me-2"></i><?php echo $hotel['location']; ?></p>
                            </div>
                            <div class="price-tag">₹<?php echo $hotel['price']; ?> <small style="font-size:14px; font-weight:400;">/ Night</small></div>
                        </div>

                        <div class="hotel-info-tabs mt-40">
                            <h4 class="mb-3">Description</h4>
                            <p class="hotel-desc"><?php echo nl2br($hotel['description']); ?></p>
                        </div>

                        <div class="calendar-card mt-50">
                            <h4 class="mb-4"><i class="far fa-calendar-check text-warning me-2"></i>Availability Calendar</h4>
                            <p class="text-muted mb-4">Dates shown in the calendar below are available for booking at this property.</p>
                            <div id="inline-calendar"></div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="booking-sidebar">
                            <h4 class="mb-4">Book This Stay</h4>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Free Cancellation</li>
                                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Best Price Guaranteed</li>
                                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Instant Confirmation</li>
                            </ul>
                            
                            <button class="btn-book-now" data-bs-toggle="modal" data-bs-target="#bookingModal">
                                Book Now <i class="far fa-paper-plane ms-2"></i>
                            </button>
                            
                            <div class="contact-info mt-4 pt-4 border-top">
                                <p class="mb-1 text-muted">Need help?</p>
                                <h6 class="fw-bold"><i class="fas fa-phone-alt text-warning me-2"></i> +91-9910516644</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0" style="border-radius: 15px;">
                    <div class="modal-header bg-green text-white p-4">
                        <h5 class="modal-title fw-bold text-white">Send Booking Inquiry</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="bookingQueryForm">
                            <input type="hidden" name="form_type" value="hotel">
                            <input type="hidden" name="booking_type" value="Booking">
                            <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                            <input type="hidden" name="search" value="<?php echo $hotel['name']; ?>">
                            <input type="hidden" name="accommodations" value="<?php echo $hotel['accommodations']; ?>">

                            <div class="form_group mb-3">
                                <label class="fw-bold small mb-1">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="form_group mb-3">
                                <label class="fw-bold small mb-1">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                            </div>
                            <div class="form_group mb-3">
                                <label class="fw-bold small mb-1">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+91" required>
                            </div>
                            <div class="form_group mb-4">
                                <label class="fw-bold small mb-1">Check-in Date</label>
                                <input type="text" name="check_in" id="modalDatePicker" class="form-control" placeholder="Select Date" required>
                            </div>

                            <button type="submit" class="btn-book-now py-3">Send Inquiry Now</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<?php include 'footer.php'; ?>

        
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                const availableDates = "<?php echo $hotel['available_dates']; ?>".split(', ');

                flatpickr("#inline-calendar", {
                    inline: true,
                    enable: availableDates,
                    dateFormat: "Y-m-d",
                });

                flatpickr("#modalDatePicker", {
                    enable: availableDates,
                    dateFormat: "Y-m-d",
                });

                $('#bookingQueryForm').on('submit', function(e) {
                    e.preventDefault();
                    const btn = $(this).find('button[type="submit"]');
                    btn.prop('disabled', true).text('Sending...');

                    const formData = new FormData(this);
                    
                    fetch('submit.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'Query Sent!', text: 'Our team will contact you shortly.', confirmButtonColor: '#F7921E' });
                            $('#bookingModal').modal('hide');
                            this.reset();
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                        }
                    })
                    .catch(() => {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
                    })
                    .finally(() => {
                        btn.prop('disabled', false).text('Send Inquiry Now');
                    });
                });
            });
        </script>
    </body>
</html>
