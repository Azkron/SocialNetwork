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
        <div class="title">Welcome <?= $userPseudo ?>!</div>
        <div class="menu">
            <a href="event/my_planning">My Planning</a>
            <a href="calendar/my_calendars">My Calendars</a>
            <a href="main/logout">Log Out</a>
        </div>
    </body>
</html>


