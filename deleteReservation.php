<?php
include('reservationStorage.php');

$id = $_GET['id'];
$reservationStorage = new ReservationStorage();
$reservationStorage->delete($id);
header('Location: profile.php');
?>