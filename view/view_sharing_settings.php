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
        <div class="main">
            <br><br>     
            <div id="Sharing">
            <div class="SharingHeader">
                <div class="SharingPseudoHeader">Pseudo</div>
                <div class="SharingActionsHeader">Actions</div>
            </div>
                <?php if (count($shared_calendars) != 0): ?>
                    <?php foreach ($shared_calendars as $shared_calendar): ?>
            <div class="SharingRow">
                <form class="SharingForm" action="calendar/sharing_settings" method="post">
                    <div class="SharingPseudo">
                        <input class="pseudo" name="pseudo" type="text" size="16" value="<?= $shared_calendar['pseudo']; ?>">
                    </div>      
                    <div class="SharingActions">                  
                        <input type="hidden" name="idcalendar" value="<?= $calendar->idcalendar; ?>"/>
                        <input type="checkbox" name="write_permission" value="1" <?php if($shared_calendar->write_permission == 0) echo "checked"; ?>>Write permission</td>
                        <input class="btn" type="submit" name="edit" value="Edit">
                        <input class="btn" type="submit" name="delete" value="Delete">
                    </div>
                </form>         
            </div>         
                    <?php endforeach; ?>
                <?php endif; ?>
            <div class="SharingRow">
                <form class="SharingForm" action="calendar/sharing_settings" method="post">
                    <div class="SharingPseudo">
                        <select name="iduser">
                            <?php
                            if (count($not_shared_calendars) == 0) 
                                foreach($not_shared_calendars as $not_shared)
                                {
                                    $selected = '';
                                    $selected = 'selected="selected"';
                                    echo '<option value="'.$not_shared->pseudo.'"selected >"'."Select Pseudo".'</option>';
                                }                                   
                            ?>
                        </select>
                    </div>
                    <div class="SharingActions">
                         <input type="checkbox" name="write_permission" value="1">Write permission</td>
                         <input class="btn" type="submit" name="share" value="Share my calendar">
                    </div>
                </form>         
            </div>       
            
            <?php
                if(isset($errors))
                    View::print_errors($errors);
            ?>
                </div>
        </div>
    </body>
</html>