<?php

require_once 'model/User.php';
require_once 'model/Calendar.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerCalendar extends Controller {
    
    const UPLOAD_ERR_OK = 0;

    
    public function index() {
        $this->my_calendars();
    }

    public function my_calendars() {
        $user = $this->get_user_or_redirect();
        $errors = [];
        
        if(isset($_POST["share"]))
            $this->sharing_settings();
        else if(isset($_POST["delete"]))
            $this->confirm_delete();
        else 
        {
            if(isset($_POST["edit"]))
                $errors = $this->edit($user);
            else if(isset($_POST["create"]))
                $errors = $this->create($user);
            
            (new View("my_calendars"))->show(array("calendars" => Calendar::get_calendars($user), "errors" => $errors));
        }
    }
    
    public function sharing_settings()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];     
        if (isset($_POST['idcalendar'])) 
        {
            $idcalendar = $_POST['idcalendar'];
            $shared_users = Calendar::get_shared($idcalendar);
            $not_shared_users = Calendar::get_not_shared($idcalendar);
        }
        else
            throw new Exception("Missing parameters for calendar edition!");
        
        (new View("sharing_settings"))->show(array("shared_users" => $shared_users, "not_shared_users" => $not_shared_users)); 
    }
    
    private function edit($user) {
        $errors = [];

        if (isset($_POST['description']) && 
            isset($_POST['color']) && 
            isset($_POST['idcalendar'])) 
        {
            $description = trim($_POST['description']);
            $color = $_POST['color'];
            $idcalendar = $_POST['idcalendar'];
            $errors = Calendar::validate($user, $description, $color, $idcalendar);
            
            if(count($errors) == 0)
                Calendar::update_calendar($description, $this->prepare_color($color), $idcalendar);
            
        }
        else
            throw new Exception("Missing parameters for calendar edition!");
        
        return $errors;
    }
    
    private function create($user)
    {
        $errors = [];
        if (isset($_POST["color"]) && isset($_POST["description"]))
        {
            $description = $_POST['description'];
            $color = $_POST['color'];
            $errors = Calendar::validate($user, $description, $color);
            
            if(count($errors) == 0)
                Calendar::add_calendar(new calendar($_POST["description"], $this->prepare_color($_POST["color"])),$user);
       
        }
        else
            throw new Exception("Missing parameters for calendar creation!");
        
        return $errors;
    }
 

    
    //gestion du suivi d'un membre
    public function confirm_delete() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["idcalendar"])) 
        {
            if(isset($_POST["confirm"]))
            {
                Calendar::delete_calendar($_POST['idcalendar']);
                $this->redirect("calendar","my_calendars");
            }
            else if(isset($_POST["cancel"]))
                $this->redirect("calendar","my_calendars");
            
            (new View("confirm_calendar_delete"))->show(array("idcalendar" => $_POST["idcalendar"]));
        }
        else 
            throw new Exception("Missing Calendar ID");
    }
    
    
    private function prepare_color($color)
    {
        return str_replace("#","",$color);
    }

}
