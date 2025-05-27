<?php
require_once('storage.php');
class UserStorage extends Storage {
    public function __construct(){
        parent::__construct(new JsonIO('users.json'));
        
    }   
    
    public function findByEmail($email){
        $users = $this->findMany(function ($user) use ($email){
            return $user['email'] == $email;
        });
        return !empty($users) ? reset($users) : null;
    }

    public function findById($id){
        $users = $this->findMany(function ($user) use ($id){
            return $user['id'] == $id;
        });
        return !empty($users) ? reset($users) : null;
    }
}
?>