<?php

require_once "framework/Model.php";
require_once "Event.php";
require_once "Tools.php";

class Member extends Model {
    public $idevent;
    public $idcalendar;
    public $start;
    public $finish;
    public $whole_day;
    public $title;
    public $description;
    public $color;
    //public $idcalendar;
    

    public function __construct($title, $whole_day, $start, $idcalendar, $finish = NULL, $description = NULL, $color = NULL, $idevent = NULL) 
    {      
        $this->title = $title;
        $this->whole_day = $whole_day;
        $this->start = $start;
        $this->finish = $finish;
        $this->description = $description;
        $this->idevent = $idevent;   
        $this->idcalendar = $idcalendar;
        $this->color = $color;
        return true;
    } 
    
    public static function get_events_in_week($user, $monday = 0) 
    {
        if($monday == 0)
            $monday = strtotime('monday this week');
        $start = $monday;
        $finish = Tools::get_timestamp($start, 7);
        $query = self::execute("SELECT event.idevent, event.start, event.finish, event.whole_day, event.title, event.description, event.idcalendar, calendar.color
                                FROM event, calendar WHERE event.idcalendar = calendar.idcalendar && calendar.iduser = :iduser &&  (:finish >= start && :start <= finish)", 
                                array('iduser' => $user,
                                        'start' => $start, 
                                        'finish' => $finish));
        $data = $query->fecth();
        
        $events = [];
        foreach ($data as $row) 
            $events[] = new event($row['title'], $row['whole_day'], $row['start'], $row['idcalendar'],
                                   $row['finish'], $row['description'], $row['color'], $row['idevent']);
        //$week = [][]; Apparently not needed
        $week = get_events_in_week_array($events, $start);
        
        return $week;
    }
    
    private static function get_events_in_week_array(&$events, $monday)
    {
        $week = [][];
        $day = $monday;
        for($i=0; $i < 7; $i++) 
        {
            foreach ($events as $event) 
                if($event->start <= $day  && $event->finish >= $day) 
                    insert_by_hour($week[$i],$event,$day);
                
            $day = Tools::get_timestamp($day, 1); // adds one day, I made the function
        }
        
        return $week;
    }

    private static function insert_by_hour(&$array, $event, $day) // noticed the & before $array, arrays are not passed by refference by default
    {
        $pos = 0;
        if(!$event->whole_day && (Tools::equal_day($day, $event->start) || Tools::equal_day($day, $event->finish)))
        {
            $i = 0;
            $pos = count($array);
            while($pos == count($array) && $i < count($array))
            {
                if(!$array[$i]->whole_day && (($array[$i]->start-$day) > ($event->start-$day)))
                    $pos = $i;
                ++$i;
            }
        }
        
        if($pos < count($array))
            for($i = count($array); $i >= $pos; --$i)
                $array[$i] = $array[$i-1];
        
        $array[$pos] = $event; 
    }
    
    
    public function get_hour_string($day)
    {
        if($this->whole_day)
            return "All day";
        else
        {
            $start = Tools::equal_day($day, $this->start);
            $finish = Tools::equal_day($day, $this->finish);
            if(!$start && !$finish)
                return "All day";
            else if($start && $finish)
                return date("H\hi", $this->start) + " - " + date("H\hi", $this->start);
            else if($start)
                return date("H\hi", $this->start) + " >>";
            else if($finish)
                return ">> " + date("H\hi", $this->finish);
        }
            
    }
    
    
    //new event
    public static function add_event($event, $user) 
    {
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
    
    public static function update_event($title, $whole_day, $start, $finish, $description, $idevent) 
    {
        self::execute("UPDATE event SET title=?, whole_day =?, start=?, finish=?, description=? WHERE idevent=?", 
                array($title, $whole_day, $start, $finish, $description, $idevent));
        return true;
    }
    
    public static function delete_event($idevent) 
    {
        self::execute("DELETE FROM event WHERE idcalendar=? ", 
                array($idevent));
        return true;
    }
    
    public static function get_event($idevent) 
    {
        $query = self::execute("SELECT * FROM event WHERE idevent = ?", array($idevent));
        $data = $query->fecth();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new event($data["title"], $data["whole_day"], $data["start"],   
                              $data["finish"], $data["description"], $data["idevent"]);
        }       
    }
    
    public static function get_events($user) 
    {
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