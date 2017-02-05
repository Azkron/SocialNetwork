<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>My Calendars</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">My Calendars</div>
        <div class="menu">
            <a href="index.php">Home</a>
        </div>
        <div class="main">
            <br><br>
            
            <div id="calendarDiv"></div>
            <div class="calendarHeader">
                <span class="calendarDescription">Description</span>
                <span class="calendarColor">Color</span>
                <span class="calendarActions">Actions</span>
            </div>
            
                <?php if (count($calendars) != 0): ?>
                    <?php foreach ($calendars as $calendar): ?>
            <div class="calendarRow">
                <form class="calendarForm" action="calendar/my_calendars" method="post">
                    <span class="calendarDescription">
                        <input class="description" name="description" type="text" size="16" value="<?= $calendar->description; ?>">
                    </span>
                    <span class="calendarColor">
                        <input class="color" name="color" type="color" <?php $color = $calendar->color; echo "value=\"#$color\""?>>
                    </span>
                    <span class="calendarActions">
                        <input type="hidden" name="idcalendar" value="<?= $calendar->idcalendar; ?>"/>
                        <input class="btn" type="submit" name="edit" value="Edit">
                        <input class="btn" type="submit" name="delete" value="Delete">
                    </span>
                </form>         
            </div>         
                    <?php endforeach; ?>
                <?php endif; ?>
            <div class="calendarRow">
                <form class="calendarForm" action="calendar/my_calendars" method="post">
                    <span class="calendarDescription">
                        <input class="description" name="description" type="text" size="16" value="">
                    </span>
                    <span class="calendarColor">
                        <input class="color" name="color" type="color" value="">
                    </span>
                    <span class="calendarActions">
                        <input class="btn" type="submit" value="Create a calendar" name="create">
                    </span>
                </form>         
            </div>       
            
            <?php
                if(isset($errors))
                    View::print_errors($errors);
            ?>
        </div>
    </body>
</html>