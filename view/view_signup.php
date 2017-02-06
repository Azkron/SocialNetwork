<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
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
            <form id="signupForm" action="main/signup" method="post">
                <table>
                    <tr>
                        <td>Pseudo:</td>
                        <td><input id="pseudo" name="pseudo" type="text" size="16" value="<?= $pseudo ?>"></td>
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
                        <td><input id="password" name="password" type="password" size="16" value="<?= $password ?>"></td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td><input id="passwordConfirm" name="password_confirm" size="16" type="password" value="<?= $password_confirm ?>"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="btn" type="submit" value="Sign Up"></td>
                    </tr>
                </table>
                
            </form>
            <?php
                if(isset($errors))
                    View::print_errors($errors);
            ?>
            </div>
        </div>
    </body>
</html>