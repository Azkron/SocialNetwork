<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>My Planning</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <link href='Lib/fullcalendar-3.4.0/fullcalendar.min.css' rel='stylesheet' />
        <link href='Lib/fullcalendar-3.4.0/fullcalendar.print.min.css' rel='stylesheet' media='print' />
        <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
        <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"/>
        
        <script src="Lib/jquery-3.1.1.min.js" type="text/javascript"></script>
        <script src="Lib/jquery-validation-1.16.0/jquery.validate.min.js" type="text/javascript"></script>
        <script src="Lib/carhartl-jquery-cookie-92b7715/jquery.cookie.js" type="text/javascript"></script>
        <script src="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.js" type="text/javascript"></script>
        <script src="Lib/moment.js" type="text/javascript"></script>
        <script src="Lib/fullcalendar-3.4.0/fullcalendar.min.js" type="text/javascript"></script>
        <script src="Lib/jquery.redirect.js" type="text/javascript"></script>
        <script>

        
        function clearEventForm()
        {
            $("#eventTitle").val("");
            $("#eventIdcalendar").val("");
            $("#eventDescription").val("");
            $("#eventStartDate").val("");
            $("#eventStartTime").val("");
            $("#eventFinishDate").val("");
            $("#eventFinishTime").val("");
            $("#eventAllDay").val("");
        }
        
        function openEventForm() {
            $('#eventPopup').dialog({
                resizable: false,
                height: 500,
                width: 700,
                modal: true,
                autoOpen: true
            });
        } 
        var isNew = 0;
        var allDayChecked = false;
        
        function submitEvent()
        {
            
        }
        
	$(function() {
            
            //var defaultViewCookie = $.cookie('defaultViewCookie');
            //var defaultDateCookie = $.cookie('defaultDateCookie');

            $('#calendar').fullCalendar({
                    header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,basicWeek,basicDay'
                    },
                    defaultDate: moment(),
                    defaultView: 'month',
                    /*
                    defaultDate: defaultDateCookie != undefined ? moment(defaultDateCookie) : moment(),
                    defaultView: defaultViewCookie != undefined ? defaultViewCookie : 'month',
                    viewRender: function(view) { 
                        //$.cookie('defaultViewCookie', view.name, { path: '/' }); 
                        //$.cookie('defaultDateCookie', view.start.format()); 
                        //$.cookie('defaultDateCookie', view.intervalStart, { path: '/' }); 
                    },*/
                    navLinks: true, // can click day/week names to navigate views
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    events: 'event/get_events_json',
                    eventClick: function(event, element) {
                        //$.redirect('event/update_event', { 'weekMod' : 0, 'idevent':event.id, read_only:(event.editable ? 0: 1) });
                        isNew = 0;
                        $("#eventCreate").hide();
                        clearEventForm();
                        openEventForm();
                    },
                    dayClick: function(date, jsEvent, view) {

                        //alert('Clicked on: ' + date.format());

                        //alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);

                        isNew = 1;
                        clearEventForm();
                        $("#eventUpdate").hide();
                        $("#eventDelete").hide();
                        var dateString = date.format("YYYY-MM-DD");
                        $("#eventStartDate").val(dateString);
                        openEventForm();
                    }
            });
            
            $('#eventStartDate').change(function() {
                console.log($("#eventStartDate").val());
            });
           
            
            $("#eventCancel").click(function(){$("#eventPopup").dialog("close");});

            $('#eventAllDay').change(function() {
                if($(this).is(":checked")) {
                    $("#eventFinishTime").hide();
                    $("#eventStartTime").hide();
                    allDayChecked = true;
                }
                else
                {
                    $("#eventFinishTime").show();
                    $("#eventStartTime").show();
                    allDayChecked = false;
                }
                
                $("#eventForm").valid();
            });

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
            
            $.validator.addMethod("laterThanStartDate", function (value, element, pattern) {
                if(allDayChecked)
                    return value > $("#eventStartDate").val();
                else
                    return value >= $("#eventStartDate").val();
            }, "Must be later than start date.");
            
            $.validator.addMethod("laterThanStartHour", function (value, element, pattern) {
                if(allDayChecked)
                    return true;
                else
                    return  ($("#eventFinishDate").val() == "" 
                                || $("#eventFinishDate").val() > $("#eventStartDate").val()) 
                                || (value == "" || value > $("#eventStartTime").val());
            }, "Must be later than start hour.");


            $('#eventForm').validate({
                rules: {
                    title: {
                        remote: {
                            url: 'event/available_service',
                            type: 'post',
                            data:  {
                                title: function() { 
                                    return $("#eventTitle").val();
                                },                                   
                                idcalendar: function() { 
                                    return $("#eventIdcalendar").val();
                                },
                                isnewevent:function() { 
                                    console.log("isNew = " + isNew);
                                    return isNew;
                                }
                            }
                        },
                        required: true,
                        minlength: 3,
                        maxlength: 50,
                        regex: /^[a-zA-Z][\sa-zA-Z0-9]*$/
                    },
                    description: {
                        minlength: 3,
                        maxlength: 500,
                        regex: /^[a-zA-Z][\sa-zA-Z0-9]*$/
                    },
                    startDate: {
                        required: true
                    },
                    finishDate: {
                        required: function() { return !allDayChecked && $("#eventFinishTime").val() != "";},
                        laterThanStartDate: $("#eventFinishDate").val()
                    },
                    startTime: {
                        required: function() { return !allDayChecked;}
                    },
                    finishTime: {
                        required: function() { return !allDayChecked && $("#eventFinishDate").val() != "";},
                        laterThanStartHour: $("#eventFinishTime").val()
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
                    },
                    startDate: {
                        required: "required"
                    },
                    finishDate: {
                        required: "required"
                    },
                    startTime: {
                        required: "required"
                    },
                    finishTime: {
                        required: "required"
                    }
                }
            });
	});
    </script>
       
    </head>
    <body>
        <div class="title">My Planning</div>
        <div class="menu">
            <a href="main/welcome">Back</a>
        </div>
        <div class="main">
            
	<div id='calendar'></div>
            <br><br>
                <table>
                    <tr>
                        <td>
                        <form class="buttonForm" action="event/my_planning" method="post">
                            <input type="hidden" name="weekMod" value="<?=$weekMod-1; ?>"/>
                            <input class="btn" type="submit" name="change_week" value="<< Previous week">
                        </form>
                        </td>
                        <td><h1>My Planning</h1></td>
                        <td>
                        <form class="buttonForm" action="event/my_planning" method="post">
                            <input type="hidden" name="weekMod" value="<?=$weekMod+1; ?>"/>
                            <input class="btn" type="submit" name="change_week" value="Next week >>">
                        </form>
                        </td>
                </table>
                        <h2><?php $day = Date::monday($weekMod);?><?=$day->week_string()?></h2>
                
            <div class="events">
                <?php for ($i = 0; $i < 7; ++$i): ?>

                <div class="eventHeader">
                    <div class="eventHour"><?=$day->day_string()?></div>
                </div>
                        <?php if (count($week[$i]) != 0): ?>
                            <?php foreach ($week[$i] as $event): ?>

                <div class="eventRow" title="<?= $event->description; ?>">
                    <form class="buttonForm" action="event/update_event" method="post">
                        <div style="color:#<?=$event->color?>" class="eventHour">
                            <?= $event->get_time_string($day); ?>
                        </div>
                        <div style="color:#<?=$event->color?>" class="eventTitle">
                            <?= $event->title; ?>
                        </div>
                        <?php if ($event->read_only != 1): ?>
                        <div class="eventEdit">
                                <input type="hidden" name="weekMod" value="<?= $weekMod; ?>"/>
                                <input type="hidden" name="idevent" value="<?= $event->idevent; ?>"/>
                                <input type="hidden" name="read_only" value="<?= $event->read_only; ?>"/>
                                <input class="btn" type="submit" name="edit_event" value="Edit event">
                        </div>
                        
                        <?php endif; ?>
                    </form>
                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php $day->next_day();?>

                    <?php endfor; ?>
                <div id="createEvent">
                    <?php
                        if(isset($errors))
                            View::print_errors($errors);
                    ?>
                    <?php if (!isset($errors) || count($errors) <= 0): ?>
                    <form class="buttonForm" action="event/create_event" method="post">
                        <input type="hidden" name="weekMod" value="<?= $weekMod; ?>"/>
                        <input class="btn" type="submit" value="create" name="create">
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        
        
        <div id="eventPopup" class="tableForm" hidden>
            <form class="eventForm" id="eventForm" action="javascript:submitEvent()" method="post">
                <table>
                    <tr>
                        <td>Title:</td>
                        <td><input id="eventTitle" name="title" type="text"></td>
                    </tr>
                    <tr>
                        <td>Calendar:</td>
                        <td>
                            <select id="eventIdcalendar" name="idcalendar">
                                <?php
                                if (count($calendars) != 0) 
                                    foreach($calendars as $calendar)
                                        echo '<option value="'.$calendar->idcalendar.'" style="color:#'.$calendar->color.'">'.$calendar->description.'</option>';  
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Description:</td> 
                        <td><textarea id="eventDescription" name="description" rows=4 cols=50 ></textarea></td>
                    </tr>
                    <tr>
                        <td>Start :</td> 
                        <td>
                            <input id="eventStartDate" class="datetime" name="startDate" type="date">
                            <input id="eventStartTime" class="datetime" name="startTime" type="time">
                        </td>
                    </tr>
                    <tr>
                        <td>Finish :</td>
                        <td>
                            <input id="eventFinishDate" class="datetime" name="finishDate"  type="date">
                            <input id="eventFinishTime" class="datetime" name="finishTime"  type="time">
                        </td>
                    </tr>
                    <tr>
                        <td><input id="eventAllDay" type="checkbox" name="allDay" value="1">Whole day event</td>
                    </tr>    
                    <tr>
                        <td></td>
                        <td>
                            <input type="hidden" name="weekMod" />
                            <input type="hidden" name="idevent" />
                            <input type="hidden" name="read_only" />
                            <input id="eventCreate" class="btn" type="submit" name = "create" value="Create">
                            <input id="eventUpdate" class="btn" type="submit" name = "update" value="Update">
                            <input id="eventCancel" class="btn" type="button" name = "cancel" value="Cancel"> 
                            <input id="eventDelete" class="btn" type="submit" name = "delete" value="Delete"> 
                        </td>
                    </tr>                                     
                </table>
            </form>
        </div>
        
    </body>
</html>