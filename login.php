<?php
session_start();
include('userStorage.php');

$user = $_SESSION['user'] ?? null;
$admin = false;

if($user && $user['email']=="admin@ikarrental.hu"){
    $admin = true;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$errors = [];
if($_POST){
    
    if($email == ''){
        $errors['email'] = "E-mail cím megadása kötelező!";
    }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = "Az e-mail cím nem valid!";
    }
    
    if($password == ''){       
        $errors['password'] = "Jelszó megadása kötelező!";
    }else if(strlen($password)<5){
        $errors['password'] = "Jelszó legalább 5 karakterből kell hogy álljon!";
        
    }
    
    if(count($errors)==0){
        
        $userStorage = new UserStorage();
        $user = $userStorage->findByEmail($email);
        if(!$user){   
            $errors['email'] = "Ez az e-mail cím még nincs regisztrálva!";
        }else if($user['password']!=$password){
            $errors['password'] = "Helytelen jelszó!";
            $user = null;
        }else{
            $_SESSION['user'] = $user;
            header('Location: index.php');
            exit();
        
        }
    }    

}

?>

<!DOCTYPE html>
<html lang="hu">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKarRental - Bejelentkezés</title>
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
    <h2>Bejelentkezés</h2>
    <form action="login.php" method="post">
        
        <div class="mb-3">
            <label for="email" class="form-label">E-mail cím</label>
            <input type="text" class="form-control" id="email" name="email" novalidate value="<?= $email ?>">
            <span><?=$errors['email'] ?? "" ?></span>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Jelszó</label>
            <input type="password" class="form-control" id="password" name="password" novalidate value="<?= $password ?>">
            <span><?=$errors['password'] ?? "" ?></span>
        </div>
       
        <button type="submit" class="btn btn-outline-success me-2">Bejelentkezés</button>
    </form>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>