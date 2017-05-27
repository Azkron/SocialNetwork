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

        var isNew = false;
        var allDayChecked = false;
        var currEvent = null;
        
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
        
        function openEventForm(event) {
            currEvent = event;
            $('#eventPopup').dialog({
                resizable: false,
                height: 500,
                width: 700,
                modal: true,
                autoOpen: true
            });
        } 
        
        
        function clearEventShow()
        {
            $("#eventCalendarShow").html("");
            $("#eventTitleShow").html("");
            $("#eventDescriptionShow").html("");  
            $('#eventAllDayShow').html("");
            $("#eventStartShow").html("");
            $("#eventStartShow").html("");
        }
        
        function openEventShow(event) {
            currEvent = event;
            clearEventShow();
            eventToShow(event);
            $('#showEventPopup').dialog({
                resizable: false,
                height: 700,
                width: 700,
                modal: true,
                autoOpen: true,
                buttons: {
                    Close: function () {
                        $(this).dialog("close");
                    }
                }
            });
        } 
        
        function formToEvent(event)
        {
            event.idcalendar = $("#eventIdcalendar").val();
            event.color = $("#" + event.idcalendar + "color").val();
            event.title = $("#eventTitle").val();
            event.description = $("#eventDescription").val();  
            event.allDay = allDayChecked;
            event.start = $("#eventStartDate").val() + " " + $("#eventStartTime").val();
            event.end = $("#eventFinishDate").val() + " " + $("#eventFinishTime").val(); 
            event.editable = 1;
        }
        
        function eventToForm(event)
        {
            $("#eventIdcalendar").val(event.idcalendar);
            $("#eventTitle").val(event.title);
            $("#eventDescription").val(event.description);  
            $('#eventAllDay').prop('checked', event.allDay);
            updateAllDay()
            $("#eventStartDate").val(event.start.format("YYYY-MM-DD"));
            $("#eventStartTime").val(event.start.format("HH-mm-ss"));
            
            if(event.end != null)
            {
                $("#eventFinishDate").val(event.end.format("YYYY-MM-DD"));
                $("#eventFinishTime").val(event.end.format("HH-mm-ss")); 
            }
            //event.editable = 1;
        }
        
        function eventToShow(event)
        {
            $.post( 'calendar/calendar_sharing_name_ajax', {idcalendar : event.idcalendar}, function( data ) {
                                    $("#eventCalendarShow").html(JSON.parse(data));
                                });
            //$("#eventCalendarShow").html("test calendar");
            $("#eventTitleShow").html(event.title);
            $("#eventDescriptionShow").html(event.description);  
            $('#eventAllDayShow').html(event.allDay ? "This event lasts the whole day" : "");
            $("#eventStartShow").html(event.start.format());
            if(event.end != null)
                $("#eventStartShow").html(event.end.format());
        }
        
        function submitEventForm()
        {
            formToEvent(currEvent);
            
            var postMap ={
                        "title" : $("#eventTitle").val(), 
                        "idcalendar" : $("#eventIdcalendar").val(), 
                        "startDate" : $("#eventStartDate").val(), 
                        "startTime" : $("#eventStartTime").val(), 
                        "finishDate" : $("#eventFinishDate").val(), 
                        "finishTime" : $("#eventFinishTime").val(), 
                        "description" : $("#eventDescription").val()
                        };
                        
            if(allDayChecked)
               postMap["whole_day"] = $("#eventAllDay").val();
           
            if(isNew)
            {
                $('#calendar').fullCalendar('renderEvent', currEvent , true);
                $.post( 'event/create_event_ajax', postMap);
            }
            else
            {
                $('#calendar').fullCalendar('updateEvent', currEvent);
                postMap["idevent"] = currEvent.id;
                $.post( 'event/update_event_ajax', postMap);
            }
            
            $("#eventPopup").dialog("close");
        }
        
        function updateAllDay()
        {
            if($('#eventAllDay').is(":checked")) {
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
                        if(event.editable)
                        {
                            isNew = false;
                            clearEventForm();
                            $("#eventCreate").hide();
                            $("#eventUpdate").show();
                            $("#eventDelete").show();
                            eventToForm(event);
                            openEventForm(event);
                        }
                        else
                        {
                            openEventShow(event);
                        }
                    },
                    dayClick: function(date, jsEvent, view) {

                        //alert('Clicked on: ' + date.format());

                        //alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                        isNew = true;
                        clearEventForm();
                        $("#eventCreate").show();
                        $("#eventUpdate").hide();
                        $("#eventDelete").hide();
                        $("#eventStartDate").val(date.format("YYYY-MM-DD"));
                        openEventForm(jsEvent);
                    }
            });
            
            $("#eventDelete").click(function(){
                $.post( 'event/delete',{idevent : currEvent.id});
                $('#calendar').fullCalendar('removeEvents', currEvent.id);
            });
            
            $('#eventStartDate').change(function() {
                console.log($("#eventStartDate").val());
            });
           
            
            $("#eventCancel").click(function(){$("#eventPopup").dialog("close");});

            $('#eventAllDay').change(function() {
                updateAllDay();
                $("#eventStartTime").valid();
                $("#eventFinishTime").valid();
                $("#eventFinishDate").valid();
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
                if(value == "")
                    return true;
                else if(allDayChecked)
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
        
        <input id='color' value="5" hidden/>
        
        <div id="eventPopup" class="tableForm" hidden>
            <form class="eventForm" id="eventForm" action="javascript:submitEventForm()" method="post">
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
                            
                            <?php
                            if (count($calendars) != 0) 
                                foreach($calendars as $calendar)
                                    echo "<input id='".$calendar->idcalendar."color' value='#".$calendar->color."' type='hidden'/>";
                            ?>
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
                            <input id="eventDelete" class="btn" type="button" name = "delete" value="Delete"> 
                        </td>
                    </tr>                                     
                </table>
            </form>
        </div>
        
        <div id="showEventPopup"  hidden>
                <table>
                    <tr>
                        <td>Title:</td>
                        <td id="eventTitleShow"></td>
                    </tr>
                    <tr>
                        <td>Calendar:</td>
                        <td id="eventCalendarShow">
                        </td>
                    </tr>
                    <tr>
                        <td>Description:</td> 
                        <td id="eventDescriptionShow"></td>
                    </tr>
                    <tr>
                        <td>Start :</td> 
                        <td id="eventStartShow">
                        </td>
                    </tr>
                    <tr>
                        <td>Finish :</td>
                        <td id="eventFinishShow">
                        </td>
                    </tr>
                    <tr>
                        <td id="eventAllDayShow"></td>
                    </tr>                                      
                </table>
        </div>
        
    </body>
</html>