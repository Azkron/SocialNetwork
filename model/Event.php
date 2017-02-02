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
        if(count($whole_day)!= 0)
            $this->finish = $start;
        else
            $this->finish = $finish;
        $his->description = $description;
        $this->idevent = $idevent;     
        return true;
    } 
    
    public static function events_in_week($start, $finish) {
        $query = self::execute("SELECT * FROM event WHERE (:finish >= start && :start <= finish)", 
                        array('start' => $start, 
                              'finish' => $finish));
        $data = $query->fecth();
        $events = [];
        foreach ($data as $row) 
            $events[] = new event($row['title'], $row['whole_day'], $row['start'],
                                   $row['finish'], $row['description'], $row['idevent']);
        
        $week = [][][]; // [day][event][idevent, description, time]
            //$week[3][1][0]
        $day = $start;
        for($i=0; $i < 7; $i++) {
            // replace time() with the time stamp you want to add one day to    
            $e = 0;
            foreach ($events as $event) {            
                if($event->start <= $day  && $event->finish >= $day) {
                    $week[$i][$e++][$day]
                }
            }
            $start->modify('+1 day');
        }
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
    
    public static function get_event($idevent) {
        $query = self::execute("SELECT * FROM event WHERE idevent = ?", array($idevent));
        $data = $query->fecth();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new event($data["title"], $data["whole_day"], $data["start"],   
                              $data["finish"], $data["description"], $data["idevent"]);
        }       
    }
    
    public static function get_events($user) {
        $query = self::execute("SELECT *
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