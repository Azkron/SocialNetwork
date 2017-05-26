<?php

require_once 'model/User.php';
require_once 'model/Calendar.php';
require_once "framework/Model.php";
require_once "framework/Controller.php";

class Share extends Model {

    public $iduser;
    public $idcalendar;
    public $pseudo;
    public $read_only;
    

    public function __construct($iduser, $idcalendar, $read_only, $pseudo = NULL) {
        $this->iduser = $iduser;
        $this->idcalendar = $idcalendar;
        $this->read_only = $read_only;
        $this->pseudo = $pseudo;
    }
    
    public static function get_shared_calendar($iduser, $idcalendar) 
    {
        $query = self::execute("SELECT * FROM share where iduser = ? AND idcalendar = ?", 
                                array($iduser, $idcalendar));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) 
            return false;
        else
            return new share($data["iduser"], $data["idcalendar"], $data["read_only"]);
    }

    public function add_share() {
        
        $user = User::get_user_by_iduser($this->iduser);
        if(!$user->check_owned_calendar($_POST['idcalendar']))
            throw new Exception("Current user does not own this calendar!");
        self::execute("INSERT INTO share(iduser, idcalendar, read_only) VALUES(?,?,?)", 
                       array($this->iduser, $this->idcalendar, $this->read_only));
        
        return true;
    }
    
    public function update_share() {        
        self::execute("UPDATE share SET read_only=? WHERE iduser=? AND idcalendar = ?", 
                      array($this->read_only, $this->iduser, $this->idcalendar));
        return true;
    }
    
    public function delete_share() {
        self::execute("DELETE FROM share WHERE iduser=? AND idcalendar = ?", 
                      array($this->iduser, $this->idcalendar));
        return true;
    }
    
     public function validate_share() {
        $errors = [];
        if(strlen($this->pseudo) == 0)
            $errors[] = "You need to select a pseudo to share your calendar";      
        return $errors;
    }
    
    
}
