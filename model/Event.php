<?php

require_once "framework/Model.php";
require_once "framework/Tools.php";
require_once "model/Date.php";

class Event extends Model {
    public $idevent;
    public $idcalendar;
    public $start;
    public $finish;
    public $whole_day;
    public $title;
    public $description;
    public $color;
    //public $idcalendar;
    

    public function __construct($title, $whole_day, $start, $idcalendar, $finish = NULL, 
                                $description = NULL, $color = NULL, $idevent = NULL) 
    {      
        $this->title = $title;
        $this->whole_day = $whole_day;
        $this->start = new Date($start);
        $this->finish = new Date($finish);
        $this->description = $description;
        $this->idevent = $idevent;   
        $this->idcalendar = $idcalendar;
        $this->color = $color;
        return true;
    } 
    
    public function start_string()
    {
        return $this->start->datetime_string();
    }
    
    public function finish_string()
    {
        if($this->finish != NULL)
            return $this->finish->datetime_string();
        else
            return NULL;
    }
    
    // $weekMod argument is te index of the week relative to the current week
    public static function get_events_in_week($user, $weekMod = 0) 
    {
        $start = Date::monday($weekMod);
        $finish = Date::sunday($weekMod);
//        $start = mb_convert_encoding($start, "UTF-8");
//        $finish = mb_convert_encoding($finish, "UTF-8");
        $query = self::execute("SELECT idevent, start, finish, whole_day, title, event.description, event.idcalendar, color "
                                . "FROM event, calendar WHERE event.idcalendar = calendar.idcalendar && calendar.iduser = :iduser "
                                . "&&  (:finish >= start && :start <= finish)", 
                                array('iduser' => $user->iduser,
                                       'start' => $start->datetime_string(), 
                                       'finish' => $finish->datetime_string())
                );
        
        $data = $query->fetchAll();
        
        $events = [];
        if(count($data) > 0)
        {
            foreach ($data as $row) 
                $events[] = new event($row['title'], $row['whole_day'], $row['start'], $row['idcalendar'],
                                       $row['finish'], $row['description'], $row['color'], $row['idevent']);
            //$week = [][]; Apparently not needed
            return self::get_events_in_week_array($events, $start);
        }
        else
            return NULL;
        
    }
    
    private static function get_events_in_week_array(&$events, $start)
    {
        $week;
        $day = $start;
        for($i=0; $i < 7; $i++) 
        {
            foreach ($events as $event) 
                if($event->start->compare($day) <= 0  && $event->finish->compare($day) >= 0) 
                    self::insert_by_hour($week[$i], $event, $day);
                
            $day->nextDay();
        }
        
        return $week;
    }

    private static function insert_by_hour(&$array, $event, $day) // noticed the & before $array, arrays are not passed by refference by default
    {
        $pos = 0;
        if(!$event->whole_day && ($event->start->compare_date($day) == 0 || $event->finish->compare_date($day) == 0))
        {
            $i = 0;
            $pos = count($array);
            while($pos == count($array) && $i < count($array))
            {
                if(!$array[$i]->whole_day && ($event->start->compare($array[$i]->start) < 0))
                    $pos = $i;
                ++$i;
            }
        }
        
        if($pos < count($array))
            for($i = count($array)-1; $i > $pos; --$i)
                $array[$i] = $array[$i-1];
        
        $array[$pos] = $event; 
    }
    
    
    public function get_time_string($day)
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
    public static function add_event($event) 
    {
        self::execute("INSERT INTO event(title, whole_day, start, idcalendar, finish, description)
                       VALUES(:title, :whole_day, :start, :idcalendar, :finish, :description)", 
                       array( 'title' => $event->title,
                              'whole_day'=> $event->whole_day,
                              'start' => $event->start_string(), 
                              'finish' => $event->finish_string(),                                                        
                              'description' => $event->description, 
                              'idcalendar' => $event->idcalendar));
        
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
        $query = self::execute("SELECT idevent, start, finish, whole_day, title, event.description, event.idcalendar, color
                                FROM event, calendar WHERE idevent event.idcalendar = calendar.idcalendar", array($idevent));
        $data = $query->fecth();
        
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new event($data["title"], $data["whole_day"], $data["start"], $data["idcalendar"],   
                              $data["finish"], $data["description"], $data["color"], $data["idevent"]);
        }       
    }
    /* NOT NEEDED AS WE TAKE THE EVENTS PER WEEK
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
     * 
     */
}