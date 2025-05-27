<?php
session_start();
include('reservationStorage.php');

$user = $_SESSION['user'] ?? null;
$admin = false;

if($user && $user['email']=="admin@ikarrental.hu"){
    $admin = true;
}

$car = $_SESSION['car'] ?? null;
$start_date = $_SESSION['start_date'] ?? '';
$end_date = $_SESSION['end_date'] ?? '';

$reservationStorage = new ReservationStorage();
$succes = $reservationStorage->free($car, $start_date, $end_date);
$id = uniqid();
$reservation = [
    'id' => $id,
    'userId' => $user['id'],
    'carId' => $car['id'],
    'start_date' => $start_date,
    'end_date' => $end_date,
];

if($succes && $start_date != '' && $end_date != ''){
    $reservationStorage->add($reservation);    
}else{
    $succes = $reservationStorage->same($reservation);
}


$days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
$fullprice = $days * $car['daily_price_huf'];



?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKarRental - Foglalás</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

</head>
<body>
<nav class="navbar bg-body-tertiary fixed-top">
    <form class="container-fluid justify-content-start">
        
        <?php if (!$user ): ?>
            <a href="reg.php" class="btn btn-outline-success me-2" role="button">Regisztráció</a>
            <a href="login.php" class="btn btn-sm btn-outline-secondary" role="button">Bejelentkezés</a>
            <a href="index.php" class="link-secondary link-underline-opacity-0">IKarRental</a>
        <?php else: ?>
            <a href="logout.php" class="btn btn-outline-danger me-2" role="button">Kijelentkezés</a>
            <a href="index.php" class="link-secondary link-underline-opacity-0">IKarRental</a>
            <div class="ms-auto d-flex align-items-center">
                <a href="profile.php" class="link-secondary link-underline-opacity-0 d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                    <h5 class="d-inline ms-2 mb-0"><?=$admin ? "Adminisztrátor" : $user['name'] ?></h5>
                </a>
            </div>    
        <?php endif; ?>
    </form>
</nav>

<?php if (!$succes): ?>
    <div class="container mt-5" style="max-width: 800px;">
        <div class="alert alert-danger" role="alert">
            <h2>Sikertelen foglalás!</h2>
            <p>A(z) <strong><?=$car['brand']?> <?=$car['model']?></strong> nem érhető el a megadott <?=$start_date?> <strong>-</strong> <?=$end_date?> intervallumban.</p>
            <p>Próbálj megadni egy másik intervallumot, vagy keress egy másik járművet.</p>
        </div>
        <a href="rent.php" class="btn btn-outline-success me-2">Vissza a jármű oldalára</a>
    </div>
<?php else: ?>
    <div class="container mt-5" style="max-width: 800px;">
        <div class="alert alert-success" role="alert">
            <h2>Sikeres foglalás!</h2>
            <p>A(z) <strong><?=$car['brand']?> <?=$car['model']?></strong> sikeresen lefoglalva a megadott <?=$start_date?> <strong>-</strong> <?=$end_date?> intervallumra.</p>
            <p>A foglalás teljes ára <strong><?=$fullprice?> Ft</strong></p>
            <p>Foglalásod státuszát a profiloldaladon követheted nyomon.</p>
        </div>
        <a href="profile.php" class="btn btn-outline-success me-2">Profilom</a>
    </div>
<?php endif; ?>
    




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>