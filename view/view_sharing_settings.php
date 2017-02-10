<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sharing Settings</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Sharing Settings</div>
        <div class="menu">
            <a href="calendar/my_calendars">My Calendars</a>
        </div>
        <div class="main">
            <br><br>
            
            <div id="Sharing">
            <div class="SharingHeader">
                <div class="SharingPseudoHeader">Pseudo</div>
                <div class="SharingActions">Actions</div>
            </div>
            // pseudo, write_permissions, calendar, read_only
                <?php if (count($calendars) != 0): ?>
                    <?php foreach ($calendars as $calendar): ?>
            <div class="SharingRow">
                <form class="SharingForm" action="calendar/sharing_settings" method="post">
                    <div class="SharingPseudoHeader">
                        <input class="pseudo" name="pseudo" type="text" size="16" value="<?= $user->pseudo; ?>">
                    </div>      
                    <div class="SharingActions">                  
                        <input type="hidden" name="idcalendar" value="<?= $calendar->idcalendar; ?>"/>
                        <input class="btn" type="submit" name="edit" value="Edit">
                        <input class="btn" type="submit" name="delete" value="Delete">
                        <input class="btn" type="submit" name="share" value="Share">
                    </div>
                </form>         
            </div>         
                    <?php endforeach; ?>
                <?php endif; ?>
            <div class="calendarRow">
                <form class="calendarForm" action="calendar/my_calendars" method="post">
                    <div class="calendarDescription">
                        <input class="description" name="description" type="text" size="16" value="">
                    </div>
                    <div class="calendarColor">
                        <input class="color" name="color" type="color" value="">
                    </div>
                    <div class="calendarActions">
                        <input class="btn" type="submit" value="Create calendar" name="create">
                    </div>
                </form>         
            </div>       
            
            <?php
                if(isset($errors))
                    View::print_errors($errors);
            ?>
                </div>
        </div>
    </body>
</html>