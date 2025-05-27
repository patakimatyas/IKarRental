<?php
session_start();
include('carStorage.php');

$user = $_SESSION['user'] ?? null;
$admin = false;

if($user && $user['email']=="admin@ikarrental.hu"){
    $admin = true;
}

$carStorage = new CarStorage();
if($_GET){
    $car = $carStorage->findById($_GET['id']);
}else if($_SESSION){
    $car = $_SESSION['car'];
}

$start_date = $_SESSION['start_date'] ?? '';
$end_date = $_SESSION['end_date'] ?? '';

$_SESSION['car'] = $car;
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

<div class="container text-center d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="row">
        <div class="col-12 col-lg-6"><img id="rentImg"src="<?=$car['image']?>" alt=""></div>
        <div class="col-12 col-lg-6">
            <div class="row">
                <div class="col-12">
                    <div class="card infocard">
                        <div class="card-body">
                            <h2 class="card-title"><?=$car['brand']?> <strong><?=$car['model']?></strong></h2>
                            <p class="card-text"><strong>Üzemanyag:</strong> <?=$car['fuel_type']?></p>
                            <p class="card-text"><strong>Gyártási év:</strong> <?=$car['year']?></p>
                            <p class="card-text"><strong>Váltó:</strong> <?=$car['transmission']?></p>
                            <p class="card-text"><strong>Férőhelyek száma:</strong> <?=$car['passengers']?></p>
                            <p class="card-text"><strong> <?=$car['daily_price_huf']?> </strong>Ft<small>/nap</small></p>
                        </div>
                    </div>
                </div>
                <?php if (!$admin): ?>
                    <div class="col-6">
                        <a href="datepicker.php" class="btn btn-outline-secondary" role="button">Dátum <?=($start_date && $end_date) ? "módosítása" : "kiválasztása"?></a>
                        <?php if ($start_date && $end_date): ?>
                            <p class="mb-0">Bérlés kezdete: <?=$start_date?></p>
                            <p class="mb-0">Bérlés vége: <?=$end_date?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <a href="<?=$user ? "confirm.php" : "login.php"?>" class="btn btn-outline-success me-2 <?=($start_date && $end_date) ? '' : 'disabled'?>" role="button">Lefoglalom</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>