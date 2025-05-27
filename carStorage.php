<?php
require_once('storage.php');
class CarStorage extends Storage {
    public function __construct(){
        parent::__construct(new JsonIO('cars.json'));
    }

    public function findByModel($model){
        return $this->findMany(function ($car) use ($model){
            return $car['model'] == $model;
        });
    }

   public function findById($id){
    $cars = $this->findMany(function ($car) use ($id){
        return $car['id'] == $id;
    });
        return !empty($cars) ? reset($cars) : null;
    }

    public function filter($passengers, $transmission, $price_min, $price_max) {
        return $this->findMany(function($car) use ($passengers, $transmission, $price_min, $price_max) {
            
            if ($transmission != '' && $car['transmission'] != $transmission) {
            return false;
            }
            if ($passengers != '' && $car['passengers'] < $passengers) {
                return false;
            }
            if ($price_min != '' && $car['daily_price_huf'] < $price_min) {
                return false;
            }
            if ($price_max != '' && $car['daily_price_huf'] > $price_max) {
                return false;
            }
           
            return true;
        });
    }
    
    
}
?>