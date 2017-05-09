<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="JS/jquery-3.1.1.min.js" type="text/javascript"></script>
        <script src="JS/jquery-validation-1.16.0/jquery.validate.min.js" type="text/javascript"></script>
        <script>
            $.validator.addMethod("regex", function (value, element, pattern) {
                if (pattern instanceof Array) {
                    for(p of pattern) {
                        if (!p.test(value))
                            return false;
                    }
                    return true;
                } else {
                    return pattern.test(value);
                }
            }, "Please enter a valid input.");
            
            $(function () {
                $('#signupForm').validate({
                    rules: {
                        pseudo: {
                            remote: {
                                url: 'main/pseudo_available_service',
                                type: 'post',
                                data:  {
                                    pseudo: function() { 
                                        return $("#pseudo").val();
                                    }
                                }
                            },
                            required: true,
                            minlength: 3,
                            maxlength: 16,
                            regex: /^[a-zA-Z][a-zA-Z0-9]*$/
                        },
                        password: {
                            required: true,
                            minlength: 8,
                            maxlength: 16,
                            regex: [/[A-Z]/, /\d/, /['";:,.\/?\\-]/]
                        },
                        password_confirm: {
                            required: true,
                            minlength: 8,
                            maxlength: 16,
                            equalTo: "#password",
                            regex: [/[A-Z]/, /\d/, /['";:,.\/?\\-]/]
                        }
                    },
                    messages: {
                        pseudo: {
                            remote: 'this pseudo is already taken',
                            required: 'required',
                            minlength: 'minimum 3 characters',
                            maxlength: 'maximum 16 characters',
                            regex: 'bad format for pseudo'
                        },
                        password: {
                            required: 'required',
                            minlength: 'minimum 8 characters',
                            maxlength: 'maximum 16 characters',
                            regex: 'bad password format'
                        },
                        password_confirm: {
                            required: 'required',
                            minlength: 'minimum 8 characters',
                            maxlength: 'maximum 16 characters',
                            equalTo: 'must be identical to password above',
                            regex: 'bad password format'
                        }
                    }
                });
                
                $("input:text:first").focus();
            });
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
                <form id="signupForm" action="main/signup" method="post">
                <table>
                    <tr>
                        <td>Pseudo:</td>
                        <td><input id="pseudo" name="pseudo" type="text" size="16" value="<?= $pseudo ?>" required></td>
                    </tr>
                    <tr>
                        <td>Full Name:</td>
                        <td><input id="full_name" name="full_name" type="text" size="16" value="<?= $full_name ?>"></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input id="email" name="email" type="email" size="16" value="<?= $email ?>" required></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input id="password" name="password" type="password" size="16" value="<?= $password ?>" required></td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td><input id="passwordConfirm" name="password_confirm" size="16" type="password" value="<?= $password_confirm ?>" required></td>
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