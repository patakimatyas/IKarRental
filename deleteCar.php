<?php

include('carStorage.php');

$id = $_GET['id'];
$carStorage = new CarStorage();
$carStorage->delete($id);
header('Location: index.php');
?>
