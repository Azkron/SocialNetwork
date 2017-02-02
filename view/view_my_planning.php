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
                <?php if (count($calendars) != 0): ?>
                    <?php foreach ($calendars as $calendar): ?>
                <form class="calendarForm" action="calendar/edit_or_delete" method="post">
                    <table>
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
                    </table>
                </form>
                    <?php endforeach; ?>
                <?php endif; ?>
            
                <form class="calendarForm" action="calendar/create_calendar" method="post">
                    <table>
                        <tr>
                            <td>
                                <input class="description" name="description" type="text" size="16" value="">
                            </td>
                            <td>
                                <input class="color" name="color" type="color" value="">
                            </td>
                            <td>
                                    <input class="btn" type="submit" value="create" name="Create a calendar">
                            </td>
                        </tr>
                    </table>
                </form>
        </div>
    </body>
</html>