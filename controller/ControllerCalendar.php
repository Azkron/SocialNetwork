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

    
    public function description_available_service_edit(){
        $user = $this->get_user_or_redirect();
        $res = "true";
        if(isset($_POST["description"]) && $_POST["description"] != "" && isset($_POST["idcalendar"])){
            $calendar = Calendar::get_calendar_by_description_id($_POST["description"], $_POST["idcalendar"]);
            if($calendar != null ){
                $res = "false";
            }
        }
        echo $res;
    }
    
    public function description_available_service(){
        $user = $this->get_user_or_redirect();
        $res = "true";
        if(isset($_POST["description"]) && $_POST["description"] != ""){
            $calendar = Calendar::get_calendar_by_description($_POST["description"]);
            if($calendar != null){
                $res = "false";
            }
        }
        echo $res;
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
                $errors = $this->edit();
            else if(isset($_POST["create"]))
                $errors = $this->create();
            
            (new View("my_calendars"))->show(array("calendars" => $user->get_calendars(),
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
                $errors = $this->edit_share();              
            }
            else if (isset($_POST["delete"])) {
                $errors = $this->delete_share();
            }
            else if (isset($_POST["share_calendar"])) {
                    $errors = $this->create_share();      
            }            
        }
        else
            throw new Exception("Missing parameters for showing share page!");
        
        (new View("sharing_settings"))->show(array("calendar" => Calendar::get_calendar($idcalendar), 
                                                   "shared_users" => $user->get_shared_users($idcalendar),
                                                   "not_shared_users" => $user->get_not_shared_users($idcalendar),
                                                   "errors" => $errors)); 
    }
    
    private function edit_share() 
    {
        $user = $this->get_user_or_redirect();
        
        if(!$user->check_owned_calendar($_POST['idcalendar']))
            throw new Exception("Current user does not own this calendar!");
            
        $errors = []; 
        if (isset($_POST['iduser'])) {
            $iduser = $_POST['iduser'];
            $read_only = isset($_POST['write']) ? 0 : 1;
            $idcalendar = $_POST['idcalendar'];
            
            
            if ($user->check_owned_calendar($idcalendar)) // vérifie l'utilisateur courant
            {
                $shared_user = User::get_user_by_iduser($iduser);
            
                $share = new Share($iduser, $idcalendar, $read_only, $shared_user->pseudo);

                $errors = $share->validate_share();
                if(count($errors) == 0)
                    $share->update_share();
            }     
        }
        else
            throw new Exception("Missing parameters for shared calendar edition!");
        
        return $errors;
        
    }
    
    private function create_share() 
    {
        $user = $this->get_user_or_redirect();
        
        if(!$user->check_owned_calendar($_POST['idcalendar']))
            throw new Exception("Current user does not own this calendar!");
        
        $errors = [];
        $pseudo = NULL;
        if (isset($_POST['pseudo'])) 
        {
            foreach($_POST['pseudo'] as $index => $valeur) {
                if (is_string($valeur)) {
                    $index = trim($valeur);
                    $pseudo = $index;                   
                }
            }
            $read_only = isset($_POST['write']) ? 0 : 1;
            $idcalendar = $_POST['idcalendar'];
            
            if ($user->check_owned_calendar($idcalendar)) // vérifie l'utilisateur courant
            {
                $new_shared_user = User::get_user($pseudo);

                $share =  new Share($new_shared_user->iduser, $idcalendar, $read_only, $pseudo);

                $errors = $share->validate_share();
                if (count($errors) == 0)
                    $share->add_share();
            }
        }
        else
            throw new Exception("Missing parameters for shared calendar creation!");
        
        return $errors;
    }    
    
    private function delete_share() 
    {
        $user = $this->get_user_or_redirect();
        
        if(!$user->check_owned_calendar($_POST['idcalendar']))
            throw new Exception("Current user does not own this calendar!");
        
        $errors = [];
        if (isset($_POST['iduser'])) {
            $iduser = $_POST['iduser'];
            $idcalendar = $_POST['idcalendar'];
            
            if ($user->check_owned_calendar($idcalendar)) // vérifie l'utilisateur courant
            {
                $share = Share::get_shared_calendar($iduser, $idcalendar);
                $share->delete_share();
            }
        }
        else
            throw new Exception("Missing parameters for shared calendar deletion!");
        
        return $errors;
    }
    
    private function edit() {
        $user = $this->get_user_or_redirect();
        
        if(!$user->check_owned_calendar($_POST['idcalendar']))
            throw new Exception("Current user does not own this calendar!");
        
        $errors = [];

        if (isset($_POST['description']) && 
            isset($_POST['color']) && 
            isset($_POST['idcalendar'])) 
        {
            $description = trim($_POST['description']);
            $color = $this->prepare_color($_POST['color']);
            $idcalendar = $_POST['idcalendar'];
            
            if ($user->check_owned_calendar($idcalendar)) // vérifie l'utilisateur courant
            {
                $calendar = new Calendar($description, $color, $user->iduser, $idcalendar);
                $errors = $calendar->validate();
                if(count($errors) == 0)
                    $calendar->update();
            }
        }
        else
            throw new Exception("Missing parameters for calendar edition!");
        
        return $errors;
    }
    
    private function create()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_POST["color"]) && isset($_POST["description"]))
        {
            $description = $_POST['description'];
            $color = $this->prepare_color($_POST["color"]);
            
            $calendar = new Calendar($description, $color, $user->iduser);
            $errors = $calendar->validate();

            if(count($errors) == 0)
                $calendar->add_calendar();
       
        }
        else
            throw new Exception("Missing parameters for calendar creation!");
        
        return $errors;
    }
 
    public function delete_calendar_ajax() {
        $user = $this->get_user_or_redirect();
        
        if (isset($_POST["idcalendar"])) 
        {
            $idcalendar = $_POST["idcalendar"];
            if(!$user->check_owned_calendar($idcalendar))
                throw new Exception("Current user does not own this calendar!");
            
                $calendar = Calendar::get_calendar($idcalendar);
                $calendar->delete();
        }
        else 
            throw new Exception("Missing Calendar ID");
    } 
    
    public function has_events_ajax() {
        $user = $this->get_user_or_redirect();
        
        if (isset($_POST["idcalendar"])) 
        {
            $idcalendar = $_POST["idcalendar"];
            if(!$user->check_owned_calendar($idcalendar))
                throw new Exception("Current user does not own this calendar!");
            
                $calendar = Calendar::get_calendar($idcalendar);
                echo $calendar->hasEvents() ? "true" : "false";
        }
        else 
            throw new Exception("Missing Calendar ID");
    }   

    
    //gestion du suivi d'un membre
    public function confirm_delete() {
        $user = $this->get_user_or_redirect();
        
        if(!$user->check_owned_calendar($_POST['idcalendar']))
            throw new Exception("Current user does not own this calendar!");
        
        if (isset($_POST["idcalendar"])) 
        {
            $idcalendar = $_POST["idcalendar"];
            if(isset($_POST["cancel"]))
            {
                if ($user->check_owned_calendar($idcalendar)) // vérifie l'utilisateur courant
                    $this->redirect("calendar","my_calendars");
            }
            else
            {
                $calendar = Calendar::get_calendar($idcalendar);
                if(isset($_POST["confirm"]) || !$calendar->hasEvents())
                {
                    if ($user->check_owned_calendar($idcalendar)) // vérifie l'utilisateur courant
                    {
                        $calendar->delete();
                        $this->redirect("calendar","my_calendars");
                    }
                } 
            }
            
            (new View("confirm_calendar_delete"))->show(array("idcalendar" => $idcalendar));
        }
        else 
            throw new Exception("Missing Calendar ID");
    }    
    
    private function prepare_color($color)
    {
        return str_replace("#","",$color);
    }

}
