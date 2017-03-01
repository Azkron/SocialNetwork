<?php

require_once 'model/User.php';
require_once 'model/Calendar.php';
require_once 'model/Share.php';
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
            
            (new View("my_calendars"))->show(array("calendars" => Calendar::get_calendars($user),
                                                   "errors" => $errors));
        }
    }
    
    public function sharing_settings()
    {
        $user = $this->get_user_or_redirect();
        $errors = []; 
        $idcalendar = NULL;
        if (isset($_POST['idcalendar'])) 
        {
            $idcalendar = $_POST['idcalendar'];     
            if(isset($_POST["edit"])){
                $errors = $this->edit_share($idcalendar);              
            }
            else if (isset($_POST["delete"])) {
                $errors = $this->delete_share($idcalendar);
            }
            else if (isset($_POST["share_calendar"])) {
                $errors = $this->create_share();      
            }            
        }
        else
            throw new Exception("Missing parameters for showing share page!");
        
        (new View("sharing_settings"))->show(array("calendar" => Calendar::get_calendar($idcalendar), 
                                                   "shared_users" => Share::get_shared_users($idcalendar, $user),
                                                   "not_shared_users" => Share::get_not_shared_users($user, $idcalendar),
                                                   "errors" => $errors)); 
    }
    
    private function edit_share($idcalendar) 
    {
        $errors = [];        
        if (isset($_POST['iduser'])) {
            $iduser = $_POST['iduser'];
            $read_only = isset($_POST['write']) ? 0 : 1;
            
            Share::update_share($iduser, $idcalendar, $read_only);
        }
        else
            $errors = "Missing parameters for calendar edition!";
        
        return $errors;
        
    }
    
    private function delete_share($idcalendar) 
    {
        $errors = [];
        if (isset($_POST['iduser'])) {
            $iduser = $_POST['iduser'];
            
            Share::delete_share($iduser, $idcalendar);
        }
        else
            $errors = "Missing parameters for calendar deletion!";
        
        return $errors;
    }
    
    private function create_share() 
    {
        $errors = [];
        $pseudo = NULL;
        if (isset($_POST['pseudo'])) {
            foreach($_POST['pseudo'] as $index => $valeur) {
                if (is_string($valeur)) {
                    $index = trim($valeur);
                    $pseudo = $index;                   
                }
            }
        }
        $read_only = isset($_POST['write']) ? 0 : 1;
        $idcalendar = $_POST['idcalendar'];
        $errors = Share::validate_share($pseudo);

        if (count($errors) == 0)
            Share::add_share($pseudo, $idcalendar, $read_only);

        return $errors;
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
