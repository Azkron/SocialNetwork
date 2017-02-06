<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Create event</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Create event</div>
        <div class="main">
            <br><br>
            <div class="tableForm">
                <form class="eventForm" action="Event/create_event" method="post">
                    <table>
                        <tr>
                            <td>Title:</td>
                            <td><input name="title" type="text" value="<?=$title?>"></td>
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
                                            if($calendar->idcalendar == $idcalendar)
                                                $selected = 'selected="selected"';
                                            echo '<option '.$selected.' value="'.$calendar->idcalendar.'" style="color:#'.$calendar->color.'">'.$calendar->description.'</option>';  
                                        }                                 
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Description:</td> 
                            <td><textarea name="description" rows=4 cols=50 ><?=$description?></textarea></td>
                        </tr>
                        <tr>
                            <td>Start time:</td> 
                            <td><input class="datetime" name="start" type="datetime-local" <?php if($start != NULL) echo 'value="'.$start.'"'; ?>></td>
                        </tr>
                        <tr>
                            <td>Finish time:</td>
                            <td><input class="datetime" name="finish"  type="datetime-local" <?php if($finish != NULL) echo 'value="'.$finish.'"'; ?>></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="whole_day" value="1" <?php if($whole_day == 1)echo "checked"; ?>>Whole day event</td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <input class="btn" type="submit" name = "create" value="Create">
                                <input class="btn" type="submit" name = "cancel" value="Cancel"> 
                            </td>
                        </tr>                                          
                    </table>
                </form>
            
            <?php
                if(isset($errors))
                    View::print_errors($errors);
            ?>
             </div>
        </div>
    </body>
</html>