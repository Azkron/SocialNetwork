<?php

require_once "framework/Model.php";

class User extends Model {

    public $pseudo;
    public $hashed_password;
    public $email;
    public $full_name;
    public $iduser;
    

    public function __construct( $pseudo, $hashed_password, $email, $full_name, $iduser = NULL) {
        $this->iduser = $iduser;
        $this->pseudo = $pseudo;
        $this->hashed_password = $hashed_password;
        $this->email = $email;
        $this->full_name = $full_name;
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
    public static function add_user($user) {
        self::execute("INSERT INTO user(pseudo,password, email, full_name)
                       VALUES(?,?,?,?)", array($user->pseudo, $user->hashed_password, $user->email, $user->full_name));
        
        $user->iduser = self::lastInsertId();
        return true;
    }

    public static function get_user($pseudo) {
        $query = self::execute("SELECT * FROM User where pseudo = ?", array($pseudo));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return new User( $data["pseudo"], $data["password"], $data["email"], $data["full_name"], $data["iduser"]);
        }
    }

    public static function get_user_by_email($email) {
        $query = self::execute("SELECT * FROM User where email = ?", array($email));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return new User( $data["pseudo"], $data["password"], $data["email"], $data["full_name"], $data["iduser"]);
        }
    }
    
//    public static function get_user_pseudo($iduser) {
//        $query = self::execute("SELECT * FROM User where iduser = ?", array($iduser));
//        $data = $query->fetch(); // un seul résultat au maximum
//        if ($query->rowCount() == 0) {
//            return false;
//        } else {
//            return $data;
//        }
//    }    

    //renvoie un tableau de strings en fonction des erreurs de signup.
    public static function validate($pseudo, $password, $password_confirm, $email, $full_name) {
        $errors = [];
        $user = self::get_user($pseudo);
        if (self::get_user($pseudo) != null || self::get_user_by_email($email) != null) {
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

    //indique si un mot de passe correspond à son hash
    private static function check_password($clear_password, $hash) {
        return $hash === Tools::my_hash($clear_password);
    }

    //renvoie un string en fonction de l'erreur de login.
    public static function validate_login($pseudo, $password) {
        $error = "";
        $user = User::get_user($pseudo);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $error = "Wrong password. Please try again.";
            }
        } else {
            $error = "Can't find a user with the pseudo '$pseudo'. Please sign up.";
        }
        return $error;
    }

    /*public static function validate_photo($file) {
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
