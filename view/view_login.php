<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Log In</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Log In</div>
        <div class="menu">
            <a href="main/index">Home</a>
            <a href="main/signup">Sign Up</a>
        </div>
        <div class="main">
            <div class="tableForm">
            <form action="main/login" method="post">
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
                <div class='errors'><br><br><?= $error ?></div>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>


