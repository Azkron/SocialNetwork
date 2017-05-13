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
/*
    public function write_event($event) {
        return Calendar::add_event($event);
    }

    public function delete_event($event) {
        return $event->delete($this);
    }

    public function get_events() {
        return Calendar::get_events($this);
    }
*/
    
    public function check_owned_calendar($idcalendar){
        $query =  self::execute("SELECT COUNT(*) FROM calendar 
                                 WHERE calendar.iduser = :iduser AND calendar.idcalendar = :idcalendar",
                                 array("iduser" => $this->iduser, "idcalendar" => $idcalendar));
        
        
        return $query->rowCount() >= 0;
    }
    
    public function check_owned_event($idevent){
        $query =  self::execute("SELECT COUNT(*) FROM calendar, event
                                 WHERE calendar.iduser = :iduser AND calendar.idcalendar = event.idcalendar && event.idevent = :idevent",
                                 array("iduser" => $this->iduser, "idevent" => $idevent));
        
        return $query->rowCount() >= 0;
    }
    
    
    public function get_events_json($start, $finish = NULL)
    {
        
        $start = (new Date($start))->datetime_string();
        if($finish != NULL)
            $finish = (new Date($finish))->datetime_string();
        
        $query = self::execute("SELECT idevent, start, finish, whole_day, title, event.description, event.idcalendar, color, -1 as read_only
                                    FROM event, calendar 
                                    WHERE :iduser = calendar.iduser && calendar.idcalendar = event.idcalendar && 
                                        ((start >= :start && start <= :finish) || (finish IS NOT NULL && (finish >= :start && finish <= :finish)))
                                UNION
                                SELECT idevent, start, finish, whole_day, title, event.description, event.idcalendar, color, read_only
                                    FROM event, calendar, share 
                                    WHERE :iduser = share.iduser && calendar.idcalendar = share.idcalendar && calendar.idcalendar = event.idcalendar && 
                                        ((start >= :start && start <= :finish) || (finish IS NOT NULL && (finish >= :start && finish <= :finish)))
                                ", 
                                array('iduser' => $this->iduser, 'start' => $start, 'finish' =>$finish));
        $data = $query->fetchAll();
        
        
        
        if ($query->rowCount() == 0) {
            return NULL;
        } else {
            
            $events = Array();
            foreach ($data as $row) 
                $events[] = new event($row['title'], $row['whole_day'], $row['start'], $row['idcalendar'],
                                       $row['finish'], $row['description'], $row['color'], $row['idevent'], $row['read_only']);
            
            $fullcalendarArr = Array();
            foreach($events as $event)
                $fullcalendarArr[] = $event->fullcalendar_params();
            
            return json_encode($fullcalendarArr);
        }    
        
    }
    
    public function get_shared_users($idcalendar) {
        $query =  self::execute("SELECT user.iduser, pseudo, read_only, idcalendar FROM user, share
                                 WHERE share.iduser = user.iduser AND idcalendar = ? AND user.iduser != ?
                                 ORDER BY pseudo", 
                                array($idcalendar, $this->iduser));
        
        $data = $query->fetchAll();
        
        $shared_calendars = [];
        if(count($data) > 0) {
            foreach ($data as $row)
                $shared_calendars[] = new Share($row['iduser'], $row['idcalendar'], $row['read_only'], $row['pseudo']);
            return $shared_calendars;
        }
        else
            return NULL;
        
    }
    
    public function get_not_shared_users($idcalendar) {
        $query =  self::execute("SELECT user.iduser, pseudo FROM user 
                         WHERE user.iduser != :iduser AND user.iduser NOT IN 
                            (SELECT share.iduser FROM share WHERE share.idcalendar = :idcalendar)
                         ORDER BY pseudo",
                         array("iduser" => $this->iduser,"idcalendar" => $idcalendar));
//        $query =  self::execute("SELECT iduser, pseudo FROM user
//                                 WHERE user.iduser != :iduser AND user.iduser NOT IN 
//                                      (select share.iduser FROM share)
//                                 UNION
//                                 SELECT user.iduser, pseudo FROM user 
//                                 join share on share.iduser = user.iduser 
//                                 where user.iduser != :iduser AND share.idcalendar != :idcalendar
//                                 ORDER BY pseudo",
//                                 array("iduser" => $this->iduser,"idcalendar" => $idcalendar));
        $data = $query->fetchAll();
        
        $not_shared_users = [];
        if(count($data) > 0) {
            foreach ($data as $row) 
                $not_shared_users[] = array("iduser" => $row['iduser'],
                                            "pseudo" => $row['pseudo']);
            return $not_shared_users;
        }
        else 
            return NULL;
        
    }    
    
    //pre : user does'nt exist yet
    public static function add_user($user) {
        self::execute("INSERT INTO user(pseudo,password, email, full_name)
                       VALUES(?,?,?,?)", array($user->pseudo, $user->hashed_password, $user->email, $user->full_name));
        
        $user->iduser = self::lastInsertId();
        return true;
    }
    
    public static function get_user_by_iduser($iduser) {
        $query = self::execute("SELECT * FROM User where iduser = ?", array($iduser));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return new User( $data["pseudo"], $data["password"], $data["email"], $data["full_name"], $data["iduser"]);
        }
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
    
    public function get_calendars() 
    {
        $query = self::execute("SELECT description, color, calendar.iduser, idcalendar, -1 as read_only, pseudo
                                FROM calendar, user
                                WHERE calendar.iduser = :iduser && user.iduser = :iduser
                                UNION
                                SELECT description, color, calendar.iduser, calendar.idcalendar, read_only, pseudo
                                FROM share, calendar, user
                                WHERE calendar.iduser = user.iduser && share.idcalendar = calendar.idcalendar && share.iduser = :iduser", 
                                array("iduser" => $this->iduser));
        
        $data = $query->fetchAll();
        
        $calendars = [];
        
        foreach ($data as $row)
            $calendars[] = new Calendar($row['description'], $row['color'], $row['iduser'], $row['idcalendar'], $row['read_only'], $row['pseudo']);
            
        return $calendars;
    }
    
    public function get_writable_calendars() 
    {
        $query = self::execute("SELECT idcalendar, description, color
                                FROM calendar 
                                WHERE iduser = :iduser
                                UNION
                                SELECT share.idcalendar, description, color
                                FROM share join calendar on share.idcalendar = calendar.idcalendar
                                WHERE share.iduser = :iduser AND read_only = 0", 
                                array("iduser" => $this->iduser));

        
        $data = $query->fetchAll();
        $calendars = [];
        foreach ($data as $row)
        {
            $calendars[] = new Calendar($row['description'], $row['color'],$this->iduser, $row['idcalendar']);
        }
        
        return $calendars;
    }
    
    
    // $weekMod argument is the index of the week relative to the current week
    public function get_events_in_week($weekMod = 0) 
    {
        $start = Date::monday($weekMod);
        $finish = Date::sunday($weekMod);
        $query = self::execute("SELECT idevent, start, finish, whole_day, title, event.description, event.idcalendar, color, -1 as read_only 
                                FROM event, calendar WHERE event.idcalendar = calendar.idcalendar AND calendar.iduser = :iduser 
                                AND ( 
                                        (DATE(start) >= DATE(:start) AND DATE(start) <= DATE(:finish)) 
                                        OR ( 
                                            finish IS NOT NULL  
                                            AND (DATE(start) <= DATE(:finish) AND DATE(finish) >= DATE(:start))
                                            ) 
                                    )
                                UNION
                                SELECT idevent, start, finish, whole_day, title, event.description, event.idcalendar, color, read_only
                                FROM event, calendar, share WHERE event.idcalendar = calendar.idcalendar AND calendar.idcalendar = share.idcalendar AND share.iduser = :iduser
                                AND ( 
                                        (DATE(start) >= DATE(:start) AND DATE(start) <= DATE(:finish)) 
                                        OR ( 
                                            finish IS NOT NULL  
                                            AND (DATE(start) <= DATE(:finish) AND DATE(finish) >= DATE(:start))
                                            ) 
                                    )
                                ", 
                        array('iduser' => $this->iduser,
                               'start' => $start->datetime_string(), 
                               'finish' => $finish->datetime_string())
        );
        $data = $query->fetchAll();
        
        $events = [];
        if(count($data) > 0)
        {
            foreach ($data as $row) 
                $events[] = new event($row['title'], $row['whole_day'], $row['start'], $row['idcalendar'],
                                       $row['finish'], $row['description'], $row['color'], $row['idevent'], $row['read_only']);
            
            //$week = [][]; Apparently not needed
            return Event::get_week($events, $start);
        }
        else
            return NULL;
        
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
