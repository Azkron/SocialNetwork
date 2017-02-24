<?php

require_once 'model/User.php';
require_once "framework/Model.php";
require_once "framework/Controller.php";

class Calendar extends Model {

    public $idcalendar;
    public $description;
    public $color;
    //public $shared_pseudo;
    

    public function __construct($description, $color , $idcalendar = NULL) {
        $this->description = $description;
        $this->color = $color;
        $this->idcalendar = $idcalendar;
        //$this->shared_pseudo = $shared_pseudo;
        return  true;
    }
    
    public static function get_shared_user($iduser) {
        $query = self::execute("SELECT iduser, read_only FROM share WHERE iduser = ?",
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
        foreach ($data as $row) 
            $shared_calendars[] = array("iduser" => $row['iduser'],
                                        "pseudo" => $row['pseudo'],
                                        "read_only" => $row['read_only'],
                                        "idcalendar" => $row['idcalendar']);
        return $shared_calendars;
    }
    
    public static function get_not_shared($user) {
        $query =  self::execute("SELECT iduser, pseudo FROM user
                                 WHERE user.iduser != ? AND user.iduser NOT IN (select share.iduser FROM share)",
                                 array($user->iduser));
        $data = $query->fetchAll();
        $not_shared_calendars = [];
        foreach ($data as $row) 
            $not_shared_calendars[] = array("iduser" => $row['iduser'],
                                            "pseudo" => $row['pseudo']);
        return $not_shared_calendars;
    }    
    
    public static function add_share($pseudo) {
        $shared_user_id = User::get_user_id_pseudo($pseudo);
        self::execute("INSERT INTO share(iduser, idcalendar, read_only)
                       VALUES(?,?,?)", array($shared_user->iduser, $idcalendar, $read_only));
        return true;
    }
    
    public static function update_share($iduser, $read_only) {
        $shared_user_id = User::get_user_id($iduser);
        self::execute("UPDATE share SET read_only=? WHERE share.iduser=? ", 
                array($read_only, $shared_user_id['iduser']));
        return true;
    }
    
    public static function delete_share($iduser) {
        $shared_user_id = User::get_user_id($iduser);
        self::execute("DELETE FROM share WHERE iduser=?", 
                array($shared_user_id['iduser']));
        return true;
    }
    
    public static  function calendar_count($user)
    {
        $query = self::execute("SELECT * FROM calendar WHERE iduser=?", array($user->iduser));
        return $query->rowCount();
    }

    //pre : user does'nt exist yet
    public static function add_calendar($calendar, $user) {
        self::execute("INSERT INTO calendar(description, color, iduser)
                       VALUES(?,?,?)", array($calendar->description, $calendar->color, $user->iduser));
        
        $calendar->idcalendar = self::lastInsertId();
        return true;
    }
    
    public static function update_calendar($description, $color, $idcalendar) {
        self::execute("UPDATE Calendar SET description=?, color=? WHERE idcalendar=? ", 
                array($description, $color, $idcalendar));
        return true;
    }
    
    public static function delete_calendar($idcalendar)
    {
        self::execute("DELETE FROM event WHERE idcalendar=?", 
                array($idcalendar));
        
        self::execute("DELETE FROM calendar WHERE idcalendar=?", 
                array($idcalendar));
        return true;
    }

    public static function get_calendar($idcalendar) 
    {
        $query = self::execute("SELECT * FROM calendar where idcalendar = ?", array($idcalendar));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) 
            return false;
        else
            return new calendar($data["description"], $data["color"], $data["idcalendar"]);
    }
    
    public static function get_calendars($user) 
    {
        $query = self::execute("SELECT idcalendar, description, color
                                FROM calendar 
                             WHERE iduser = :iduser", array("iduser" => $user->iduser));
//        $query = self::execute("SELECT calendar.idcalendar, description, color
//              FROM calendar, share
//              WHERE calendar.iduser = :iduser OR (share.iduser = :iduser AND share.idcalendar = calendar.idcalendar)"
//                , array("iduser" => $user->iduser));
//        $query = self::execute("SELECT calendar.idcalendar, description, color, (SELECT pseudo FROM share, calendar, user WHERE share.iduser = :iduser AND share.idcalendar = calendar.idcalendar AND calendar.iduser = user.iduser)
//              FROM calendar, share
//              WHERE calendar.iduser = :iduser OR 
//              ( :iduser = share.iduser AND share.idcalendar = calendar.idcalendar)"
//                , array("iduser" => $user->iduser));
//        $query = self::execute("SELECT calendar.idcalendar, description, color, pseudo
//              FROM calendar, share, user
//              WHERE calendar.iduser = :iduser OR 
//              ( :iduser = share.iduser AND share.idcalendar = calendar.idcalendar AND calendar.iduser = user.iduser)"
//                , array("iduser" => $user->iduser));
        
        $data = $query->fetchAll();
        $calendars = [];
        foreach ($data as $row) 
            $calendars[] = new Calendar($row['description'], $row['color'], $row['idcalendar']);
        
        return $calendars;
    }
    
    
    public static function validate($user, $description, $color, $idcalendar = NULL) {
        $errors = [];
        if(strlen($description) < 1 || strlen($description) > 50 )
            $errors[] = "The calendar description must be between 1 and 50 characters.";
        else if(self::check_duplicate($user, $description, $idcalendar)) 
                $errors[] = "A calendar with this description already exists.";
        
        return $errors;
    }
    
    
    public static function check_duplicate($user, $description, $idcalendar = NULL) // just to check  for duplicates
    {
        $query;
        if($idcalendar == NULL)
            $query = self::execute("SELECT * FROM calendar where description = ? && iduser = ?", array($description, $user->iduser));
        else
            $query = self::execute("SELECT * FROM calendar where description = ? && iduser = ? && idcalendar != ?", array($description, $user->iduser, $idcalendar));
        
        return $query->rowCount() != 0;
    }

    //renvoie un tableau de strings en fonction des erreurs de signup.
    /*public static function validate($pseudo, $password, $password_confirm, $email, $full_name) {
        $errors = [];
        $user = self::get_user($pseudo);
        if ($user) {
            $errors[] = "This user already exists.";
        } if ($pseudo == '') {
            $errors[] = "Pseudo is required.";
        } if (strlen($pseudo) < 3 || strlen($pseudo) > 16) {
            $errors[] = "Pseudo length must be between 3 and 16.";
        } if (!preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $pseudo)) {
            $errors[] = "Pseudo must start by a letter and must contain only letters and numbers.";
        } if (strlen($password) < 8 || strlen($password) > 16) {
            $errors[] = "Password length must be between 8 and 16.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        } if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    public static function validate_photo($file) {
        if (isset($file['name']) && $file['name'] != '') {
            if ($file['error'] == 0) {
                $valid_types = array("image/gif", "image/jpeg", "image/png");
                if (!in_array($_FILES['image']['type'], $valid_types)) {
                    return "Unsupported image format : gif, jpg/jpeg or png.";
                }
            } else {
                return "Error while uploading file.";
            }
        }
        return true;
    }

    //pre : validate_photo($file) returns true
    public function generate_photo_name($file) {
        //note : time() est utilisé pour que la nouvelle image n'aie pas
        //       le meme nom afin d'éviter que le navigateur affiche
        //       une ancienne image présente dans le cache
        if ($_FILES['image']['type'] == "image/gif") {
            $saveTo = $this->pseudo . time() . ".gif";
        } else if ($_FILES['image']['type'] == "image/jpeg") {
            $saveTo = $this->pseudo . time() . ".jpg";
        } else if ($_FILES['image']['type'] == "image/png") {
            $saveTo = $this->pseudo . time() . ".png";
        }
        return $saveTo;
    }
*/
}
