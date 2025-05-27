<?php
session_start();
require_once('carStorage.php');
require_once('userStorage.php');
require_once('reservationStorage.php');

$user = $_SESSION['user'] ?? null;
$admin = false;

if($user && $user['email']=="admin@ikarrental.hu"){
    $admin = true;
}

$carStorage = new CarStorage();
$userStorage = new UserStorage();
$reservationStorage = new ReservationStorage();
if($admin){
    $reservations = $reservationStorage->findAll();
}else{

    $reservations = $reservationStorage->findByUserId($user['id']);
}

$_SESSION['start_date'] = null;
$_SESSION['end_date'] = null;


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

<div class="container mt-5">
    <div class="row">
        <div class="col-12 text-end">
            <small>Bejelentkezve, mint</small> <br><h2><?=$user['name']?></h2>
        </div>
        <div class="col-12">
            <h3><?=$admin ? "Foglalások" : "Foglalásaim"?></h3>
            <hr>

        <div class="row g-4">
        <?php foreach($reservations as $reservation): ?>
            <?php
                $car = $carStorage->findById($reservation['carId']);
                $resUser = $userStorage->findById($reservation['userId']);
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">  
                <div class="card profile-card h-100">
                <a href="rent.php?id=<?=$car['id']?>"><img src="<?=$car['image']?>" class="card-img-top" alt="..."></a>
                    <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><a href="rent.php?id=<?=$car['id']?>" class="link-dark text-decoration-none"><?=$car['brand']?> <br> <?=$car['model']?></a></h5>
                    <p class="card-text">Kezdés: <?=$reservation['start_date']?> <br>Befejezés: <?=$reservation['end_date']?></p>
                    <?php if ($admin): ?>
                    <p class="card-text"><?=$resUser['email']?></p>
                    <a href="deleteReservation.php?id=<?=$reservation['id']?>" class="btn btn-danger mt-auto align-self-end">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5v-7zM4.118 4a1 1 0 0 1 .876-.5h6.012a1 1 0 0 1 .876.5H13.5a.5.5 0 0 1 0 1h-11a.5.5 0 0 1 0-1h1.618zM4.5 1a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v1h-7V1z"/>
                    </svg>
                    </a>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

        </div>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>