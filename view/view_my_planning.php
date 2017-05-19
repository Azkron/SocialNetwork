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
        <script src="Lib/jquery-3.1.1.min.js" type="text/javascript"></script>
        <script src="Lib/jquery-validation-1.16.0/jquery.validate.min.js" type="text/javascript"></script>
        <script src="Lib/carhartl-jquery-cookie-92b7715/jquery.cookie.js" type="text/javascript"></script>
        <script src="Lib/moment.js" type="text/javascript"></script>
        <script src="Lib/fullcalendar-3.4.0/fullcalendar.min.js" type="text/javascript"></script>
        <script src="Lib/jquery.redirect.js" type="text/javascript"></script>
        <script>

	$(document).ready(function() {
            
                /*$.get('event/json_test', function(data){
                    console.log(data);
                }, "json").fail(function(){
                    console.log("Error encountered while retrieving the data!");
                });
                
                $.get('event/get_events_json', { start: '2017-03-10', end: "2017-03-26" }, function(data){
                    console.log(data);
                }, "json").fail(function(){
                    console.log("Error encountered while retrieving the events!");
                });*/
                /*var viewName = null;
                var viewIntervalStart = null;
                $.get( "main/get_view_session", function( data ) {
                    if(data == "null")
                        console.log("data == " + data);
                  }, "json" );*/
                  
                var defaultViewCookie = $.cookie('defaultViewCookie');
                var defaultDateCookie = $.cookie('defaultDateCookie');
                
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek,basicDay'
			},
			defaultDate: defaultDateCookie != undefined ? moment(defaultDateCookie) : moment(),
                        defaultView: defaultViewCookie != undefined ? defaultViewCookie : 'month',
                        viewRender: function(view) { 
                            $.cookie('defaultViewCookie', view.name); 
                            //$.cookie('defaultDateCookie', view.start.format()); 
                            $.cookie('defaultDateCookie', view.intervalStart); 
                        },
			navLinks: true, // can click day/week names to navigate views
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			events: 'event/get_events_json',
                        eventClick: function(event, element) {
                            //window.location.href = "event/my_planning";
                            $.redirect('event/update_event', { 'weekMod' : 0, 'idevent':event.id, read_only:(event.editable ? 0: 1) });
                            //$.post( 'event/update_event', { 'weekMod' : 0, 'idevent':event.id, read_only:(event.editable ? 0: 1) });
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
        
    </body>
</html>