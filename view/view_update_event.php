<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Update event</title>
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
                $('#updateEventForm').validate({
                    rules: {
                        title: {
                            remote: {
                                url: 'main/update_event_title_available_service',
                                type: 'post',
                                data:  {
                                    pseudo: function() { 
                                        console.log($("#title").val());
                                        return $("#title").val();
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
        <div class="title">Update event</div>
        <div class="main">
            <br><br>
            <div class="tableForm">
                <form class="eventForm" id="updateEventForm"action="Event/update_event" method="post">
                    <table>
                        <tr>
                            <td>Title:</td>
                            <td><input name="title" type="text" value="<?=$event->title?>"></td>
                        </tr>
                        <tr>
                            <td>Calendar:</td>
                            <td>
                                <select name="idcalendar">
                                    <?php
                                    if (count($calendars) != 0) 
                                        foreach($calendars as $calendar)
                                        {
                                            $selected = '';
                                            $isCalendar = $calendar->idcalendar == $event->idcalendar;
                                            if($isCalendar || $event->read_only == -1)
                                            {
                                                if($isCalendar)
                                                    $selected = 'selected="selected"';

                                                echo '<option '.$selected.' value="'.$calendar->idcalendar.'" style="color:#'.$calendar->color.'">'.$calendar->description.'</option>';
                                            }
                                        }                                   
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Description:</td> 
                            <td><textarea name="description" rows=4 cols=50><?=$event->description ?></textarea></td>
                        </tr>
                        <tr>
                            <td>Start time:</td> 
                            <td>
                                <input class="date" name="startDate" type="date"  <?php echo 'value="'.$event->start->date_input_string().'"'; ?>>
                                <input class="time" name="startTime" type="time"  <?php echo 'value="'.$event->start->hour_input_string().'"'; ?>>
                            </td>
                        </tr>
                        <tr>
                            <td>Finish time:</td>
                            <td>
                                <input class="date" name="finishDate"  type="date" <?php if($event->finish != NULL) echo 'value="'.$event->finish->date_input_string().'"'; ?>>
                                <input class="time" name="finishTime"  type="time" <?php if($event->finish != NULL) echo 'value="'.$event->finish->hour_input_string().'"'; ?>>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="whole_day" value="1" <?php if($event->whole_day)echo "checked"; ?>>Whole day event</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type="hidden" name="weekMod" value="<?= $weekMod; ?>"/>
                                <input type="hidden" name="idevent" value="<?= $event->idevent; ?>"/>
                                <input type="hidden" name="read_only" value="<?= $event->read_only; ?>"/>
                                <input class="btn" type="submit" name = "update" value="Update">
                                <input class="btn" type="submit" name = "cancel" value="Cancel"> 
                                <input class="btn" type="submit" name = "delete" value="Delete"> 
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