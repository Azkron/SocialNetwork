<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Welcome!</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Log In</div>
        <div class="menu">
            <a href="planning/MyPlanning">My Planning</a>
            <a href="calendar/MyCalendars">My Calendars</a>
            <a href="main/logout">Log Out</a>
        </div>
        <div class="main">
            <h1>Welcome <?= $user ?></h1>
        </div>
    </body>
</html>


