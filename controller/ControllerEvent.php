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
        $calendars = Calendar::get_calendars($user);
        (new View("create_event"))->show(array("calendars" => $calendars));
    }
    
    public function create()
    {
        if (isset($_POST['title']) && isset($_POST['idcalendar']) && isset($_POST['start'])) 
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
            
            Event::add_event(new event($_POST["title"], $whole_day, $_POST["start"], 
                        $_POST['idcalendar'], $finish, $description));
        }
                
        $this->my_planning();
    }
    
    public function create_or_cancel()
    {
        if(isset($_POST["create"]))
            $this->create();
        else 
            $this->my_planning();
    }
    
    public function update_event()
    {
        $user = $this->get_user_or_redirect();
        if(isset($_POST['idevent']) && isset($_POST['weekMod']))
        {
            $event = Event::get_event($_POST['idevent']);
            $calendars = Calendar::get_calendars($user);
            if($event != NULL)
                (new View("update_event"))->show(array("event" => $event, "weekMod" => $_POST['weekMod'], "calendars" => $calendars));
        }
    }
    
    public function delete_cancel_update()
    {
        if(isset($_POST['update']))
            $this->update();
        else if(isset($_POST['delete']))
            $this->delete();
        else
            $this->my_planning();
    }
    
    
    public function update() {
        $error = "";
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
            
            Event::update_event(new Event($_POST["title"], $whole_day, $_POST["start"], 
                        $_POST['idcalendar'], $finish, $description, NULL, $_POST['idevent']));
        
            $success = "The event has been successfully updated.";
        }
        else
            throw new Exception("Missing parameters for event update!");
        
        $this->my_planning();
    }
    
    
    
    public function delete() {
        if (isset($_POST["idevent"])) {
            Event::delete_event($_POST['idevent']);
            $this->my_planning();
        } else 
            throw new Exception("Missing Event ID");
    }    
}
