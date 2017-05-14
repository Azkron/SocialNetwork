<?php

require_once 'model/User.php';
require_once 'model/Share.php';
require_once "framework/Model.php";
require_once "framework/Controller.php";

class Calendar extends Model {

    public $idcalendar;
    public $description;
    public $color;
    public $owner_pseudo;
    public $iduser;
    public $read_only;



    public function __construct($description, $color , $iduser, $idcalendar = NULL, $read_only = -1, $owner_pseudo = NULL) {
        $this->description = $description;
        $this->color = $color;
        $this->iduser = $iduser;
        $this->idcalendar = $idcalendar;
        $this->read_only = $read_only;
        $this->owner_pseudo= $owner_pseudo;
    }
    
    public static function get_calendar_by_description($description)
    {
        $query = self::execute("SELECT * FROM calendar where description = ?", array($description));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) 
            return false;
        else
            return new calendar($data["description"], $data["color"], $data["iduser"], $data["idcalendar"]);
    }


    public static function calendars_exist($user)
    {
        return Calendar::calendar_count($user) != 0;
    }
    
    public static  function calendar_count($user)
    {
        $query = self::execute("SELECT idcalendar FROM calendar WHERE iduser= :iduser UNION
                                SELECT idcalendar FROM share WHERE iduser= :iduser", array("iduser" => $user->iduser));
        return $query->rowCount();
    }

    //pre : user doesn't exist yet
    public function add_calendar() {
        self::execute("INSERT INTO calendar(description, color, iduser)
                       VALUES(?,?,?)", array($this->description, $this->color, $this->iduser));
        
        $this->idcalendar = self::lastInsertId();
        return true;
    }
    
    public function update() {
        self::execute("UPDATE Calendar SET description=?, color=? WHERE idcalendar=? ", 
                array($this->description, $this->color, $this->idcalendar));
        return true;
    }
    
    public function delete()
    {
        self::execute("DELETE FROM event WHERE idcalendar=?", 
                array($this->idcalendar));
        
        self::execute("DELETE FROM share WHERE idcalendar=?", 
                array($this->idcalendar));
        
        self::execute("DELETE FROM calendar WHERE idcalendar=?", 
                array($this->idcalendar));
        return true;
    }

    public static function get_calendar($idcalendar) 
    {
        $query = self::execute("SELECT * FROM calendar where idcalendar = ?", array($idcalendar));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) 
            return false;
        else
            return new calendar($data["description"], $data["color"], $data["iduser"], $data["idcalendar"]);
    }
    
    public static function get_calendars($user) 
    {
        $query = self::execute("SELECT description, color, calendar.iduser, idcalendar, -1 as read_only, pseudo
                                FROM calendar, user
                                WHERE calendar.iduser = :iduser && user.iduser = :iduser
                                UNION
                                SELECT description, color, calendar.iduser, calendar.idcalendar, read_only, pseudo
                                FROM share, calendar, user
                                WHERE calendar.iduser = user.iduser && share.idcalendar = calendar.idcalendar && share.iduser = :iduser", 
                                array("iduser" => $user->iduser));
        
        $data = $query->fetchAll();
        
        $calendars = [];
        
        foreach ($data as $row)
            $calendars[] = new Calendar($row['description'], $row['color'], $row['iduser'], $row['idcalendar'], $row['read_only'], $row['pseudo']);
            
        return $calendars;
    }
    
    public function hasEvents()
    {
        $query = self::execute("SELECT COUNT(*) 
                                FROM event
                                WHERE idcalendar = :idcalendar", 
                                array("idcalendar" => $this->idcalendar));
        
        $data = $query->fetch();
        return $data[0] > 0;
    }
    

    public function validate() {
        $errors = [];
        
        if(strlen($this->description) < 1 || strlen($this->description) > 50 )
            $errors[] = "The calendar description must be between 1 and 50 characters.";
        
        if($this->color == NULL || $this->color == '')
                $errors[] = "A color must be set.";
        
        if($this->has_duplicate()) 
                $errors[] = "A calendar with this description already exists.";
        
        return $errors;
    }
    
    
    private function has_duplicate() // just to check  for duplicates
    {
        if($this->idcalendar == NULL)
            $query = self::execute("SELECT COUNT(*) FROM calendar where description = ? && iduser = ?", array($this->description, $this->iduser));
        else
            $query = self::execute("SELECT COUNT(*) FROM calendar where description = ? && iduser = ? && idcalendar != ?", array($this->description, $this->iduser, $this->idcalendar));
        
        $data = $query->fetch();
        
        return $data[0] != 0;
    }

}
