<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Log In</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="Lib/jquery-3.1.1.min.js" type="text/javascript"></script>
        <script src="Lib/jquery-validation-1.16.0/jquery.validate.min.js" type="text/javascript"></script>
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
                //$.cookie('defaultViewCookie', null, { path: '/' });
                //$.cookie('defaultDateCookie', null, { path: '/' });

                
                $('#loginForm').validate({
                    rules: {
                        pseudo: {
                            remote: {
                                url: 'main/login_pseudo_available_service',
                                type: 'post',
                                data:  {
                                    pseudo: function() {
                                        return $("#pseudo").val();
                                    }
                                }
                            },
                            required: true,
                            minlength: 3,
                            maxlength: 32,
                            regex: /^[a-zA-Z][a-zA-Z0-9]*$/
                        }
                    },
                    messages: {
                        pseudo: {
                            remote: 'this pseudo is not present in the database',
                            required: 'required',
                            minlength: 'minimum 3 characters',
                            maxlength: 'maximum 32 characters',
                            regex: 'bad format for pseudo'
                        }
                    }
                });
                
                $("input:text:first").focus();
            });
        </script>
    </head>
    <body>
        <div class="title">Log In</div>
        <div class="menu">
            <a href="main/index">Home</a>
            <a href="main/signup">Sign Up</a>
        </div>
        <div class="main">
            <div class="tableForm">
            <form id="loginForm" action="main/login" method="post">
                <table>
                    <tr>
                        <td>Pseudo:</td>
                        <td><input id="pseudo" name="pseudo" type="text" value="<?= $pseudo ?>"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input id="password" name="password" type="password" value="<?= $password ?>"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class = "btn" type="submit" value="Log In"></td>
                    </tr>
                </table>
                
            </form>
            <?php if ($error): ?>
                <div class='errors'><?= $error ?></div>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>


