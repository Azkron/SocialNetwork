<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Create event</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="Lib/jquery-3.1.1.min.js" type="text/javascript"></script>
        <script src="Lib/jquery-validation-1.16.0/jquery.validate.min.js" type="text/javascript"></script>
        <script>
            $.validator.addMethod("regex", function (value, element, pattern) {
                if (pattern instanceof Array) {
                    for(p of pattern) {
                        if (!p.test(value))
                            return false;
                    }
                    return true;
                } else {
                    return pattern.test(value);
                }
            }, "Please enter a valid input.");
            
            $(function () {
                $('#createEventForm').validate({
                    rules: {
                        title: {
                            remote: {
                                url: 'main/create_event_title_available_service',
                                type: 'post',
                                data:  {
                                    /*pseudo: function() { 
                                        console.log($("#title").val());
                                        return $("#title").val();
                                    }*/
                                    title: function() { 
                                        console.log($("#title").val());
                                        return $("#title").val();
                                    }
                                    
                                    idcalendar: function() { 
                                        console.log($("#idcalendar").val());
                                        return $("#idcalendar").val();
                                    }
                                }
                            },
                            required: true,
                            minlength: 3,
                            maxlength: 50,
                            regex: /^[a-zA-Z][\sa-zA-Z0-9]*$/
                        },
                        description: {
                            minlength: null,
                            maxlength: 500,
                            regex: /^[a-zA-Z][\sa-zA-Z0-9]*$/
                        }
                    },
                    messages: {
                        title: {
                            remote: 'this title is already taken',
                            required: 'required',
                            minlength: 'minimum 3 characters',
                            maxlength: 'maximum 50 characters',
                            regex: 'bad format for title'
                        },
                        description: {
                            maxlength: 'maximum 500 characters',
                            regex: 'bad format for description'
                        }
                    }
                });
                
                $("input:text:first").focus();
            });
        </script>
    </head>
    <body>
        <div class="title">Create event</div>
        <div class="main">
            <br><br>
            <div class="tableForm">
                <form class="eventForm" id="createEventForm" action="Event/create_event" method="post">
                    <table>
                        <tr>
                            <td>Title:</td>
                            <td><input id="title" name="title" type="text" value="<?=$title?>"></td>
                        </tr>
                        <tr>
                            <td>Calendar:</td>
                            <td>
                                <select id="idcalendar" name="idcalendar">
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
                            <td><textarea id="description" name="description" rows=4 cols=50 ><?=$description?></textarea></td>
                        </tr>
                        <tr>
                            <td>Start time:</td> 
                            <td>
                                <input class="datetime" name="startDate" type="date" <?php if($startDate != NULL) echo 'value="'.$startDate.'"'; ?>>
                                <input class="datetime" name="startTime" type="time" <?php if($startTime != NULL) echo 'value="'.$startTime.'"'; ?>>
                            </td>
                        </tr>
                        <tr>
                            <td>Finish time:</td>
                            <td>
                                <input class="datetime" name="finishDate"  type="date" <?php if($finishDate != NULL) echo 'value="'.$finishDate.'"'; ?>>
                                <input class="datetime" name="finishTime"  type="time" <?php if($finishTime != NULL) echo 'value="'.$finishTime.'"'; ?>>
                            </td>
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