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
        $this->finish = $finish;
        if($this->finish != NULL)
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
        $query = self::execute("SELECT idevent, start, finish, whole_day, title, event.description, event.idcalendar, color 
                                FROM event, calendar WHERE event.idcalendar = calendar.idcalendar AND calendar.iduser = :iduser 
                                AND ( 
                                        (CAST(start AS DATE) >= CAST(:start AS DATE) AND CAST(start AS DATE) <= CAST(:finish AS DATE)) 
                                        OR ( 
                                            finish IS NOT NULL  
                                            AND (CAST(start AS DATE) <= CAST(:finish AS DATE) AND CAST(finish AS DATE) >= CAST(:start AS DATE))
                                            ) 
                                    )", 
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
            return self::get_week($events, $start);
        }
        else
            return NULL;
        
    }
    
    
    private static function get_week(&$events, $start)
    {
        $week;
        $day = $start;
        for($i=0; $i < 7; $i++) 
        {
            $week[$i] = [];
            foreach ($events as $event) 
            {
                $insert = false;
                if($event->finish != NULL)
                {
                    
                    if($event->start->compare_date($day) <= 0 && $event->finish->compare_date($day) >= 0) 
                        $insert = true;
                }
                else if($event->start->compare_date($day) == 0)
                    $insert = true;
                
                if($insert)
                    self::insert_by_hour($week[$i], $event, $day);
            }
                
            $day->next_day();
        }
        
        return $week;
    }

    private static function insert_by_hour(&$array, $event, $day) // noticed the & before $array, arrays are not passed by refference by default
    {
        $pos = NULL;
        if(!$event->whole_day && ($event->start->compare_date($day) == 0 || $event->finish->compare_date($day) == 0))
        {
            $i = 0;
            while($pos == NULL && $i < count($array))
            {
                if(!$array[$i]->whole_day && ($event->start->compare($array[$i]->start) < 0))
                    $pos = $i;
                ++$i;
            }
        }
        
        if($pos != NULL)
        {
            for($i = count($array)-1; $i > $pos; --$i)
                $array[$i] = $array[$i-1];
            $array[$pos] = $event; 
        }
        else
            $array[] = $event;
    }
    
    
    public function is_in_day($day)
    {
        if($this->finish == NULL)
        {
            if($this->start->compare($day) == 0)
                return true;
        }
        else if($this->finish->compare($day) >= 0)
                if($this->start->compare($day) <= 0)
                    return true;
            
    }
    
    public function get_time_string($day)
    {
        if($this->whole_day)
            return "All day";
        else
        {
            $startTime = NULL;
            $finishTime = NULL;
            
            if($this->start->compare_date($day) == 0)
                $startTime = $this->start->time_string();
            
            if($this->finish != NULL)
                if($this->finish->compare_date($day) == 0)
                {
                    if($startTime != NULL)
                        $finishTime = " - ".$this->finish->time_string();
                    else
                        $finishTime = " >> ".$this->finish->time_string();
                    
                }
                else if($startTime != NULL)
                    $finishTime = " >>";
                
                
            if($startTime == NULL && $finishTime == NULL)
                return "All day";
            else if($startTime != NULL && $finishTime != NULL)
                return $startTime.$finishTime;
            else if($startTime != NULL)
                return $startTime;
            else if($finishTime != NULL)
                return $finishTime;
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