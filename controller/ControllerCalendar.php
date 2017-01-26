<?php

require_once 'model/Member.php';
require_once 'model/Message.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMember extends Controller {
    
    const UPLOAD_ERR_OK = 0;

    //gestion de l'édition du profil
    public function edit_profile() {
        $member = $this->get_user_or_redirect();
        $error = "";
        $success = "";

        // Il est néces

        if (isset($_POST['profile'])) {
            $profile = $_POST['profile'];
            $member->profile = $profile;
            $member->update();
            $success = "Your profile has been successfully updated.";
        }
        (new View("edit_profile"))->show(array("member" => $member, "error" => $error, "success" => $success));
    }

    //page d'accueil. 
    public function index() {
        $this->my_calendars();
    }

    //profil de l'utilisateur connecté ou donné
    public function my_calendars() {
        $user = $this->get_user_or_redirect();
        (new View("MyCalendars"))->show(Calendar::get_calendars($user));
    }

    //gestion du suivi d'un membre
    public function delete() {
        $member = $this->get_user_or_redirect();
        if (isset($_POST["follow"]) && $_POST["follow"] != "") {
            $followee_pseudo = $_POST["follow"];
            $followee = Member::get_member($followee_pseudo);
            $member->follow($followee);
            $this->redirect("member", "members");
        } else {
            throw new Exception("Missing ID");
        }
    }
    
    //gestion du suivi d'un membre
    public function confirm_delete() {
        $member = $this->get_user_or_redirect();
        if (isset($_POST["follow"]) && $_POST["follow"] != "") {
            $followee_pseudo = $_POST["follow"];
            $followee = Member::get_member($followee_pseudo);
            $member->follow($followee);
            $this->redirect("member", "members");
        } else {
            throw new Exception("Missing ID");
        }
    }
    
    public function edit() {
        $member = $this->get_user_or_redirect();
        if (isset($_POST["follow"]) && $_POST["follow"] != "") {
            $followee_pseudo = $_POST["follow"];
            $followee = Member::get_member($followee_pseudo);
            $member->follow($followee);
            $this->redirect("member", "members");
        } else {
            throw new Exception("Missing ID");
        }
    }

}
