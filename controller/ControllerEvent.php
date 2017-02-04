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
        
        (new View("my_planning"))->show(array("weekMod" => $weekMod, "week" => Event::get_events_in_week($user, $weekMod)));
    }
    
    //page d'accueil. 
    public function index() {
        $this->my_planning();
    }
    
    public function create_event()
    {
        $user = $this->get_user_or_redirect();
        
        if (isset($_POST["cancel"]))
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
                    $description = $_POST['description'];
                else
                    $description = NULL;

                //$errors = Event::validate($title, $whole_day, $start, $idcalendar, $finish, $description);

                if(count($errors) == 0)
                {
                    Event::add_event(new event($_POST["title"], $whole_day, $start, 
                                $_POST['idcalendar'], $finish, $description));

                    $this->redirect("event", "my_planning");
                }
            }
        
        
        $calendars = Calendar::get_calendars($user);
        (new View("create_event"))->show(array("calendars" => $calendars, "errors" => $errors, "title" => $title, "whole_day" => $whole_day, 
                                            "start" => $start, "idcalendar" => $idcalendar, "finish" => $finish, "description" => $description));
    }
    
    
    public function update_event()
    {
        $user = $this->get_user_or_redirect();
        if(isset($_POST['idevent']) && isset($_POST['weekMod']))
        {
            $title = '';
            $whole_day = '';
            $start = '';
            $finish = '';
            $description = '';
            $idcalendar = NULL;
            $errors = [];
            
            if(isset($_POST['cancel']))
                $this->redirect("event", "my_planning");
            if(isset($_POST['delete']))
            {
                $this->delete();
                $this->redirect("event", "my_planning");
            }
            else if(isset($_POST['update']))
            {
                $success = "";

                if (isset($_POST['idevent']) && isset($_POST['title']) && isset($_POST['idcalendar']) && isset($_POST['start'])) 
                {
                    $whole_day = isset($_POST['whole_day']) ? 1 : 0;

                    if(isset($_POST['finish']))
                        $finish = $_POST['finish'];
                    else
                        $finish = NULL;

                    if(isset($_POST['description']))
                        $description = $_POST['description'];
                    else
                        $description = NULL;

                    if(count($errors) == 0)
                    {
                        Event::update_event(new Event($_POST["title"], $whole_day, $_POST["start"], 
                                    $_POST['idcalendar'], $finish, $description, NULL, $_POST['idevent']));

                        $success = "The event has been successfully updated.";
                        $this->redirect("event", "my_planning");
                    }
                }
                else
                    throw new Exception("Missing parameters for event update!");
            }
            
            $event = Event::get_event($_POST['idevent']);
            $calendars = Calendar::get_calendars($user);
            (new View("update_event"))->show(array("event" => $event, "weekMod" => $_POST['weekMod'], "calendars" => $calendars));
        }
        else 
            throw new Exception("Missing parameters for update event!");
    }

    
    
    public function delete() {
        if (isset($_POST["idevent"])) {
            Event::delete_event($_POST['idevent']);
        } else 
            throw new Exception("Missing Event ID");
    }    
}
