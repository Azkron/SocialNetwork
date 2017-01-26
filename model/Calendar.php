<?php

require_once "framework/Model.php";
require_once "framework/Controller.php";

class Calendar extends Model {

    public $idCalendar;
    public $description;
    public $color;
    

    public function __construct($description, $color = "white", $idCalendar = NULL) {
        $this->description = $description;
        $this->color = $color;
        $this->idCalendar = $idCalendar;
    }

    public function write_event($event) {
        return Calendar::add_event($event);
    }

    public function delete_event($event) {
        return $event->delete($this);
    }

    public function get_events() {
        return Calendar::get_events($this);
    }

    //pre : user does'nt exist yet
    public static function add_calendar($calendar, $user) {
        self::execute("INSERT INTO calendar(description, color, iduser)
                       VALUES(?,?,?)", array($calendar->description, $calendar->color, $user->idUser));
        
        $calendar->idCalendar = self::lastInsertId();
        return true;
    }

    public static function get_calendar($idCalendar) {
        $query = self::execute("SELECT * FROM calendar where idcalendar = ?", array($idCalendar));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new calendar($data["description"], $data["color"], $data["idcalendar"]);
        }
    }
    
    public function get_calendars($user) {
        $query = self::execute("SELECT idCalendar, description, color
              FROM calendar 
              WHERE iduser = :iduser", array("iduser" => $user->idUser));
        return $query->fetchAll();
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
