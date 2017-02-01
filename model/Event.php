<?php

require_once "framework/Model.php";
require_once "Event.php";

class Member extends Model {
    public $idevent;
    public $start;
    public $finish;
    public $whole_day;
    public $title;
    public $description;
    //public $idcalendar;
    

    public function __construct($title, $whole_day, $start, $finish = NULL, $description = NULL, $idevent = NULL) {      
        $this->title = $title;
        $this->whole_day = $whole_day;
        $this->start = $start;
        if(!empty($whole_day))
            $this->finish = $start;
        else
            $this->finish = $finish;
        $his->description = $description;
        $this->idevent = $idevent;     
        return true;
    }  
    
    //new event
    public static function add_event($event, $user) {
        self::execute("INSERT INTO event(title,whole_day,start,finish,description,idcalendar)
                       VALUES(:title,:whole_day,:start,:finish,:description,:idcalendar)", 
                       array( 'title' => $event->title,
                              'whole_day'=> $event->whole_day,
                              'start' => $event->start, 
                              'finish' => $event->finish,                                                        
                              'description'=>$event->description, 
                              'idcalendar'=>$calendar->idcalendar));
        
        $event->idevent = self::lastInsertId();
        return true;
        
    }
    
    public static function update_event($title, $whole_day, $start, $finish, $description, $idevent) {
        self::execute("UPDATE event SET title=?, whole_day =?, start=?, finish=?, description=? WHERE idevent=?", 
                array($title, $whole_day, $start, $finish, $description, $idevent));
        return true;
    }
    
    public static function delete_event($idevent) {
        self::execute("DELETE FROM event WHERE idcalendar=? ", 
                array($idevent));
        return true;
    }
    
    public static function show_event($idevent) {
        $query = self::execute("SELECT * FROM event WHERE idevent = ?", array($idevent));
        $data = $query->fecth();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new event($data["title"], $data["whole_day"], $data["start"],   
                              $data["finish"], $data["description"], $data["idevent"]);
        }       
    }
    
    public static function show_events($user) {
        $query = self::execute("SELECT idevent, start, finish, whole_day, title, description,
                                FROM event
                                WHERE iduser = :iduser", array("iduser" => $user->iduser));
        
        $data = $query->fetchAll();
        $events = [];
        foreach ($data as $row) 
            $events[] = new Event($row['title'], $row['whole_day'], $row['start'],
                                   $row['finish'], $row['description'], $row['idevent']);
        
        return $events;
    }
}