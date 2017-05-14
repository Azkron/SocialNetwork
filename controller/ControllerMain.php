<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMain extends Controller {

    //si l'utilisateur est conecté, redirige vers son profil.
    //sinon, produit la vue d'accueil.
    public function index() {
        if ($this->user_logged()) {
            $this->redirect("main", "welcome");
        } else {
            (new View("index"))->show();
        }
    }
    
    public function login_pseudo_available_service(){
        $res = "true";
        if(isset($_POST["pseudo"]) && $_POST["pseudo"] != ""){
            $member = User::get_user($_POST["pseudo"]);
            if($member == null){
                $res = "false";
            }
        }
        echo $res;
    }
    
    public function pseudo_available_service(){
        $res = "true";
        if(isset($_POST["pseudo"]) && $_POST["pseudo"] != ""){
            $member = User::get_user($_POST["pseudo"]);
            if($member != null){
                $res = "false";
            }
        }
        echo $res;
    }
    
    public function email_available_service(){
        $res = "true";
        if(isset($_POST["pseudo"]) && $_POST["pseudo"] != ""){
            $member = User::get_user_by_email($_POST["pseudo"]);
            if($member != null){
                $res = "false";
            }
        }
        echo $res;
    }

    //gestion de la connexion d'un utilisateur
    public function login() {
        $pseudo = '';
        $password = '';
        $error = '';
        if (isset($_POST['pseudo']) && isset($_POST['password'])) { //note : pourraient contenir
        //des chaînes vides
            $pseudo = $_POST['pseudo'];
            $password = $_POST['password'];

            $error = User::validate_login($pseudo, $password);
            if ($error === "") {
                $this->log_user(User::get_user($pseudo));
            }
        }
        (new View("login"))->show(array("pseudo" => $pseudo, "password" => $password, "error" => $error));
    }
    

    //gestion de l'inscription d'un utilisateur
    public function signup() {
        $pseudo = '';
        $password = '';
        $password_confirm = '';
        $errors = [];
        $email = '';
        $full_name = '';

        if (isset($_POST['pseudo']) && isset($_POST['password']) && isset($_POST['password_confirm'])
                 && isset($_POST['full_name']) && isset($_POST['email'])) {
            $pseudo = trim($_POST['pseudo']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $email = $_POST['email'];
            $full_name = $_POST['full_name'];


            $errors = User::validate($pseudo, $password, $password_confirm, $email, $full_name);

            if (count($errors) == 0) {
                $user = new User($pseudo, Tools::my_hash($password), $email, $full_name);
                User::add_user($user);
                $this->log_user($user);
            }
        }
        
        (new View("signup"))->show(array("pseudo" => $pseudo, "email" => $email, "full_name" => $full_name, 
            "password" => $password, "password_confirm" => $password_confirm, "errors" => $errors));
    }
    
    public function welcome()
    {   
        $user = $this->get_user_or_redirect();
        (new View("welcome"))->show(array("userPseudo" => $user->pseudo));
    }

}
