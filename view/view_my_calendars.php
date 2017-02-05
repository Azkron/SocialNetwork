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
                <?php $check = true;?>
                <table>
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th>Color</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                <?php if (count($calendars) != 0): ?>
                    <?php foreach ($calendars as $calendar): ?>
                <form class="calendarForm" action="calendar/my_calendars" method="post">
                    <tbody>
                        <tr>
                            <td>
                                <input class="description" name="description" type="text" size="16" value="<?= $calendar->description; ?>">
                            </td>
                            <td>
                                <input class="color" name="color" type="color" <?php $color = $calendar->color; echo "value=\"#$color\""?>>
                            </td>
                            <td>
                                    <input type="hidden" name="idcalendar" value="<?= $calendar->idcalendar; ?>"/>
                                    <input class="btn" type="submit" name="edit" value="Edit">
                                    <input class="btn" type="submit" name="delete" value="Delete">
                            </td>
                        </tr>
                    </tbody>
                </form>                  
                    <?php endforeach; ?>
                <?php endif; ?>
            
                <form class="calendarForm" action="calendar/my_calendars" method="post">
                    <tfoot>
                        <tr>
                            <td>
                                <input class="description" name="description" type="text" size="16" value="">
                            </td>
                            <td>
                                <input class="color" name="color" type="color" value="">
                            </td>
                            <td>
                                    <input class="btn" type="submit" value="Create a calendar" name="create">
                            </td>
                        </tr>
                    </tfoot>                 
                </form>
                </table>
        </div>
    </body>
</html>