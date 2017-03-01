<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sharing Settings</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Sharing Settings</div>
        <div class="menu">
            <a href="calendar/my_calendars">Back</a>
        </div>
        <?php
            echo '<h1 class="subTitle" style="color:#' . $calendar->color . '">Calendar : ' . $calendar->description . '</h1>';
        ?>
        <div class="main">
            <br><br>     
            <div id="Sharing">
                <div class="SharingHeader">
                    <div class="SharingPseudoHeader">Pseudo</div>
                    <div class="SharingActionsHeader">Actions</div>
                </div>
                <?php if (count($shared_users) != 0): ?>
                    <?php foreach ($shared_users as $shared_user): ?>
                        <div class="SharingRow">
                            <form class="SharingForm" action="calendar/sharing_settings" method="post">
                                <div class="SharingPseudo">
                                    <input type="hidden" name="pseudo" value="<?= $shared_user->pseudo; ?>"/>
                                    <?= $shared_user->pseudo; ?>
                                </div>      
                                <div class="SharingActions">
                                    <input type="hidden" name="iduser" value="<?= $shared_user->iduser; ?>"/>
                                    <input type="hidden" name="idcalendar" value="<?= $shared_user->idcalendar; ?>"/>
                                    <input type="checkbox" name="write" value="1" <?php if ($shared_user->read_only == 0) echo "checked"; ?>/><label>Write permission </label>
                                    <input class="btn" type="submit" name="edit" value="Edit">
                                    <input class="btn" type="submit" name="delete" value="Delete">
                                </div>
                            </form>         
                        </div>         
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="SharingRow">
                    <form class="SharingForm" action="calendar/sharing_settings" method="post">
                        <input type="hidden" name="idcalendar" value="<?= $calendar->idcalendar; ?>"/>
                        <div class="SharingPseudo">
                            <select name="pseudo[]">
                                <option selected disabled>Select pseudo</option>
                                <?php
                                if (count($not_shared_users) != 0)
                                    foreach ($not_shared_users as $value)
                                        echo '<option value="' . $value['pseudo'] . '">' . $value['pseudo'] . '</option>';
                                ?>
                            </select>
                        </div>
                        <div class="SharingActions">
                            <input type="checkbox" name="write" value="1"/><label>Write permission </label>
                            <input class="btn" type="submit" name="share_calendar" value="Share calendar"/>
                        </div>
                    </form>         
                </div>       

                <?php
                if (isset($errors))
                    View::print_errors($errors);
                ?>
            </div>
        </div>
    </body>
</html>