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
    //public $idcalendar;
    

    public function __construct($title, $whole_day, $start, $idcalendar, $finish = NULL, 
                                $description = NULL, $color = NULL, $idevent = NULL, $read_only = NULL) 
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
    
    
    // $weekMod argument is the index of the week relative to the current week
    public static function get_events_in_week($user, $weekMod = 0) 
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
                                       $row['finish'], $row['description'], $row['color'], $row['idevent'], $row['read_only']);
            
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
    
    public static function update_event($event) 
    {
        self::execute("UPDATE event SET title = :title, whole_day = :whole_day, 
                        start = :start, idcalendar = :idcalendar,
                         finish = :finish, description = :description 
                         WHERE idevent = :idevent", 
                       array( 'title' => $event->title,
                              'whole_day'=> $event->whole_day,
                              'start' => $event->start_string(), 
                              'finish' => $event->finish_string(),                                                        
                              'description' => $event->description, 
                              'idcalendar' => $event->idcalendar,
                              'idevent' => $event->idevent));
        return true;
    }
    
    public static function delete_event($idevent) 
    {
        self::execute("DELETE FROM event WHERE idevent=? ", 
                array($idevent));
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
    
    public static function validate($user, $title, $whole_day, $startDate, $startTime, $idcalendar, $finishDate, $finishTime, $description, $idevent = NULL) 
    {
        $errors = [];
        if(strlen($title) < 1 || strlen($title) > 50 )
            $errors[] = "The event title must be between 1 and 50 characters.";
        if($description != NULL && strlen($description) > 500 )
            $errors[] = "The event title must have a maximum 500 characters.";
        
        if($startDate == NULL) 
            $errors[] = "Start date is required.";
        else
        {   
            if(!$whole_day)
            {
                if($startTime == NULL)
                    $errors[] = "Start hour is required if event is not whole day.";
                else if($finishDate != NULL)
                    if($finishTime == NULL)
                        $errors[] = "Finish hour is required if finish date selected and event is not whole day.";
                    else if($startDate.$startTime > $finishDate.$finishTime)
                        $errors[] = "Start time must be earlier than finish time.";
            }
            else if($finishDate != NULL && $startDate > $finishDate)
                        $errors[] = "Start time must be earlier than finish time.";
        }
        
        if(count($errors)==0)
            if(self::check_duplicate($user, $title, $startDate.$startTime, $idevent))
                $errors[] = "A duplicate of this event already exists with same title and starting time";
            
        return $errors;
    }
    
    public static function check_duplicate($user, $title, $start, $idevent = NULL)
    {
        $query;
        if($idevent == NULL)
        {
            $query = self::execute("SELECT * FROM event, calendar 
                                    WHERE event.idcalendar = calendar.idcalendar && calendar.iduser=? && title=? && start=?", 
                                    array($user->iduser, $title, (new Date($start))->datetime_string()));
        }
        else 
            $query = self::execute("SELECT * FROM event, calendar 
                                    WHERE event.idcalendar = calendar.idcalendar && calendar.iduser=? && title=? && start=? && idevent!=?", 
                                    array($user->iduser, $title, (new Date($start))->datetime_string(), $idevent));
        
        return $query->rowCount() != 0;
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