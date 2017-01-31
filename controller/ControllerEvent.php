<?php

require_once 'model/Calendar.php';
require_once 'model/Event.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerEvent extends Controller {

    //page d'accueil. 
    public function index() {
        $this->profile();
    }
    
    public function edit() {
        $error = "";
        $success = "";

        if (isset($_POST['title']) && isset($_POST['whole_day']) && isset($_POST['start']) && 
            isset($_POST['finish']) && isset($_POST['description']) && isset($_POST['$idevent'])) {
            Event::update_event($_POST["title"], $_POST["whole_day"], $_POST["start"], $_POST["finish"],
                                $_POST["description"], $_POST['$idevent']);
            
            $success = "The calendar has been successfully updated.";
        }
        else
            throw new Exception("Missing parameters for calendar creation!");
        
        $this->my_events();
    }
    
    public function edit_or_delete()
    {
        if(isset($_POST["delete"]) && $_POST["delete"])
            $this->confirm_delete();
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
        
        //$this->my_calendars();
        
    }
    
    public function delete() {
        if (isset($_POST["idevent"])) {
            Event::delete_event($_POST['idevent']);
        } else 
            throw new Exception("Missing Event ID");
    }
    
    //gestion du suivi d'un membre
    public function confirm_delete() {
        if (isset($_POST["idevent"])) 
            (new View("confirm_event_delete"))->show(array("idevent" => $_POST["idevent"]));
        else 
            throw new Exception("Missing Event ID");
    }
    
    

}
