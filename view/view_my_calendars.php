<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>My Calendars</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="JS/jquery-3.1.1.min.js" type="text/javascript"></script>
        <script src="JS/jquery-validation-1.16.0/jquery.validate.min.js" type="text/javascript"></script>
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
                
                $('#calendarCreate').validate({
                        rules: {
                            description: {
                                remote: {
                                    url: 'calendar/description_available_service', 
                                    type: 'post',
                                    data:  {
                                        description: function() { 
                                            return $("#descriptionCreate").val();
                                        }
                                    }
                                },
                                required: true,
                                minlength: 3,
                                maxlength: 50,
                                regex: /^[a-zA-Z][\sa-zA-Z0-9]*$/
                            }
                        },
                        messages: {
                            description: {
                                remote: 'this description is already taken',
                                required: 'required',
                                minlength: 'minimum 3 characters',
                                maxlength: 'maximum 50 characters',
                                regex: 'bad format for description'
                            }
                        }
                    });
                
                $('.calendarEdit').each(function() { 
                    // We define description here because $(this) doesn't work inside validate
                    var description = $(this).find(".description").val();  
                    var idcalendar = $(this).find(".idcalendar").val(); 
                    $(this).validate({
                        rules: {
                            description: {
                                remote: {
                                    url: 'calendar/description_available_service_edit', 
                                    type: 'post',
                                    data:  {
                                        description: function() { 
                                            return description;
                                        },
                                        idcalendar: function() { 
                                            return idcalendar;
                                        }
                                    }
                                },
                                required: true,
                                minlength: 3,
                                maxlength: 50,
                                regex: /^[a-zA-Z][\sa-zA-Z0-9]*$/
                            }
                        },
                        messages: {
                            description: {
                                remote: 'this description is already taken',
                                required: 'required',
                                minlength: 'minimum 3 characters',
                                maxlength: 'maximum 50 characters',
                                regex: 'bad format for description'
                            }
                        }
                    });
                });  
                
                $("input:text:first").focus();
            });
        </script>
    </head>
    <body>
        <div class="title">My Calendars</div>
        <div class="menu">
            <a href="index.php">Back</a>
        </div>
        <div class="main">
            <br><br>
            
            <div id="calendars">
            <div class="calendarHeader">
                <div class="calendarDescriptionHeader">Description</div>
                <div class="calendarColorheader">Color</div>
                <div class="calendarActionsHeader">Actions</div>
            </div>
                
                <?php if (count($calendars) != 0) : ?>
                    <?php foreach ($calendars as $calendar): ?>
            <div class="calendarRow">
                    <div class="calendarDescription">
                        <?php if ($calendar->read_only == -1) :?>
                        <form class="calendarForm calendarEdit" action="calendar/my_calendars" method="post">
                            <div class="calendarDescription">
                                <input class="description" name="description" type="text" size="16" value="<?= $calendar->description; ?>">
                                <br/>
                                <label id="description-error" class="error" for="description"></label>
                                
                            </div>
                            <div class="calendarColor">
                                <input class="color" name="color" type="color" <?php $color = $calendar->color; echo "value=\"#$color\""?>>
                            </div>
                            <div class="calendarActions">
                                <input type="hidden" class = "idcalendar" name="idcalendar" value="<?= $calendar->idcalendar; ?>"/>   
                                <input class="btn" type="submit" name="edit" value="Edit">
                                <input class="btn" type="submit" name="delete" value="Delete">
                                <input class="btn" type="submit" name="share" value="Share">
                            </div>
                        </form>         
                        <?php else:?>
                            <div class="description" <?php echo 'style="color:#'.$calendar->color.'"' ?>>
                                <?= $calendar->description; ?>
                            </div>
                            <div class="description">
                                (Owned by <?= $calendar->owner_pseudo; ?>)
                            </div>
                        
                            
                        <?php endif; ?>
                    </div>
            </div>    
                    <?php endforeach; ?>
                <?php endif; ?>
                
            <div class="calendarRow">
                <form class="calendarForm"  id="calendarCreate" action="calendar/my_calendars" method="post">
                    <div class="calendarDescription">
                        <input class="description" id="descriptionCreate" name="description" type="text" size="16" value="">
                        <br/>
                        <label id="description-error" class="error" for="description"></label>
                    </div>
                    <div class="calendarColor">
                        <input class="color" name="color" type="color" value="">
                    </div>
                    <div class="calendarActions">
                        <input class="btn" type="submit" value="Create calendar" name="create">
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