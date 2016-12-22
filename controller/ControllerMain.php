<?php

require_once 'model/Member.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMain extends Controller {

    //si l'utilisateur est conecté, redirige vers son profil.
    //sinon, produit la vue d'accueil.
    public function index() {
        if ($this->user_logged()) {
            $this->redirect("member", "profile");
        } else {
            (new View("index"))->show();
        }
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

            $error = Member::validate_login($pseudo, $password);
            if ($error === "") {
                $this->log_user(Member::get_member($pseudo));
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

        if (isset($_POST['pseudo']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $pseudo = trim($_POST['pseudo']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];


            $errors = Member::validate($pseudo, $password, $password_confirm);

            if (count($errors) == 0) {
                $member = new Member($pseudo, Tools::my_hash($password));
                Member::add_member($member);
                $this->log_user($member);
            }
        }
        (new View("signup"))->show(array("pseudo" => $pseudo, "password" => $password, "password_confirm" => $password_confirm, "errors" => $errors));
    }

}
