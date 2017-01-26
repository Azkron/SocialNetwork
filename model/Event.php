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
    public $idcalendar;
    

    public function __construct($start, $whole_day, $title , $idcalendar, $idevent = NULL, $finish = NULL, $description = NULL) {
        $this->idevent = $idevent;
        $this->start = $start;
        $this->finish = $finish;
        $this->whole_day = $whole_day;
        $this->title = $title;
        $his->description = $description;
        $this->idcalendar = $idcalendar; 
    }
/*
    public function write_message($message) {
        return Message::add_message($message);
    }

    public function delete_message($message) {
        return $message->delete($this);
    }

    public function get_messages() {
        return Message::get_messages($this);
    }
*/
    public static function view_event($description) {
        $query = self::execute("SELECT *
                                FROM event 
                                WHERE description = ?", array($description));
        $data = $query->fecth();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new event( $data["start"], $data["whole_day"], $data["title"], 
                              $data["idcalendar"], $data["idevent"], $data["finish"], 
                              $data["description"]);
        }
        
    }
    
    public function add_event() {
        self::execute("INSERT INTO event(start,finish,whole_day,title,description,idcalendar)
                       VALUES(:idevent,:start,:finish,:whole_day,:title,:description,:idcalendar)", 
                       array('start' => $event->start, 
                              'finish' => $event->finish,
                              'whole_day'=> $event->whole_day,
                              'title' => $event->title, 
                              'description'=>$event->description, 
                              'idcalendar'=>$event->idcalendar));
        $event->idevent = self::lastInsertId();
        
    }

}