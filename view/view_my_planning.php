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
                        <td><h1>My Planning</h1></td>
                        <td>
                        <form class="buttonForm" action="event/my_planning" method="post">
                            <input type="hidden" name="weekMod" value="<?=$weekMod+1; ?>"/>
                            <input class="btn" type="submit" name="change_week" value="Next week >>">
                        </form>
                        </td>
                </table>
                        <h2><?php $day = Date::monday($weekMod);?><?=$day->week_string()?></h2>
            <table>
                
            <div class="events">
                <?php if (count($week) != 0): 
                    for ($i = 0; $i < 7; ++$i): ?>

                <div class="eventHeader">
                    <div class="eventHour"><?=$day->day_string()?></div>
                </div>
                        <?php if (count($week[$i]) != 0): ?>
                            <?php foreach ($week[$i] as $event): ?>

                <div class="eventRow">
                    <form class="buttonForm" action="event/update_event" method="post">
                        <div style="color:#<?=$event->color?>" class="eventHour">
                            <?= $event->get_time_string($day); ?>
                        </div>
                        <div style="color:#<?=$event->color?>" class="eventTitle">
                            <?= $event->title; ?>
                        </div>
                        <div class="eventEdit">
                                <input type="hidden" name="weekMod" value="<?= $weekMod; ?>"/>
                                <input type="hidden" name="idevent" value="<?= $event->idevent; ?>"/>
                                <input class="btn" type="submit" name="edit_event" value="Edit event">
                        </div>
                    </form>
                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php $day->next_day();?>

                    <?php endfor; ?>
                <?php endif; ?>

                <form class="buttonForm" action="event/create_event" method="post">
                    <input type="hidden" name="weekMod" value="<?= $weekMod; ?>"/>
                    <input class="btn" type="submit" value="create" name="create">
                </form>

                <?php
                    if(isset($errors))
                        View::print_errors($errors);
                ?>
            </div>
                

        </div>
        
    </body>
</html>