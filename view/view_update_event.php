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
        <div class="menu">
            <a href="index.php">Home</a>
        </div>
        <div class="main">
            <br><br>
                <form class="eventForm" action="event/update_event" method="post">
                    <table>
                        <tr>
                            <td>Title:</td>
                            <td><input class="title" name="title" type="text" value="<?= $event->title; ?>"></td>
                        </tr>
                        <tr>
                            <td>Calendar:</td>
                            <td>
                                <select>
                                    <?php
                                    if (count($calendars) != 0) 
                                        foreach($calendars as $value)
                                            echo '<option value="'.$value.'">'.$value.'</option>';                                   
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Description:</td> 
                            <td><textarea name="description" rows=4 cols=50 value="<?= $event->description; ?>"></textarea></td>
                        </tr>
                        <tr>
                            <td>Start time:</td> 
                            <td><input class="datetime" name="startTime" type="datetime-local" value=""></td>
                        </tr>
                        <tr>
                            <td>Finish time:</td>
                            <td><input class="datetime" name="finishTime" type="datetime-local"  value=""></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="whole_day[]" value="1">Whole day event</td>
                        </tr>
                        <tr>
                            <td>
                                <input class="btn" type="submit" name="delete" value="Delete">
                                <input class="btn" type="submit" name = "cancel" value="Cancel">  
                                <input class="btn" type="submit" name="edit" value="Update">                         
                            </td>
                        </tr>
                    </table>
                </form>
        </div>
    </body>
</html>