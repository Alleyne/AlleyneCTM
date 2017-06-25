@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Reservaciones')

@section('content')

    <!-- MAIN CONTENT -->
    <div id="content">
        
        <div class="row">
        

            <div class="col-sm-12 col-md-12 col-lg-12">
        
                <!-- new widget -->
                <div class="jarviswidget jarviswidget-color-blueDark">
        
                    <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
        
                    data-widget-colorbutton="false"
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true"
                    data-widget-sortable="false"
        
                    -->
                    <header>
                        <span class="widget-icon"> <i class="fa fa-calendar"></i> </span>
                        <h2> Eventos del PH </h2>
                        <div class="widget-toolbar">
                            <!-- add: non-hidden - to disable auto hide -->
                            <div class="btn-group">
                                <button class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown">
                                    Mostrar vista <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu js-status-update pull-right">
                                    <li>
                                        <a href="javascript:void(0);" id="mt">Mensual</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" id="ag">Agenda semanal</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" id="td">Agenda diaria</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </header>
        
                    <!-- widget div-->
                    <div>
        
                        <div class="widget-body no-padding">
                            <!-- content goes here -->
                            <div class="widget-body-toolbar">
        
                                <div id="calendar-buttons">
        
                                    <div class="btn-group">
                                        <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-prev"><i class="fa fa-chevron-left"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-next"><i class="fa fa-chevron-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div id="calendar"></div>
                            
                            <div id="eventContent" title="Event Details" style="display:none;">
                                Reservado por Unidad: <span id="eventUnidad"></span><br>
                                Inicia: <span id="startTime"></span><br>
                                Termina: <span id="endTime"></span><br><br>                                
                                <div id="eventInfo"></div>
                                <p><strong><a id="eventLink" target="_blank">Read More</a></strong></p>
                            </div>       

                            <!-- end content -->
                        </div>
                    </div> <!-- end widget div -->
                </div> <!-- end widget -->
            </div>
        </div> <!-- end row -->
    </div>
    <!-- END MAIN CONTENT -->

@stop

@section('relatedplugins')
   
    <script src="{{ URL::asset('assets/backend/js/plugin/fullcalendar/jquery.fullcalendar.min.js') }}"></script>
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
              center: 'month,agendaWeek,agendaDay',
              right: 'prev,today,next'
          };
      
          /* initialize the calendar
         -----------------------------------------------------------------*/
          $('#calendar').fullCalendar({
              monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
              monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
              dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sábado'],
              dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'],

              //minTime: "09:00:00",
              //maxTime: "23:00:00",

              //defaultTimedEventDuration: '02:00:00',
              //forceEventDuration: true,              
              //defaultAllDayEventDuration: { days: 1 },
              //nextDayThreshold: '09:00:00', // 9am
              
              header: hdr,
              buttonText: {
                  prev: '<i class="fa fa-chevron-left"></i>',
                  next: '<i class="fa fa-chevron-right"></i>'
              },
      
              editable: true,
              droppable: true, // this allows things to be dropped onto the calendar !!!

              /*drop: function (date, allDay) { // this function is called when something is dropped
      
                  // retrieve the dropped element's stored Event Object
                  var originalEventObject = $(this).data('eventObject');
      
                  // we need to copy it, so that multiple events don't have a reference to the same object
                  var copiedEventObject = $.extend({}, originalEventObject);
                  //console.log(copiedEventObject);
                  
                  // assign it the date that was reported
                  copiedEventObject.start = moment(date).format("YYYY-MM-DD HH:mm:ss");
                  copiedEventObject.allDay = allDay;
                  //copiedEventObject.backgroundColor = $(this).css("background-color");
                 
                  if ($('#drop-remove').is(':checked')) {
                    // if so, remove the element from the "Draggable Events" list
                    $(this).remove();
                  }  
                
                  //Guardamos el evento creado en base de datos
                  var title = copiedEventObject.title;
                  var description = copiedEventObject.description;
                  var start = copiedEventObject.start;
                  var end = copiedEventObject.start;
                  var allDay = copiedEventObject.allDay;  
                  var className = copiedEventObject.className;
                  var icon = copiedEventObject.icon;
                  //console.log(title, start, end, allDay, className, icon);

                  crsfToken = document.getElementsByName("_token")[0].value;
                  //console.log(crsfToken);

                  $.ajax({
                    url: '{{ URL::route('guardaEventos') }}',
                    data: 'title='+ title + '&description=' + description + '&start=' + start + '&end=' + end+ '&allDay=' + allDay + '&className=' + className + '&icon=' + icon,
                    type: 'POST',
                    headers: {
                          "X-CSRF-TOKEN": crsfToken
                      },
                    success: function(events) {
                      // render the event on the calendar
                      // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                      $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                      console.log('Evento creado');      

                    },
                      error: function(json){
                      console.log("Error al crear evento");
                    }        
                  });     
              },*/
              

              eventDrop: function(event, delta, revertFunc, jsEvent, ui, view ) {
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
              },

              
              eventResize: function(event, delta, revertFunc) {
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
              },

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
              
              //dayClick: function() {
                  //alert('a day has been clicked!'+event.id);
              //},
     
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

              /*eventRender: function(event, element) {
                  element.find(".fc-event-title").remove();
                  element.find(".fc-event-time").remove();
                  var new_description =   
                      moment(event.start).format("HH:mm") + '-'
                      + moment(event.end).format("HH:mm") + '<br/>'
                      + event.customer + '<br/>'
                      + '<strong>Address: </strong><br/>' + event.address + '<br/>'
                      + '<strong>Task: </strong><br/>' + event.task + '<br/>'
                      + '<strong>Place: </strong>' + event.place + '<br/>'
                  ;
                  element.append(new_description);
              },  */            

              eventRender: function (event, element, icon) {
                  element.find(".fc-event-time").remove();            
                  element.find('.fc-event-title').append("<br/><span class='ultra-light'>" + event.un_id + "</span>");
                  element.find('.fc-event-title').append("<i class='air air-top-right fa " + event.icon + " '></i>");
                  element.append("<br/><span class='fc-event-inner fc-event-skin ultra-light'>" + 'Inicia: ' + moment(event.start).format("HH:mm A")  + "</span>");
                  element.append("<br/><span class='fc-event-inner fc-event-skin ultra-light'>" + 'Finaliza: ' + moment(event.end).format("HH:mm A")  + "</span>");

              },
      
              windowResize: function (event, ui) {
                  $('#calendar').fullCalendar('render');
              }
          });
      
        /* hide default buttons */
        $('.fc-header-right, .fc-header-center').hide();
      
        $('#calendar-buttons #btn-prev').click(function () {
            $('.fc-button-prev').click();
            return false;
        });
        
        $('#calendar-buttons #btn-next').click(function () {
            $('.fc-button-next').click();
            return false;
        });
        
        $('#calendar-buttons #btn-today').click(function () {
            $('.fc-button-today').click();
            return false;
        });
        
        $('#mt').click(function () {
            $('#calendar').fullCalendar('changeView', 'month');
        });
        
        $('#ag').click(function () {
            $('#calendar').fullCalendar('changeView', 'agendaWeek');
        });
        
        $('#td').click(function () {
            $('#calendar').fullCalendar('changeView', 'agendaDay');
        });     

   })
    </script>
@stop