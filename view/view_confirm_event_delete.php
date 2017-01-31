<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>My Confirm event deletion</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Confirm event deletion</div>
        <div class="main">
            <br><br>
            <p>The event you are about to delete is not empty!</p>
            <p>Are you certain you want to delete it?</p>
            <form class="confirmDeleteForm" action="event/delete_or_cancel" method="post">
                <input type="hidden" name="idevent" value="<?= $idevent; ?>"/>
                <input class="btn" type="submit" name="cancel" value="Cancel">
                <input class="btn" type="submit" name="delete" value="Confirm">
            </form>
        </div>
    </body>
</html>