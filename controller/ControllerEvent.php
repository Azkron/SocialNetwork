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
        if(!self::calendars_exist($user))
            $errors[] = "You must own at least one calendar before being able to create an event";
        
        (new View("my_planning"))->show(array("weekMod" => $weekMod, "errors" => $errors, "week" => Event::get_events_in_week($user, $weekMod)));
    }
    
    //page d'accueil. 
    public function index() {
        $this->my_planning();
    }
    
    private static function calendars_exist($user)
    {
        return Calendar::calendar_count($user) != 0;
    }
    
    public function create_event()
    {
        $user = $this->get_user_or_redirect();
        
        
        if (!self::calendars_exist($user) || isset($_POST["cancel"]))
            $this->redirect("event", "my_planning");
        
        $title = '';
        $whole_day = '';
        $start = '';
        $finish = '';
        $description = '';
        $idcalendar = NULL;
        $errors = [];
        
        
        if(isset($_POST["create"]))
            if (isset($_POST['title']) && isset($_POST['idcalendar']) && isset($_POST['start'])) 
            {
                $title = trim($_POST['title']);
                $idcalendar = $_POST['idcalendar'];
                
                $whole_day = isset($_POST['whole_day']) ? 1 : 0;

                if($_POST['start'] != "")
                    $start = $_POST['start'];
                else
                    $start = NULL;

                if(isset($_POST['finish']) && $_POST['finish'] != "")
                    $finish = $_POST['finish'];
                else
                    $finish = NULL;

                if(isset($_POST['description']))
                    $description = trim($_POST['description']);
                else
                    $description = NULL;

                $errors = Event::validate($user, $title, $whole_day, $start, $idcalendar, $finish, $description);

                if(count($errors) == 0)
                {
                    Event::add_event(new event($_POST["title"], $whole_day, $start, 
                                $_POST['idcalendar'], $finish, $description));

                    $this->redirect("event", "my_planning");
                }
            }
        
        $calendars = Calendar::get_writable_calendars($user);
        (new View("create_event"))->show(array("calendars" => $calendars, "errors" => $errors, "title" => $title, "whole_day" => $whole_day, 
                                            "start" => $start, "idcalendar" => $idcalendar, "finish" => $finish, "description" => $description));
    }
    
    
    public function update_event()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if(isset($_POST['idevent']) && isset($_POST['weekMod']))
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
                
                if (isset($_POST['title']) && isset($_POST['idcalendar']) && isset($_POST['start']) && isset($_POST['idevent'])) 
                {
                    $title = trim($_POST['title']);
                    $idcalendar = $_POST['idcalendar'];
                    $idevent = $_POST['idevent'];

                    $whole_day = isset($_POST['whole_day']) ? 1 : 0;

                    if($_POST['start'] != "")
                        $start = $_POST['start'];
                    else
                        $start = NULL;

                    if(isset($_POST['finish']) && $_POST['finish'] != "")
                        $finish = $_POST['finish'];
                    else
                        $finish = NULL;

                    if(isset($_POST['description']))
                        $description = trim($_POST['description']);
                    else
                        $description = NULL;

                    $errors = Event::validate($user, $title, $whole_day, $start, $idcalendar, $finish, $description, $idevent);

                    if(count($errors) == 0)
                    {
                        Event::update_event(new Event($_POST["title"], $whole_day, $_POST["start"], 
                                    $_POST['idcalendar'], $finish, $description, NULL, $_POST['idevent']));
                        $this->redirect("event", "my_planning");
                    }
                }
                else
                    throw new Exception("Missing parameters for event update!");
            }
            else if(isset($_POST['read_only']))
            {
                $event = Event::get_event($_POST['idevent']);
                $event->read_only = $_POST['read_only'];
                $calendars = Calendar::get_calendars($user);
                (new View("update_event"))->show(array("event" => $event, "errors" => $errors, "weekMod" => $_POST['weekMod'], "calendars" => $calendars));
            }
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
