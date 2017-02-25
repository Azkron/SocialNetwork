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
        <?php if (count($calendar) != 0)
                echo "<h1>Calendar : $calendar->description</h1>";
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
                        <input class="pseudo" name="pseudo" type="text" size="16" disabled value="<?= $shared_user->pseudo; ?>">
                    </div>      
                    <div class="SharingActions">
                        <input type="hidden" name="iduser" value="<?= $shared_user->iduser; ?>"/>
                        <input type="hidden" name="read_only" value="<?php $shared_user->read_only; ?>"/>
                        <input type="checkbox" name="read_only" value="1" <?php if($shared_user->read_only == 1) echo "checked"; ?>>Write permission</td>
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
                        <select name="pseudo">
                            <option selected disabled>Select pseudo</option>
                            <?php
                            if (count($not_shared_users) != 0) 
                                foreach($not_shared_users as $key => $value)
                                {
                                    echo '<option value="'.$key.'">'.$value['pseudo'].'</option>'; 
                                }                                   
                            ?>
                        </select>
                    </div>
                    <div class="SharingActions">
                         <input type="checkbox" name="read_only" value="0">Write permission</td>
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