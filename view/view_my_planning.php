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
            <a href="main/welcome">Back</a>
        </div>
        <div class="main">
            <br><br>
                <table>
                    <tr>
                        <td>
                        <form class="buttonForm" action="event/my_planning" method="post">
                            <input type="hidden" name="weekMod" value="<?=$weekMod-1; ?>"/>
                            <input class="btn" type="submit" name="change_week" value="<< Previous week">
                        </form>
                        </td>
                        <td>My Planning</td>
                        <td>
                        <form class="buttonForm" action="event/my_planning" method="post">
                            <input type="hidden" name="weekMod" value="<?=$weekMod+1; ?>"/>
                            <input class="btn" type="submit" name="change_week" value="Next week >>">
                        </form>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?php $day = Date::monday($weekMod);?><?=$day->week_string()?></td>
                        <td></td>
                    </tr>
                </table>
            <table>
                <?php if (count($week) != 0): 
                            
                            for ($i = 0; $i < 7; ++$i): ?>
                <tr class="dayRow">
                    <th><?=$day->day_string()?></th>
                    <th></th>
                    <th></th>
                </tr>
                        <?php if (count($week[$i]) != 0): ?>
                            <?php foreach ($week[$i] as $event): ?>
                <tr class="eventRow">
                    <td style="color:#<?=$event->color?>">
                        <?= $event->get_time_string($day); ?>
                    </td>
                    <td style="color:#<?=$event->color?>">
                        <?= $event->title; ?>
                    </td>
                    <td>
                        <form class="buttonForm" action="event/update_event" method="post">
                            <input type="hidden" name="weekMod" value="<?= $weekMod; ?>"/>
                            <input type="hidden" name="idevent" value="<?= $event->idevent; ?>"/>
                            <input class="btn" type="submit" name="edit_event" value="Edit event">
                        </form>
                    </td>
                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                
                        <?php $day->next_day();?>
                    <?php endfor; ?>
                <?php endif; ?>
            </table>
            
            <form class="buttonForm" action="event/create_event" method="post">
                <input type="hidden" name="weekMod" value="<?= $weekMod; ?>"/>
                <input class="btn" type="submit" value="create" name="Create a calendar">
            </form>
            
            <?php
                if(isset($errors))
                    View::print_errors($errors);
            ?>
        </div>
        
    </body>
</html>