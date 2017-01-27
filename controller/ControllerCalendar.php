<?php

require_once 'model/User.php';
require_once 'model/Calendar.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerCalendar extends Controller {
    
    const UPLOAD_ERR_OK = 0;

    public function edit() {
        $error = "";
        $success = "";

        if (isset($_POST['description']) && 
            isset($_POST['color']) && 
            isset($_POST['idcalendar'])) {
            Calendar::update_calendar($_POST['description'], 
                       $this->prepare_color($_POST["color"]), $_POST['idcalendar']);
            
            $success = "The calendar has been successfully updated.";
        }
        else
            throw new Exception("Missing parameters for calendar creation!");
        
        $this->my_calendars();
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
        
        $this->my_calendars();
        
    }
    
    public function create_calendar()
    {
        $user = $this->get_user_or_redirect();
        $id = $user->iduser;
        if (isset($_POST["color"]) && isset($_POST["description"]))
            Calendar::add_calendar(new calendar($_POST["description"], $this->prepare_color($_POST["color"])),
                                    $user);
        $this->my_calendars();
    }
 
    public function index() {
        $this->my_calendars();
    }

    public function my_calendars() {
        $user = $this->get_user_or_redirect();
        (new View("my_calendars"))->show(array("calendars" => Calendar::get_calendars($user)));
    }

    public function delete() {
        if (isset($_POST["idcalendar"])) {
            Calendar::delete_calendar($_POST['idcalendar']);
        } else 
            throw new Exception("Missing Calendar ID");
    }
    
    //gestion du suivi d'un membre
    public function confirm_delete() {
        if (isset($_POST["idcalendar"])) 
            (new View("confirm_calendar_delete"))->show(array("idcalendar" => $_POST["idcalendar"]));
        else 
            throw new Exception("Missing Calendar ID");
    }
    
    public function prepare_color($color)
    {
        return str_replace("#","",$color);
    }

}
