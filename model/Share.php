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
    
    public static function get_shared_user($iduser) {
        $query = self::execute("SELECT pseudo FROM User WHERE iduser = ?",
                                array($iduser));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data;
        }
    }
    
    
    public static function get_shared($idcalendar, $user) {
        $query =  self::execute("SELECT user.iduser, pseudo, read_only, idcalendar FROM user, share
                                 WHERE share.iduser = user.iduser AND idcalendar = ? AND user.iduser != ?", 
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
    
    public static function get_list_not_shared($user) {
        $query =  self::execute("SELECT iduser, pseudo FROM user
                                 WHERE user.iduser != ? AND user.iduser NOT IN (select share.iduser FROM share)",
                                 array($user->iduser));
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
    
    public static function add_share($share) {
        //$shared_user_id = User::get_user_id_pseudo($pseudo);
        self::execute("INSERT INTO share(idcalendar, read_only) VALUES(?,?)", 
                       array($share->idcalendar, $share->read_only));
        
        $share->iduser = self::lastInsertId();
        return true;
    }
    
    public static function update_share($iduser, $read_only) {
        //$shared_user_id = User::get_user_id($iduser);
        
        self::execute("UPDATE share SET read_only=? WHERE share.iduser=? ", 
                      array($read_only, $iduser));
        return true;
    }
    
    public static function delete_share($iduser) {
       // $shared_user_id = User::get_user_id($iduser);
        self::execute("DELETE FROM share WHERE iduser=?", 
                      array($iduser));
        return true;
    }
    
    
}
