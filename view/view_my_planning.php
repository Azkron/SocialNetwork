<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>My Planning</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">My Planning</div>
        <div class="menu">
            <a href="index.php">Home</a>
        </div>
        <div class="main">
            <br><br>
                <table>
                    <tr>
                        <td>Previous week</td>
                        <td>My Planning</td>
                        <td>Next week</td>
                    </tr>
                </table>
            <table>
                <?php if (count($week) != 0): 
                            $day = Date::monday($weekMod);
                            for ($i = 0; $i < 7; ++$i): ?>
                                <?php $day->nextDay();?>
                <tr class="dayRow">
                    <th><?=$day->day_string()?></th>
                    <th></th>
                    <th></th>
                </tr>
                        <?php if (count($week[$i]) != 0): ?>
                            <?php foreach ($week[$i] as $event): ?>
                <tr class="eventRow">
                    <td>
                        <?= $event->get_hour_string; ?>
                    </td>
                    <td>
                        <?= $event->description; ?>
                    </td>
                    <td>
                        <form class="buttonForm" action="event/update_event" method="post">
                            <input type="hidden" name="monday" value="<?= $monday; ?>"/>
                            <input type="hidden" name="idevent" value="<?= $event->idevent; ?>"/>
                            <input class="btn" type="submit" name="edit_event" value="Edit event">
                        </form>
                    </td>
                </tr>
                
                
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endfor; ?>
                <?php endif; ?>
            </table>
            
            <form class="buttonForm" action="event/create_event" method="post">
                <input type="hidden" name="monday" value="<?= $monday; ?>"/>
                <input class="btn" type="submit" value="create" name="Create a calendar">
            </form>
        </div>
    </body>
</html>