<?php
require_once('storage.php');
class ReservationStorage extends Storage {
    public function __construct(){
        parent::__construct(new JsonIO('reservations.json'));
        
    }   
    
    public function free($car, $start_date, $end_date){
        $reservations = $this->findAll();
        foreach ($reservations as $reservation) {
            if ($reservation['carId'] === $car['id']) {
                if (($start_date >= $reservation['start_date'] && $start_date <= $reservation['end_date']) ||
                    ($end_date >= $reservation['start_date'] && $end_date <= $reservation['end_date']) ||
                    ($start_date <= $reservation['start_date'] && $end_date >= $reservation['end_date'])) {
                    return false;
                }
            }
        }
        return true;
    }
    public function filterFreeCars($cars, $start_date, $end_date) {
        $freeCars = [];
        foreach ($cars as $car) {
            if ($start_date == '') {
                $start_date = $end_date;
            } else if ($end_date == '') {
                $end_date = $start_date;
            }
            if ($this->free($car, $start_date, $end_date)) {
                $freeCars[] = $car;
            }
        }
        return $freeCars;
    }

    public function same($reservation){
        $reservations = $this->findAll();
        foreach ($reservations as $existingReservation) {
            if ($existingReservation['carId'] === $reservation['carId'] &&
            $existingReservation['start_date'] === $reservation['start_date'] &&
            $existingReservation['end_date'] === $reservation['end_date'] &&
            $existingReservation['userId'] === $reservation['userId']) {
            return true;
            }
        }
        return false;
    }
    public function findByUserId($id){
        return $this->findMany(function ($reservation) use ($id){
            return $reservation['userId'] == $id;
        });
    }
}
?>