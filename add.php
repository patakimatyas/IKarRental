<?php
session_start();
include('carStorage.php');

$user = $_SESSION['user'] ?? null;
$admin = false;

if($user && $user['email']=="admin@ikarrental.hu"){
    $admin = true;
}
    
$year = trim($_POST['year'] ?? '');
$brand = trim($_POST['brand'] ?? '');
$passengers = trim($_POST['passengers'] ?? '');
$model = trim($_POST['model'] ?? '');
$image = trim($_POST['image'] ?? '');
$price = trim($_POST['price'] ?? '');
$fuel = trim($_POST['fuel'] ?? '');
$transmission = trim($_POST['transmission'] ?? '');
    
   
$errors = [];
if($_POST){
    if($brand == ''){
        $errors['brand'] = "A márka megadása kötelező!";
    }
    if($year == ''){
        $errors['year'] = "A gyártási év megadása kötelező!";
    }else if(!filter_var($year, FILTER_VALIDATE_INT)){
        $errors['year'] = "A gyártási évet egész számként kell megadni!";
        
    }else if($year < 1950 and $year > 2025){
        
        $errors['year'] = "Az év 1950 és 2025 között lehet!";
    }
    
    if($model == ''){
        
        $errors['model'] = "A modell megadása kötelező!";
    }
    
    if($passengers == ''){
        
        $errors['passengers'] = "A férőhely megadása kötelező!";
    }else if(!filter_var($passengers, FILTER_VALIDATE_INT)){
        $errors['passengers'] = "A szükséges férőhelyeket egész számként kell megadni!";
        
    }else if($passengers < 1 or $passengers > 10 ){
        
        $errors['passengers'] = "A férőhely 1 és 10 között lehet!";
    }
    
    if($price == ''){
        
        $errors['price'] = "A napi ár megadása kötelező!";
    }if(!filter_var($price, FILTER_VALIDATE_INT)){
        $errors['price'] = "Az árat egész számként kell megadni!";
        
    }else if($price < 0){
        
        $errors['price'] = "A napi ár nem lehet kisebb mint 0!";
    }
    
    if($transmission == ''){
        
        $errors['transmission'] = "A váltó típusának megadása kötelező!";
    }else if($transmission != 'Manual' and $transmission != 'Automatic'){
        
        $errors['transmission'] = "A váltó típusa csak manuális vagy automata lehet!";
    }
    
    if($fuel == ''){
        $errors['fuel'] = "Az üzemanyag típusának megadása kötelező!";
    } else if($fuel != 'Petrol' and $fuel != 'Diesel' and $fuel != 'Electric'){
        $errors['fuel'] = "Az üzemanyag típusa csak benzin, dízel vagy elektromos lehet!";
    }
    
    if($image == ''){
        
        $errors['image'] = "A kép megadása kötelező!";
    }
    if (count($errors) === 0) {
        $carStorage = new CarStorage();
        $id = uniqid();
        $carStorage->add([
            'id' => $id,
            'brand' => $brand,
            'model' => $model,
            'year' => $year,
            'transmission' => $transmission,
            'fuel_type' => $fuel,
            'passengers' => $passengers,
            'daily_price_huf' => $price,
            'image' => $image,
        ]);
        header('Location: index.php');
        exit();
    }
}
    
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKarRental - Autó hozzáadása</title>
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
        <h2>Autó hozzáadása</h2>
        <form action="add.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
            <label for="brand" class="form-label">Márka</label>
            <input type="text" class="form-control" id="brand" name="brand" value="<?= $brand ?>" novalidate>
            </div>
            <span><?=$errors['brand'] ?? "" ?></span>
            <div class="mb-3">
            <label for="model" class="form-label">Modell</label>
            <input type="text" class="form-control" id="model" name="model" value="<?= $model ?>" novalidate>
            </div>
            <span><?=$errors['model'] ?? "" ?></span>
            <div class="mb-3">
            <label for="year" class="form-label">Év</label>
            <input type="number" class="form-control" id="year" name="year" value="<?= $year ?>" novalidate>
            </div>
            <span><?=$errors['year'] ?? "" ?></span>
            <div class="mb-3">
            <label for="price" class="form-label">Napi ár</label>
            <input type="number" class="form-control" id="price" name="price" value="<?= $price ?>" novalidate>
            </div>
            <span><?=$errors['price'] ?? "" ?></span>
            <div class="mb-3">
            <label for="passengers" class="form-label">Férőhely</label>
            <input type="number" class="form-control" id="passengers" name="passengers" value="<?= $passengers ?>" novalidate>
            </div>
            <span><?=$errors['passengers'] ?? "" ?></span>
            <div class="mb-3">
            <label for="transmission" class="form-label">Váltó</label>
            <select class="form-control" id="transmission" name="transmission" novalidate>
                <option value="">Válasszon...</option>
                <option value="Manual" <?= $transmission == 'Manual' ? 'selected' : '' ?>>Manuális</option>
                <option value="Automatic" <?= $transmission == 'Automatic' ? 'selected' : '' ?>>Automata</option>
            </select>
            </div>
            <span><?=$errors['transmission'] ?? "" ?></span>
            <div class="mb-3">
            <label for="fuel" class="form-label">Üzemanyag</label>
            <select class="form-control" id="fuel" name="fuel" novalidate>
                <option value="">Válasszon...</option>
                <option value="Petrol" <?= $fuel == 'Petrol' ? 'selected' : '' ?>>Benzin</option>
                <option value="Diesel" <?= $fuel == 'Diesel' ? 'selected' : '' ?>>Dízel</option>
                <option value="Electric" <?= $fuel == 'Electric' ? 'selected' : '' ?>>Elektromos</option>
            </select>
            </div>
            <span><?=$errors['fuel'] ?? "" ?></span>
            <div class="mb-3">
            <label for="image" class="form-label">Kép</label>
            <input type="text" class="form-control" id="image" name="image" value="<?= $image ?>" placeholder="Kép URL" novalidate>
            </div>
            <span><?=$errors['image'] ?? "" ?></span>
            <br>
            <button type="submit" class="btn btn-outline-success me-2">Hozzáadás</button>
        </form>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>