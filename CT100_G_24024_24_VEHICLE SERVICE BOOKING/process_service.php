<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: service.html');
    exit;
}

function clean($v) {
    return htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$customerName = clean($_POST['customerName'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$email = clean($_POST['email'] ?? '');
$regNumber = strtoupper(clean($_POST['regNumber'] ?? ''));
$vehicleType = clean($_POST['vehicleType'] ?? '');
$vehicleModel = clean($_POST['vehicleModel'] ?? '');
$yearOfManufacture = clean($_POST['yearOfManufacture'] ?? '');
$serviceDate = clean($_POST['serviceDate'] ?? '');
$services = $_POST['services'] ?? [];
$clientCost = (int)($_POST['estimatedCost'] ?? 0);

$priceList = [
    'Engine Oil Change' => 3000,
    'Brake Inspection' => 2000,
    'Wheel Alignment' => 2500,
    'Computer Diagnosis' => 3500,
    'Full Service' => 8000,
    'Tyre Balancing' => 2000
];

$allowed = ['Saloon Car', 'SUV', 'Pickup', 'Van'];
$errors = [];

if (strlen($customerName) < 3) {
    $errors[] = 'Customer name is required.';
}
if ($phone === '' || !preg_match('/^\d{10}$/', $phone)) {
    $errors[] = 'Phone must contain exactly 10 digits.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email is required.';
}
if ($regNumber === '') {
    $errors[] = 'Registration number is required.';
}
if (!in_array($vehicleType, $allowed, true)) {
    $errors[] = 'Select a valid vehicle type.';
}
if ($vehicleModel === '') {
    $errors[] = 'Vehicle model is required.';
}

$year = (int)$yearOfManufacture;
$cy = (int)date('Y');
if ($year < 1980 || $year > $cy) {
    $errors[] = 'Year of manufacture must be between 1980 and ' . $cy . '.';
}
if ($serviceDate === '') {
    $errors[] = 'Preferred service date is required.';
}
if (empty($services) || !is_array($services)) {
    $errors[] = 'Select at least one service.';
}

$baseTotal = 0;
$valid = [];
foreach ($services as $s) {
    $s = clean($s);
    if (isset($priceList[$s])) {
        $baseTotal += $priceList[$s];
        $valid[] = ['name' => $s, 'price' => $priceList[$s]];
    }
}
$estimatedCost = $baseTotal;

$formattedDate = '';
if ($serviceDate !== '') {
    $dt = DateTime::createFromFormat('Y-m-d', $serviceDate);
    $formattedDate = $dt ? $dt->format('d F Y') : $serviceDate;
}

function ksh($n) {
    return 'KSh ' . number_format($n, 0, '.', ',');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - AutoCare Garage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header>
        <div class="topbar">
            <div class="container topbar-inner">
                <div class="brand">
                    <div class="logo-mark">
                        <span class="piston left"></span>
                        <span class="piston right"></span>
                        <span class="car-line"></span>
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">AUTOCARE</span>
                        <span class="brand-sub">GARAGE</span>
                    </div>
                </div>
                <nav>
                    <a href="index.html">Home</a>
                    <a href="service.html">Book Service</a>
                    <a href="index.html#services">Our Services</a>
                    <a href="index.html#contact">Contact</a>
                </nav>
                <div class="nav-search">
                    <input type="text" placeholder="Search services..." aria-label="Search services">
                    <button type="button" aria-label="Search">&#128269;</button>
                </div>
            </div>
        </div>
    </header>

    <section class="result-wrap">
        <div class="container">
            <?php if (!empty($errors)): ?>
                <div class="result-box">
                    <div class="result-head error">
                        <h1>Submission Error</h1>
                    </div>
                    <div class="result-body">
                        <p>Please correct the following:</p>
                        <ul style="margin:12px 0 16px 18px; color:#b91c1c;">
                            <?php foreach ($errors as $e): ?>
                                <li><?php echo $e; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="result-actions">
                        <a href="service.html" class="btn btn-main">Back to Form</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="result-box">
                    <div class="result-head">
                        <h1>SERVICE REQUEST RECEIVED</h1>
                    </div>
                    <div class="result-body">
                        <h2>Customer</h2>
                        <table class="info-table">
                            <tr><td>Customer</td><td><?php echo $customerName; ?></td></tr>
                            <tr><td>Phone</td><td><?php echo $phone; ?></td></tr>
                            <tr><td>Email</td><td><?php echo $email; ?></td></tr>
                        </table>

                        <h2>Vehicle</h2>
                        <table class="info-table">
                            <tr><td>Vehicle</td><td><?php echo $regNumber; ?></td></tr>
                            <tr><td>Vehicle Type</td><td><?php echo $vehicleType; ?></td></tr>
                            <tr><td>Model</td><td><?php echo $vehicleModel; ?></td></tr>
                            <tr><td>Year</td><td><?php echo $yearOfManufacture; ?></td></tr>
                            <tr><td>Service Date</td><td><?php echo $formattedDate; ?></td></tr>
                        </table>

                        <h2>Selected Services</h2>
                        <ul class="svc-list">
                            <?php foreach ($valid as $v): ?>
                                <li>
                                    <span><?php echo $v['name']; ?></span>
                                    <span><?php echo ksh($v['price']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="total-line">
                            <span>ESTIMATED SERVICE COST</span>
                            <span><?php echo ksh($estimatedCost); ?></span>
                        </div>

                        <p style="margin-top:14px; font-size:0.88rem; color:#666;">
                            We will contact you on <?php echo $phone; ?> to confirm the appointment for <?php echo $formattedDate; ?>.
                            Location: Kutus, Kirinyaga.
                        </p>
                    </div>
                    <div class="result-actions">
                        <a href="service.html" class="btn btn-ghost">New Booking</a>
                        <a href="index.html" class="btn btn-main">Home</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>© 2026 AutoCare Garage. Kutus, Kirinyaga.</p>
        </div>
    </footer>

</body>
</html>
