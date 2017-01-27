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
            Calendar.update_calendar($_POST['description'], 
                       $_POST['color'], $_POST['idcalendar']);
            
            $success = "The calendar has been successfully updated.";
        }
        
        $this->my_calendars();
    }
    
    public function edit_or_delete()
    {
        
    }
    
    public function create_calendar()
    {
        $user = $this->get_user_or_redirect();
        $id = $user->iduser;
        if (isset($_POST["color"]) && isset($_POST["description"]))
            Calendar::add_calendar(new calendar($_POST["description"], str_replace("#","",$_POST["color"])),
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
            Calendar.delete_calendar($_POST['idcalendar']);
            $this->my_calendars();
        } else 
            throw new Exception("Missing ID");
    }
    
    //gestion du suivi d'un membre
    public function confirm_delete() {
        if (isset($_POST["idcalendar"])) 
            (new View("confirm_calendar_delete"))->show(array("idcalendar" => $idcalendar));
        else 
            throw new Exception("Missing ID");
    }
    

}
