<?php
session_start();
include_once('carStorage.php');
include_once('reservationStorage.php');
$cars = [];

$user = $_SESSION['user'] ?? null;
$admin = false;

if($user && $user['email']=="admin@ikarrental.hu"){
    $admin = true;
}

$errors = [];
$passengers = $_GET['passengers'] ?? '';
$transmission = $_GET['transmission'] ?? '';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';
$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';
if($_GET){
    if($passengers != ''){
        if(!filter_var($passengers, FILTER_VALIDATE_INT)){
            $errors['passengers'] = "A szükséges férőhelyeket egész számként kell megadni!";
            
        }
        else if($passengers < 1 or $passengers > 10){
            $errors['passengers'] = "A szükséges férőhelyek száma 1 és 10 között lehet";
        }
    }
    if($transmission != ''){
        if($transmission != "Manual" and $transmission != "Automatic"){
            $errors['transmission'] = "A váltó típusa csak manuális vagy automata lehet";
        }
    }
    if($price_min != ''){
        if(!filter_var($price_min, FILTER_VALIDATE_INT)){
            $errors['price_min'] = "A minimum árat egész számként kell megadni!";
            
        }
        else if($price_min < 0 ){
            $errors['price_min'] = "A minimum ár nem lehet 0-nál kisebb";
        }
    }
    if ($price_max != '') {
        if(!filter_var($price_max, FILTER_VALIDATE_INT)){
            $errors['price_max'] = "A maximum árat egész számként kell megadni!";
            
        }
        else if ($price_max < 0) {
            $errors['price_max'] = "A maximum ár nem lehet 0-nál kisebb";
        }
    }
    if ($date_start != '') {
        if (!DateTime::createFromFormat('Y-m-d', $date_start)) {
            $errors['date_start'] = "Érvénytelen kezdési dátum";
        }
    }
    if ($date_end != '') {
        if (!DateTime::createFromFormat('Y-m-d', $date_end)) {
            $errors['date_end'] = "Érvénytelen befejezési dátum";
        }
    }
    if ($date_start != '' && $date_end != '') {
        $start = DateTime::createFromFormat('Y-m-d', $date_start);
        $end = DateTime::createFromFormat('Y-m-d', $date_end);
        if ($start && $end && $start > $end) {
            $errors['date_range'] = "A kezdési dátum nem lehet későbbi, mint a befejezési dátum";
        }else{
            $_SESSION['start_date'] = $date_start;
            $_SESSION['end_date'] = $date_end;
        }
    }
}

$carStorage = new CarStorage();
$cars = $carStorage->filter($passengers, $transmission, $price_min, $price_max);
$reservationStorage = new ReservationStorage();
$cars = $reservationStorage->filterFreeCars($cars, $date_start, $date_end);



?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKarRental - Főoldal</title>
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

<div id="mainpage" class="container-fluid text-center" style="max-width: 1600px;">

  <div class="row">
   
    <div class="col-12 col-md-6 d-flex flex-column justify-content-center align-items-center">
      <h1>Kölcsönözz autókat könnyedén!</h1>
      <?php if($admin):?>
        <p><a href="add.php" class="link-success link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Új autó hozzáadása...</a></p>
        <?php endif ?>
    </div>

    <div class="col-12 col-md-6 d-flex justify-content-center" style="max-width: 600px;">
        <form action="index.php" method="get" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="passengers" class="form-label">Szükséges férőhelyek</label>
                    <input type="number" class="form-control" id="passengers" name="passengers" min="1" max="10" value="<?= $passengers ?>" novalidate>
                    <span><?=$errors['passengers'] ?? "" ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="transmission" class="form-label">Váltó típusa</label>
                    <select class="form-select" id="transmission" name="transmission" novalidate>
                        <option value="" <?= $transmission == '' ? 'selected' : '' ?>>Mindegy</option>
                        <option value="Manual" <?= $transmission == 'Manual' ? 'selected' : '' ?>>Manuális</option>
                        <option value="Automatic" <?= $transmission == 'Automatic' ? 'selected' : '' ?>>Automata</option>
                    </select>
                    <span><?=$errors['transmission'] ?? "" ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="price_min" class="form-label">Ár (minimum)</label>
                    <input type="number" class="form-control" id="price_min" name="price_min" novalidate value="<?= $price_min ?>">
                    <span><?=$errors['price_min'] ?? "" ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="price_max" class="form-label">Ár (maximum)</label>
                    <input type="number" class="form-control" id="price_max" name="price_max" novalidate value="<?= $price_max ?>">
                    <span><?=$errors['price_max'] ?? "" ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="date_start" class="form-label">Dátum (kezdés)</label>
                    <input type="date" class="form-control" id="date_start" name="date_start" novalidate value="<?= $date_start ?>">
                    <span><?=$errors['date_start'] ?? "" ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="date_end" class="form-label">Dátum (befejezés)</label>
                    <input type="date" class="form-control" id="date_end" name="date_end" novalidate value="<?= $date_end ?>">
                    <span><?=$errors['date_end'] ?? "" ?></span>
                </div>
            </div>
            <span><?=$errors['date_range'] ?? "" ?></span>
            <button type="submit" class="btn btn-outline-secondary">Szűrés</button>
        </form>
    </div>
    </div>

<div class="row">
    <?php foreach($cars as $car): ?>
        <div class="col-12 col-sm-6 col-md-4 col-xl-3">  
            <div class="card listed">
                <a href="rent.php?id=<?=$car['id']?>"><img src="<?=$car['image']?>" class="card-img-top" alt="..."></a>
                <div class="card-body">
                    <h5 class="card-title"><a href="rent.php?id=<?=$car['id']?>" class="link-dark text-decoration-none"><?=$car['brand']?> <br> <?=$car['model']?></a></h5>
                    <p class="card-text"><?=$car['passengers']?> férőhely - <?= $car['transmission'] === 'Manual' ? 'manuális' : 'automata'?> <br> <strong><?=$car['daily_price_huf']?> Ft/nap</strong></p>
                    <?php if($admin): ?>
                        <a href="deleteCar.php?id=<?=$car['id']?>" class="btn btn-outline-danger me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5v-7zM4.118 4a1 1 0 0 1 .876-.5h6.012a1 1 0 0 1 .876.5H13.5a.5.5 0 0 1 0 1h-11a.5.5 0 0 1 0-1h1.618zM4.5 3a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1H4.5z"/>
                            </svg>
                        </a>
                        <a href="modifyCar.php?id=<?=$car['id']?>" class="btn btn-outline-primary me-2">Módosítás</a>
                    <?php else: ?>
                        <a href="rent.php?id=<?=$car['id']?>" class="btn btn-outline-success me-2">Foglalás</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($cars)): ?>
        <div class="col-12">
            <span><p class="text-center">Nincs elérhető autó a megadott feltételek alapján. 😞</p></span>
        </div>
    <?php endif; ?>
</div>
  </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>