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
    

    public function __construct($idevent = NULL, $start, $finish = NULL, $whole_day, $title , $description = NULL, $idcalendar) {
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
    public function get_events() {
        $query = self::execute("SELECT description
                      FROM event 
                      WHERE ", array("user" => $this->pseudo));
        return $query->fetchAll();
    }
    
    public function add_event() {
        self::execute("INSERT INTO event(idevent,start,finish,whole_day,title,description,idcalendar)
                       VALUES(:idevent,:start,:finish,:whole_day,:title,:description,:idcalendar)", 
                       array('idevent'=> $event->idevent, 
                              'start' => $event->start, 
                              'finish' => $event->finish,
                              'whole_day'=> $event->whole_day,
                              'title' => $event->title, 
                              'description'=>$event->description, 
                              'idcalendar'=>$event->idcalendar));
        $event->idevent = lastInsertId();
        
    }

}