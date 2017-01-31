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
        <div class="menu">
            <a href="index.php">Home</a>
        </div>
        <div class="main">
            <br><br>
                <form class="calendarForm" action="event/create_event" method="post">
                    <td>
                        <p>Title: <input class="title" name="title" type="text" size="16" value=""></p>
                    </td>
                    <td>
                        <p>Calendar: <input class="calendar" name="calendar" type="calendar" value=""></p>
                    </td>
                    <td>
                        <p>Description: <input class="description" name="description" type="text" size="16" value=""></p>
                    </td>
                    <td>
                        <p>Start time: <input class="DateTime" name="startTime" type="text" size="16" value=""></p>
                    </td>
                    <td>
                        <p>Finish time: <input class="DateTime" name="finishTime" type="text" size="16" value=""></p>
                    </td>
                    <td>
                        <p><input type="checkbox" name="whole_day[]" value="1">Whole day event</p>
                    </td>
                    <td>
                        <input class="btn" type="submit" name = "create" value="Create">
                        <input class="btn" type="submit" name = "cancel" value="Cancel">
                    </td>
                </form>
        </div>
    </body>
</html>