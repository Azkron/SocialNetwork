<?php

require_once 'model/User.php';
require_once 'model/Calendar.php';
require_once 'model/Event.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once "framework/Tools.php";
require_once "model/Event.php";

class ControllerEvent extends Controller {

    
    public function my_planning() {
        $user = $this->get_user_or_redirect();
        $weekMod = 0;
        if(isset($_POST['weekMod']))
            $weekMod = $_POST['weekMod'];
        
        $errors = [];
        if(!Calendar::calendars_exist($user))
            $errors[] = "You must own at least one calendar before being able to create an event";
        
        (new View("my_planning"))->show(array("weekMod" => $weekMod, "errors" => $errors, "week" => Event::get_events_in_week($user, $weekMod)));
    }
    
    //page d'accueil. 
    public function index() {
        $this->my_planning();
    }
    
    public function create_event()
    {
        $user = $this->get_user_or_redirect();
        
        if (!Calendar::calendars_exist($user) || isset($_POST["cancel"]))
            $this->redirect("event", "my_planning");
        
        $title = '';
        $whole_day = '';
        $startDate = '';
        $startTime = '';
        $finishDate = '';
        $finishTime = '';
        $description = '';
        $idcalendar = NULL;
        $errors = [];
        
        if(isset($_POST["create"]))
            if (isset($_POST['title']) && isset($_POST['idcalendar']) && isset($_POST['startDate'])) 
            {
                $title = trim($_POST['title']);
                $idcalendar = $_POST['idcalendar'];
                
                $whole_day = isset($_POST['whole_day']) ? 1 : 0;

                if($_POST['startDate'] != "")
                    $startDate = $_POST['startDate'];
                else
                    $startDate = NULL;
                
                if(isset($_POST['startTime']) && $_POST['startTime'] != "")
                    $startTime = $_POST['startTime'];
                else
                    $startTime = NULL;

                if(isset($_POST['finishDate']) && $_POST['finishDate'] != "")
                    $finishDate = $_POST['finishDate'];
                else
                    $finishDate = NULL;

                if(isset($_POST['finishTime']) && $_POST['finishTime'] != "")
                    $finishTime = $_POST['finishTime'];
                else
                    $finishTime = NULL;

                if(isset($_POST['description']))
                    $description = trim($_POST['description']);
                else
                    $description = NULL;

                $errors = Event::validate($user, $title, $whole_day, $startDate, $startTime, $idcalendar, $finishDate, $finishTime, $description);

                if(count($errors) == 0)
                {
                    Event::add_event(new event($_POST["title"], $whole_day, $startDate.$startTime, 
                                $_POST['idcalendar'], $finishDate.$finishTime, $description));

                    $this->redirect("event", "my_planning");
                }
            }
        
        $calendars = Calendar::get_writable_calendars($user);
        (new View("create_event"))->show(array("calendars" => $calendars, "errors" => $errors, "title" => $title, "whole_day" => $whole_day, 
                                            "startDate" => $startDate, "startTime" => $startTime, "idcalendar" => $idcalendar, "finishDate" => $finishDate, "finishTime" => $finishTime,
                                            "description" => $description));
    }
    
    
    public function update_event()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if(isset($_POST['idevent']) && isset($_POST['weekMod']) && isset($_POST['read_only']))
        {
            if(isset($_POST['cancel']))
                $this->redirect("event", "my_planning");
            if(isset($_POST['delete']))
            {
                $this->delete();
                $this->redirect("event", "my_planning");
            }
            else if(isset($_POST['update']))
            {
                
                if (isset($_POST['title']) && isset($_POST['idcalendar']) && isset($_POST['startDate']) && isset($_POST['idevent'])) 
                {
                    $title = trim($_POST['title']);
                    $idcalendar = $_POST['idcalendar'];
                    $idevent = $_POST['idevent'];

                    $whole_day = isset($_POST['whole_day']) ? 1 : 0;

                    if($_POST['startDate'] != "")
                        $startDate = $_POST['startDate'];
                    else
                        $startDate = NULL;

                    if(isset($_POST['startTime']) && $_POST['startTime'] != "")
                        $startTime = $_POST['startTime'];
                    else
                        $startTime = NULL;

                    if(isset($_POST['finishDate']) && $_POST['finishDate'] != "")
                        $finishDate = $_POST['finishDate'];
                    else
                        $finishDate = NULL;

                    if(isset($_POST['finishTime']) && $_POST['finishTime'] != "")
                        $finishTime = $_POST['finishTime'];
                    else
                        $finishTime = NULL;

                    if(isset($_POST['description']))
                        $description = trim($_POST['description']);
                    else
                        $description = NULL;

                    $errors = Event::validate($user, $title, $whole_day, $startDate, $startTime, $idcalendar, $finishDate, $finishTime, $description);

                    if(count($errors) == 0)
                    {
                        Event::update_event(new Event($title, $whole_day, $startDate.$startTime, 
                                    $idcalendar, $finishDate.$finishTime, $description, NULL, $idevent));
                        $this->redirect("event", "my_planning");
                    }
                        
                }
                else
                    throw new Exception("Missing parameters for event update!");
            }
            
            $event = Event::get_event($_POST['idevent']);
            $event->read_only = $_POST['read_only'];
            $calendars = Calendar::get_calendars($user);
            (new View("update_event"))->show(array("event" => $event, "errors" => $errors, "weekMod" => $_POST['weekMod'], "calendars" => $calendars));
        }
        else 
            throw new Exception("Missing parameters for update event!");
    }

    
    
    private function delete() {
        if (isset($_POST["idevent"])) {
            Event::delete_event($_POST['idevent']);
        } else 
            throw new Exception("Missing Event ID");
    }    
}
