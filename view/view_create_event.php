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
                <form class="eventForm" action="Event/create_event" method="post">
                    <table>
                        <tr>
                            <td>Title:</td>
                            <td><input class="title" name="title" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td>Calendar:</td>
                            <td>
                                <select name="idcalendar">
                                    <?php
                                    if (count($calendars) != 0) 
                                        foreach($calendars as $calendar)
                                            echo '<option value="'.$calendar->idcalendar.'" style="color:#'.$calendar->color.'">'.$calendar->description.'</option>';                                   
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Description:</td> 
                            <td><textarea name="description" rows=4 cols=50 ></textarea></td>
                        </tr>
                        <tr>
                            <td>Start time:</td> 
                            <td><input class="datetime" name="start" type="datetime-local"></td>
                        </tr>
                        <tr>
                            <td>Finish time:</td>
                            <td><input class="datetime" name="finish"  type="datetime-local"></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="whole_day" value="1">Whole day event</td>
                        </tr>
                        <tr>
                            <td>
                                <input class="btn" type="submit" name = "create" value="Create">
                                <input class="btn" type="submit" name = "cancel" value="Cancel"> 
                            </td>
                        </tr>                                          
                    </table>
                </form>
        </div>
    </body>
</html>