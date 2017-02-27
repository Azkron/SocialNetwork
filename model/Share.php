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
    

    public function __construct($iduser, $idcalendar , $pseudo, $read_only) {
        $this->iduser = $iduser;
        $this->idcalendar = $idcalendar;
        $this->pseudo = $pseudo;
        $this->read_only = $read_only;
        return  true;
    }
    
    public static function get_user_shared($idcalendar, $user) {
        $query =  self::execute("SELECT user.iduser, pseudo, read_only, idcalendar FROM user, share
                                 WHERE share.iduser = user.iduser AND idcalendar = ? AND user.iduser != ?
                                 ORDER BY pseudo", 
                                array($idcalendar, $user->iduser));
        
        $data = $query->fetchAll();
        
        $shared_calendars = [];
        if(count($data) > 0) {
            foreach ($data as $row)
                $shared_calendars[] = new Share($row['iduser'], $row['idcalendar'], $row['pseudo'], $row['read_only']);
            return $shared_calendars;
        }
        else
            return NULL;
        
    }
    
    public static function get_user_not_shared($user, $idcalendar) {
        $query =  self::execute("SELECT iduser, pseudo FROM user
                                 WHERE user.iduser != ? AND user.iduser NOT IN 
                                      (select share.iduser FROM share)
                                 UNION
                                 SELECT user.iduser, pseudo FROM user 
                                 join share on share.iduser = user.iduser 
                                 where user.iduser != ? AND share.idcalendar != ?
                                 ORDER BY pseudo",
                                 array($user->iduser, $user->iduser, $idcalendar));
        $data = $query->fetchAll();
        
        $not_shared_calendars = [];
        if(count($data) > 0) {
            foreach ($data as $row) 
                $not_shared_calendars[] = array("iduser" => $row['iduser'],
                                                "pseudo" => $row['pseudo']);
            return $not_shared_calendars;
        }
        else 
            return NULL;
        
    }    
    
    public static function add_share($pseudo, $idcalendar, $read_only) {
        $shared_user = User::get_user($pseudo);
        self::execute("INSERT INTO share(iduser, idcalendar, read_only) VALUES(?,?,?)", 
                       array($shared_user->iduser, $idcalendar, $read_only));
        
        return true;
    }
    
    public static function update_share($iduser, $idcalendar, $read_only) {        
        self::execute("UPDATE share SET read_only=? WHERE iduser=? AND idcalendar = ?", 
                      array($read_only, $iduser, $idcalendar));
        return true;
    }
    
    public static function delete_share($iduser, $idcalendar) {
        self::execute("DELETE FROM share WHERE iduser=? AND idcalendar = ?", 
                      array($iduser, $idcalendar));
        return true;
    }
    
     public static function validate_share($pseudo) {
        $errors = [];
        if(strlen($pseudo) == 0)
            $errors[] = "You need to select a pseudo to share your calendar";      
        return $errors;
    }
    
    
}
