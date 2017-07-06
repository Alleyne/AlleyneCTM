@extends('templates.frontend._layouts.unify')

@section('title', '| Reservaciones')

@section('stylesheets')
  <link href="{{ URL::asset('assets/fullcalendar340/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" media="screen">  

    <style>
        body {
          margin: 0px 10px;
          padding: 0;
          font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
          font-size: 13px;
        }

        #calendar {
          max-width: 900px;
          margin: 0 auto;
        }

        .fc-list-item:hover td {
          background-color: #999;
        }  

        }
    </style>

@endsection

@section('content')

  <div id="calendar"></div>
  
  <div id="eventContent" title="Event Details" style="display:none;">
      Reservado por Unidad: <span id="eventUnidad"></span><br>
      Inicia: <span id="startTime"></span><br>
      Termina: <span id="endTime"></span><br><br>                                
      <div id="eventInfo"></div>
      <p><strong><a id="eventLink" target="_blank">Read More</a></strong></p>
  </div>       
@stop

@section('relatedplugins')
  <script src="{{ URL::asset('assets/fullcalendar340/lib/moment.min.js') }}"></script>
  <script src="{{ URL::asset('assets/fullcalendar340/fullcalendar.min.js') }}"></script>
  <script src="{{ URL::asset('assets/fullcalendar340/locale/es.js') }}"></script>  

  <script type="text/javascript">
    $(document).ready(function() {
      
      "use strict";
  
      var date = new Date();
      var d = date.getDate();
      var m = date.getMonth();
      var y = date.getFullYear();
  
      var hdr = {
        left: 'title',
        center: 'month,agendaWeek,agendaDay,listMonth',
        right: 'prev,today,next'
      };
  
      /* initialize the calendar
      -----------------------------------------------------------------*/
      $('#calendar').fullCalendar({
          header: hdr,
          editable: true,
          droppable: false,
          selectable: true,
          selectHelper: true,
          timeFormat: "h:mma",
          slotLabelFormat: "h:mma",
         
          eventClick:  function(event, jsEvent, view) {
            //set the values and open the modal
            $("#startTime").html(moment(event.start).format('MMM Do h:mm A'));
            $("#endTime").html(moment(event.end).format('MMM Do h:mm A'));
            $("#eventUnidad").html(event.un_id);
            $("#eventInfo").html(event.description);
            $("#eventLink").attr('href', event.url);
            
            //$("#eventContent").dialog({ modal: true, title: event.title });
            $("#eventContent").dialog({ modal: true, title: event.title, width:350, resizable: false, draggable: false});
          },
          
          select: function (start, end, allDay) {
            var title = prompt('Event Title:');
            if (title) {
              calendar.fullCalendar('renderEvent', {
                  title: title,
                  start: start,
                  end: end,
                  allDay: allDay
                }, true // make the event "stick"
              );
            }
            calendar.fullCalendar('unselect');
          },
  
          events: { url:"cargaEventos" },                
     
          windowResize: function (event, ui) {
              $('#calendar').fullCalendar('render');
          }
      });
    
    })
  </script>
@stop