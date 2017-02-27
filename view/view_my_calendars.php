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
            <a href="index.php">Back</a>
        </div>
        <div class="main">
            <br><br>
            
            <div id="calendars">
            <div class="calendarHeader">
                <div class="calendarDescriptionHeader">Description</div>
                <div class="calendarColorheader">Color</div>
                <div class="calendarActionsHeader">Actions</div>
            </div>
            
                <?php if (count($calendars) != 0): ?>
                    <?php foreach ($calendars as $calendar): ?>
            <div class="calendarRow">
                <form class="calendarForm" action="calendar/my_calendars" method="post">
                    <div class="calendarDescription">
                        <input class="description" name="description" type="text" size="16" <?php echo 'style="color:#'.$calendar->color.'"' ?> value="<?= $calendar->description; ?>">
                    </div>
                    <div class="calendarColor">
                        <input class="color" name="color" type="color" <?php $color = $calendar->color; echo "value=\"#$color\""?>>
                    </div>
                    <div class="calendarActions">
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