<?php

require_once 'Configuration.php';

class View {

    private $file;

    public function __construct($action) {
        $this->file = "view/view_$action.php";
    }

    //affiche la vue en lui passant les données reçues
    //sous forme de variables
    public function show($data = array()) {
        if (file_exists($this->file)) {
            extract($data);
            $web_root = Configuration::get("web_root");
            require $this->file;
        } else {
            throw new Exception("File '$this->file' does'nt exist");
        }
    }

    //erreurs
    public static function print_errors($errors) {
        if (isset($errors) && count($errors) != 0) {
            echo "<div class='errors'>";
                echo "<p>Please correct the following error(s) :</p>";
                echo "<ul>";
                    foreach ($errors as $error)
                        echo "<li>$error</li>";
                echo "</ul>";
             echo "</div>";
        }
    }
}
