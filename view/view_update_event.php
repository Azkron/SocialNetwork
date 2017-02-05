<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Update event</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Update event</div>
        <div class="main">
            <br><br>
                <form class="eventForm" action="Event/update_event" method="post">
                    <table>
                        <tr>
                            <td>Title:</td>
                            <td><input class="title" name="title" type="text" value="<?=$event->title?>"></td>
                        </tr>
                        <tr>
                            <td>Calendar:</td>
                            <td>
                                <select name="idcalendar">
                                    <?php
                                    if (count($calendars) != 0) 
                                        foreach($calendars as $calendar)
                                        {
                                            $selected = '';
                                            if($calendar->idcalendar == $event->idcalendar)
                                                $selected = 'selected="selected"';
                                            echo '<option '.$selected.' value="'.$calendar->idcalendar.'" style="color:#'.$calendar->color.'">'.$calendar->description.'</option>';
                                        }                                   
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Description:</td> 
                            <td><textarea name="description" rows=4 cols=50><?=$event->description ?></textarea></td>
                        </tr>
                        <tr>
                            <td>Start time:</td> 
                            <td><input class="datetime" name="start" type="datetime-local"  <?php echo 'value="'.$event->start->date_input_string().'"'; ?>></td>
                        </tr>
                        <tr>
                            <td>Finish time:</td>
                            <td><input class="datetime" name="finish"  type="datetime-local" <?php if($event->finish != NULL) echo 'value="'.$event->finish->date_input_string().'"'; ?>></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="whole_day" value="1" <?php if($event->whole_day)echo "checked"; ?>>Whole day event</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" name="weekMod" value="<?= $weekMod; ?>"/>
                                <input type="hidden" name="idevent" value="<?= $event->idevent; ?>"/>
                                <input class="btn" type="submit" name = "delete" value="Delete"> 
                                <input class="btn" type="submit" name = "cancel" value="Cancel"> 
                                <input class="btn" type="submit" name = "update" value="Update">
                            </td>
                        </tr>                                          
                    </table>
                </form>
        </div>
    </body>
</html>