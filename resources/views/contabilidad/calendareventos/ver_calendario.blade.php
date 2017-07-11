@extends('templates.backend._layouts.smartAdmin')

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
/*
    .bg-color-yellow{
      background-color: #b5b1a4!important;
    }

    .txt-color-white {
      color:#b5b1a4!important;*/
    }
  </style>

@endsection

@section('content')

  <div id="calendar"></div>
  
  <div id="eventContent" title="Event Details" style="display:none;">
      Reservado por Unidad: <strong><span id="eventUnidad"></span></strong><br>
      Inicia: <strong><span id="startTime"></span></strong><br>
      Termina: <strong><span id="endTime"></strong></span>                            
      {{-- <div id="eventInfo"></div> --}}
  </div>       
@stop

@section('relatedplugins')
  <script src="{{ URL::asset('assets/fullcalendar340/lib/moment.min.js') }}"></script>
  <script src="{{ URL::asset('assets/fullcalendar340/fullcalendar.min.js') }}"></script>
  <script src="{{ URL::asset('assets/fullcalendar340/locale/es.js') }}"></script>  

  <script type="text/javascript">
    $(document).ready(function() {
      
      pageSetUp();
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
          droppable: true,
          
          /*displayEventEnd: {
            month: false,
            agendaWeek: false,
            agendaDay: false,
            'default':true
          },
          
          defaultView: 'agendaWeek',
          slotMinutes: 60,
          minTime: 7,
          maxTime: 19 */         

          timeFormat: "h:mma",
          slotLabelFormat: "h:mma",
          
          eventDrop: function(event, delta, revertFunc, jsEvent, ui, view ) {
            if (event.status == 2) {  // no permite mover de fecha un evento si el mismo ya culmino,
              $('#calendar').fullCalendar( 'refetchEvents' );
              revertFunc();
            } else {
              if (!confirm("Seguro que desea mover el presente evento a una nueva fecha de inicio?")) {
                $('#calendar').fullCalendar( 'refetchEvents' );
                revertFunc();
              }
              var start = moment(event.start).format("YYYY-MM-DD HH:mm");
              
              if(event.end){
                var end = moment(event.end).format("YYYY-MM-DD HH:mm");
              }
              
              var className = event.className;
              var allDay = event.allDay;
              crsfToken = document.getElementsByName("_token")[0].value;

              $.ajax({  
                url: 'actualizaEvento',
                data: 'start=' + start + '&end=' + end + '&id=' + event.id + '&allDay=' + allDay,
                type: "POST",
                headers: {
                  "X-CSRF-TOKEN": crsfToken
                },
                success: function(json) {
                  console.log("Updated Successfully eventdrop");
                },
                error: function(json){
                  console.log("Error al actualizar eventdrop");
                }
              });                  
            }
          },
      
          eventResize: function(event, delta, revertFunc) {
            if (event.status == 2) {  // no permite mover de fecha un evento si el mismo ya culmino,
              $('#calendar').fullCalendar( 'refetchEvents' );
              revertFunc();
            } else {

              if (!confirm("Seguro que desea actualizar el presente evento?")) {
                  $('#calendar').fullCalendar( 'refetchEvents' );
                  revertFunc();
              }

              var id = event.id;
              var start = moment(event.start).format("YYYY-MM-DD HH:mm");
              var allDay = event.allDay;

              if(event.end){
                var end = moment(event.end).format("YYYY-MM-DD HH:mm");
              }

              //console.log(event, start, end, allDay);
              //alert(id);
              
              crsfToken = document.getElementsByName("_token")[0].value;
              $.ajax({
                url: 'actualizaEvento',
                //data: 'title='+ event.title + '&start=' + start + '&end=' + end + '&id=' + event.id + '&className=' + className + '&allDay=' + allDay,
                data: 'start=' + start + '&end=' + end + '&id=' + event.id + '&allDay=' + allDay,
                type: "POST",
                headers: {
                      "X-CSRF-TOKEN": crsfToken
                },
                success: function(json) {
                  console.log("El evento ha sigo actualizado con exito!");
                },
                error: function(json){
                  console.log("Error al actualizar evento");
                }
              });
            }
          },
          
          eventClick:  function(event, jsEvent, view) {
            //set the values and open the modal
            $("#eventUnidad").html(event.un_id);            
            $("#startTime").html(moment(event.start).format('dddd, MMMM D\/ YYYY h:mm A'));
            $("#endTime").html(moment(event.end).format('dddd, MMMM D\/ YYYY h:mm A'));
            /*$("#eventInfo").html(event.description);
            $("#eventLink").attr('href', event.url);*/
            
            //$("#eventContent").dialog({ modal: true, title: event.title });
            $("#eventContent").dialog({ modal: true, title: event.title, width:350, resizable: false, draggable: false});
          },
          
          /*select: function (start, end, allDay) {
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
          },*/
  
          events: { url:"cargaEventos" },              
     
          /*eventRender: function (event, element, icon) {
              element.find(".fc-event-time").remove();            
              element.find('.fc-event-title').append("<br/><span class='ultra-light'>" + event.un_id + "</span>");
              element.find('.fc-event-title').append("<i class='air air-top-right fa " + event.icon + " '></i>");
              element.append("<br/><span class='fc-event-inner fc-event-skin ultra-light'>" + 'Inicia: ' + moment(event.start).format("HH:mm A")  + "</span>");
              element.append("<br/><span class='fc-event-inner fc-event-skin ultra-light'>" + 'Finaliza: ' + moment(event.end).format("HH:mm A")  + "</span>");
          },*/
  
          windowResize: function (event, ui) {
              $('#calendar').fullCalendar('render');
          }
      });
    
    })
  </script>
@stop