<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script type ="text/javascript" src='JS/Tools.js'></script>
        <script>
            var pseudo, pwd, pwd2;
            
             var pseudoReg = [
                {reg: /^.{3,16}$/ , msg : "The pseudo must be 3-16 characters long"},
                {reg: /^[a-zA-Z][a-zA-Z0-9]*$/, msg : "The pseudo must be composed of letters and numbers and the first character must be a number"}
            ];
            
            var pwdReg = [
                {reg: /^.{8,16}$/, msg : "The password must be 8-16 characters long"},
                {reg: /[A-Z]/, msg : "The password must conaint at least a mayus letter"},
                {reg: /\d/, msg : "The password must contain a number"},
                {reg: /['";:,.\/?\\-]/, msg : "The password must contain a punctuation character"}
            ];
            
            document.onreadystatechange = function()
            {
                if(document.readyState === 'complete')
                {
                    pseudo = document.getElementById("pseudo");
                    pwd = document.getElementById("password");
                    pwd2 = document.getElementById("passwordConfirm");
                }
            };
            
            function checkPseudo(display = true)
            {
                pass = checkField(pseudo, pseudoReg);
                
                if(display)
                    displayErrors(errors);
                
                return pass;
            }
            
            function checkPwd(display = true)
            {
                pass = checkField(pwd, pwdReg, errors);
                if(display)
                    displayErrors(errors);
                return pass;
            }
            
            function checkPwd2(display = true)
            {
                var msg = "The passwords must equal";
                pass = (pwd.value == pwd2.value);
                if(!pass)
                    addError(msg);
                else
                    eraseError(msg);
                
                if(display)
                    displayErrors(errors);
                
                return pass;
            }
            
            function validate()
            {
                pass = checkPseudo(false) && checkPwd(false) && checkPwd2(false);
                displayErrors(errors);
                return pass;
            }
        </script>
    </head>
    <body>
        <div class="title">Sign Up</div>
        <div class="menu">
            <a href="index.php">Home</a>
        </div>
        <div class="main">
            Please enter the following details to sign up :
            <br><br>
            <div class="tableForm">
                <form id="signupForm" action="main/signup" method="post" onsubmit="return validate();">
                <table>
                    <tr>
                        <td>Pseudo:</td>
                        <td><input id="pseudo" name="pseudo" type="text" size="16" value="<?= $pseudo ?>" onchange="checkPseudo();"></td>
                    </tr>
                    <tr>
                        <td>Full Name:</td>
                        <td><input id="full_name" name="full_name" type="text" size="16" value="<?= $full_name ?>"></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input id="email" name="email" type="text" size="16" value="<?= $email ?>"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input id="password" name="password" type="password" size="16" value="<?= $password ?>" onchange="checkPwd();"></td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td><input id="passwordConfirm" name="password_confirm" size="16" type="password" value="<?= $password_confirm ?>" onchange="checkPwd2();"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="btn" type="submit" value="Sign Up"></td>
                    </tr>
                </table>
                
            </form>
            <div id='errors'>
                <!-- The errors are inserted through javascript or php with displayErrors(errors) -->
            </div>
            <?php
                if(isset($errors))
                    View::print_errors($errors);
            ?>
            </div>
        </div>
    </body>
</html>