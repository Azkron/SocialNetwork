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
    public $read_only;
    

    public function __construct($title, $whole_day, $start, $idcalendar, $finish = NULL, 
                                $description = NULL, $color = NULL, $idevent = NULL, $read_only = NULL) 
    {      
        $this->title = $title;
        $this->whole_day = $whole_day;
        $this->start = $start;
        if($this->start != NULL)
            $this->start = new Date($start);
        $this->finish = $finish;
        if($this->finish != NULL)
            $this->finish = new Date($finish);
        $this->description = $description;
        $this->idevent = $idevent;   
        $this->idcalendar = $idcalendar;
        $this->color = $color;
        $this->read_only= $read_only;
        return true;
    } 
    
    
    
    public static function get_event($idevent) 
    {
        $query = self::execute("SELECT idevent, start, finish, whole_day, title, event.description, event.idcalendar, color
                                FROM event, calendar WHERE idevent = :idevent AND event.idcalendar = calendar.idcalendar", 
                                array('idevent' => $idevent));
        $data = $query->fetch();
        
        if ($query->rowCount() == 0) {
            return NULL;
        } else {
            return new event($data["title"], $data["whole_day"], $data["start"], $data["idcalendar"],   
                              $data["finish"], $data["description"], $data["color"], $data["idevent"]);
        }       
        
    }
    
    public static function get_week(&$events, $start)
    {
        //$week;
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
        $pos = -1;
        if(!$event->whole_day && ($event->start->compare_date($day) == 0 || $event->finish->compare_date($day) == 0))
        {
            $i = 0;
            while($pos == -1 && $i < count($array))
            {
                if(!$array[$i]->whole_day && ($event->start->compare_time($array[$i]->start) < 0))
                    $pos = $i; 
                ++$i;
            }
        }
        
        
        if($pos != -1)
        {
            for($i = count($array); $i > $pos; --$i)
                $array[$i] = $array[$i-1];
            $array[$pos] = $event; 
        }
        else
            $array[] = $event;
    }
    
    
    /*public function is_in_day($day)
    {
        if($this->finish == NULL)
        {
            if($this->start->compare($day) == 0)
                return true;
        }
        else if($this->finish->compare($day) >= 0)
                if($this->start->compare($day) <= 0)
                    return true;
            
    }*/
    
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
    public function add_event() 
    {
        self::execute("INSERT INTO event(title, whole_day, start, idcalendar, finish, description)
                       VALUES(:title, :whole_day, :start, :idcalendar, :finish, :description)", 
                       array( 'title' => $this->title,
                              'whole_day'=> $this->whole_day,
                              'start' => $this->start_string(), 
                              'finish' => $this->finish_string(),                                                        
                              'description' => $this->description, 
                              'idcalendar' => $this->idcalendar));
        
        $this->idevent = self::lastInsertId();
        return true;
        
    }
    
    public function update() 
    {
        self::execute("UPDATE event SET title = :title, whole_day = :whole_day, 
                        start = :start, idcalendar = :idcalendar,
                         finish = :finish, description = :description 
                         WHERE idevent = :idevent", 
                       array( 'title' => $this->title,
                              'whole_day'=> $this->whole_day,
                              'start' => $this->start_string(), 
                              'finish' => $this->finish_string(),                                                        
                              'description' => $this->description, 
                              'idcalendar' => $this->idcalendar,
                              'idevent' => $this->idevent));
        return true;
    }
    
    public function delete() 
    {
        self::execute("DELETE FROM event WHERE idevent=? ", 
                array($this->idevent));
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
    
    public function validate() 
    {
        $errors = [];
        if(strlen($this->title) < 1 || strlen($this->title) > 50 )
            $errors[] = "The event title must be between 1 and 50 characters.";
        if($this->description != NULL && strlen($this->description) > 500 )
            $errors[] = "The event title must have a maximum 500 characters.";
        
        if($this->start == NULL) 
            $errors[] = "Start date is required.";
        else if($this->finish != NULL && $this->start->compare($this->finish) > 0)
            $errors[] = "Start time must be earlier than finish time.";
        
        //$errors[] = $this->start->compare($this->finish) > 0;
        
        if(count($errors)==0)
            if($this->has_duplicate())
                $errors[] = "A duplicate of this event already exists with same title and starting time";
            
        return $errors;
    }
    
    private function has_duplicate()
    {
        if($this->idevent == NULL)
        {
            $query = self::execute("SELECT COUNT(*) FROM event 
                                    WHERE event.idcalendar = ? && title=? && start=?", 
                                    array($this->idcalendar, $this->title, $this->start->datetime_string()));
        }
        else 
            $query = self::execute("SELECT COUNT(*) FROM event 
                                    WHERE event.idcalendar = ? && title=? && start=? && idevent!=?", 
                                    array($this->idcalendar, $this->title, $this->start->datetime_string(), $this->idevent));
        $data = $query->fetch();
        
        return $data[0] != 0;
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