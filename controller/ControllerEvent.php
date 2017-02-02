<?php

require_once 'model/User.php';
require_once 'model/Calendar.php';
require_once 'model/Event.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerEvent extends Controller {

    
    public function my_planning() {
        $user = $this->get_user_or_redirect();
        $monday = 0;
        if(isset($_POST['monday']))
            $monday = $_POST['monday'];
        else
            $monday = strtotime('monday this week');
        
        (new View("my_planning"))->show(array("monday" => $monday, "events" => Event::events_in_week($user, $monday)));
    }
    
    //page d'accueil. 
    public function index() {
        $this->my_planning();
    }
    
    public function create_event()
    {
        $user = $this->get_user_or_redirect();
        $id = $user->iduser;
        if (isset($_POST['title']) && isset($_POST['whole_day']) && isset($_POST['start']) && 
            isset($_POST['finish']) && isset($_POST['description'])) 
                Event::add_event(new event($_POST["title"], $_POST["whole_day"], $_POST["start"], $_POST["finish"],
                                           $_POST["description"], $_POST['$idevent']), $user);
        $this->my_planning();
    }
    
    public function edit() {
        $error = "";
        $success = "";

        if (isset($_POST['title']) && isset($_POST['whole_day']) && isset($_POST['start']) && 
            isset($_POST['finish']) && isset($_POST['description']) && isset($_POST['$idevent'])) {
            Event::update_event($_POST["title"], $_POST["whole_day"], $_POST["start"], $_POST["finish"],
                                $_POST["description"], $_POST['$idevent']);
            
            $success = "The event has been successfully updated.";
        }
        else
            throw new Exception("Missing parameters for event update!");
        
        $this->my_planning();
    }
    
    public function edit_or_delete()
    {
        if(isset($_POST["delete"]) && $_POST["delete"])
            $this->delete();
        else if(isset($_POST["edit"]) && $_POST["edit"])
            $this->edit();       
    }
    
    public function delete_or_cancel()
    {
        if(isset($_POST["delete"]) && $_POST["delete"])
            $this->delete();
        // No need to check for cancel as outcome is to go to  my_calendars anyway
        //else if(isset($_POST["cancel"]) && $_POST["cancel"])
            //$this->edit();
        
        $this->my_planning();
        
    }
    
    public function delete() {
        if (isset($_POST["idevent"])) {
            Event::delete_event($_POST['idevent']);
        } else 
            throw new Exception("Missing Event ID");
    }    
}
