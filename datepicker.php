<?php
session_start();
include('carStorage.php');

$user = $_SESSION['user'] ?? null;
$admin = false;

if($user && $user['email']=="admin@ikarrental.hu"){
    $admin = true;
}

if($_GET){
    
    $carStorage = new CarStorage();
    $car = $carStorage->findById($_GET['id']);
}

$errors = [];
if($_POST){
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    if ($start_date != '') {
        if (!DateTime::createFromFormat('Y-m-d', $start_date)) {
            $errors['start_date'] = "Érvénytelen kezdési dátum";
        }
    }
    if ($end_date != '') {
        if (!DateTime::createFromFormat('Y-m-d', $end_date)) {
            $errors['end_date'] = "Érvénytelen befejezési dátum";
        }
    }
    if ($start_date != '' && $end_date != '') {
        $start = DateTime::createFromFormat('Y-m-d', $start_date);
        $end = DateTime::createFromFormat('Y-m-d', $end_date);
        if ($start && $end && $start > $end) {
            $errors['date_range'] = "A kezdési dátum nem lehet későbbi, mint a befejezési dátum";
        }
    }
    if(count($errors)==0){
        $_SESSION['start_date'] = $start_date;
        $_SESSION['end_date'] = $end_date;
        header('Location: rent.php');
    }
}else{
    $start_date = $_SESSION['start_date'] ?? '';
    $end_date = $_SESSION['end_date'] ?? '';
}




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

<div class="container mt-5" style="max-width: 600px;">
    <h2>Dátum kiválasztása</h2>
    <form action="" method="post">
        <div class="mb-3">
            <label for="start_date" class="form-label">Kezdő dátum</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $start_date ?>" novalidate>
            <span><?=$errors['start_date'] ?? ''?></span>
        </div>
        
        <div class="mb-3">
            <label for="end_date" class="form-label">Befejező dátum</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $end_date ?>" novalidate>
            <span><?=$errors['end_date'] ?? ''?></span>
            <span><?=$errors['date_range'] ?? ''?></span>
        </div>
        <input type="hidden" name="car_id">
        <button type="submit" class="btn btn-outline-secondary">Kész</button>
    </form>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>