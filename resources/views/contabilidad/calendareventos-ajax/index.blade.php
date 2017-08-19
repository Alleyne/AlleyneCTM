@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Reservaciones')

@section('content')

    <!-- MAIN CONTENT -->
    <div id="content">
        
        <div class="row">
        
            <div class="col-sm-12 col-md-12 col-lg-3">
                <!-- new widget -->
                <div class="jarviswidget jarviswidget-color-blueDark">
                    <header>
                        <h2> Agregar eventos </h2>
                    </header>
        
                    <!-- widget div-->
                    <div>
        
                        <div class="widget-body">
                            <!-- content goes here -->
        
                            <form id="add-event-form">
                                <fieldset>
        
                                    <div class="form-group">
                                        <label>Seleccione un Icono</label>
                                        <div class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">
                                            <label class="btn btn-default active">
                                                <input type="radio" name="iconselect" id="icon-1" value="fa-info" checked>
                                                <i class="fa fa-info text-muted"></i> </label>
                                            <label class="btn btn-default">
                                                <input type="radio" name="iconselect" id="icon-2" value="fa-warning">
                                                <i class="fa fa-warning text-muted"></i> </label>
                                            <label class="btn btn-default">
                                                <input type="radio" name="iconselect" id="icon-3" value="fa-check">
                                                <i class="fa fa-check text-muted"></i> </label>
                                            <label class="btn btn-default">
                                                <input type="radio" name="iconselect" id="icon-4" value="fa-user">
                                                <i class="fa fa-user text-muted"></i> </label>
                                            <label class="btn btn-default">
                                                <input type="radio" name="iconselect" id="icon-5" value="fa-lock">
                                                <i class="fa fa-lock text-muted"></i> </label>
                                            <label class="btn btn-default">
                                                <input type="radio" name="iconselect" id="icon-6" value="fa-clock-o">
                                                <i class="fa fa-clock-o text-muted"></i> </label>
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label>Título del evento</label>
                                        <input class="form-control"  id="title" name="title" maxlength="40" type="text" placeholder="Event Title">
                                    </div>
                                    <div class="form-group">
                                        <label>descripción</label>
                                        <textarea class="form-control" placeholder="Una breve descripción" rows="3" maxlength="40" id="description"></textarea>
                                        <p class="note">Máximo 40 carácteres</p>
                                    </div>
        
                                    <div class="form-group">
                                        <label>Seleccione un color</label>
                                        <div class="btn-group btn-group-justified btn-select-tick" data-toggle="buttons">
                                            <label class="btn bg-color-darken active">
                                                <input type="radio" name="priority" id="option1" value="bg-color-darken txt-color-white" checked>
                                                <i class="fa fa-check txt-color-white"></i> </label>
                                            <label class="btn bg-color-blue">
                                                <input type="radio" name="priority" id="option2" value="bg-color-blue txt-color-white">
                                                <i class="fa fa-check txt-color-white"></i> </label>
                                            <label class="btn bg-color-orange">
                                                <input type="radio" name="priority" id="option3" value="bg-color-orange txt-color-white">
                                                <i class="fa fa-check txt-color-white"></i> </label>
                                            <label class="btn bg-color-greenLight">
                                                <input type="radio" name="priority" id="option4" value="bg-color-greenLight txt-color-white">
                                                <i class="fa fa-check txt-color-white"></i> </label>
                                            <label class="btn bg-color-blueLight">
                                                <input type="radio" name="priority" id="option5" value="bg-color-blueLight txt-color-white">
                                                <i class="fa fa-check txt-color-white"></i> </label>
                                            <label class="btn bg-color-red">
                                                <input type="radio" name="priority" id="option6" value="bg-color-red txt-color-white">
                                                <i class="fa fa-check txt-color-white"></i> </label>
                                        </div>
                                    </div>
        
                                </fieldset>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-default" type="button" id="add-event" >
                                                Agregar evento
                                            </button>
                                        </div>
                                        {!! Form::open(['route' => ['guardaEventos'], 'method' => 'POST', 'id' =>'form-calendario']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </form>
        
                            <!-- end content -->
                        </div>
        
                    </div>
                    <!-- end widget div -->
                </div>
                <!-- end widget -->
        
                <div class="well well-sm" id="event-container">
                    <form>
                        <fieldset>
                            <legend>
                                Eventos creados
                            </legend>
                            <ul id='external-events' class="list-unstyled">

                            </ul>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="drop-remove" class="checkbox style-0" checked="checked">
                                    <span>Remover al utilizar</span> </label>
                            </div>
                        </fieldset>
                    </form>
        
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-9">
        
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
    <script src="{{ URL::asset('assets/backend/js/plugin/fullcalendar/moment.min.js') }}"></script>

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
      
          var initDrag = function (e) {
              // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
              // it doesn't need to have a start or end
      
              var eventObject = {
                  title: $.trim(e.children().text()), // use the element's text as the event title
                  description: $.trim(e.children('span').attr('data-description')),
                  icon: $.trim(e.children('span').attr('data-icon')),
                  className: $.trim(e.children('span').attr('class')) // use the element's children as the event class
              };
              // store the Event Object in the DOM element so we can get to it later
              e.data('eventObject', eventObject);
      
              // make the event draggable using jQuery UI
              e.draggable({
                  zIndex: 999,
                  revert: true, // will cause the event to go back to its
                  revertDuration: 0 //  original position after the drag
              });
          };
      
          var addEvent = function (title, priority, description, icon) {
              title = title.length === 0 ? "Untitled Event" : title;
              description = description.length === 0 ? "No Description" : description;
              icon = icon.length === 0 ? " " : icon;
              priority = priority.length === 0 ? "label label-default" : priority;
      
              var html = $('<li><span class="' + priority + '" data-description="' + description + '" data-icon="' +
                  icon + '">' + title + '</span></li>').prependTo('ul#external-events').hide().fadeIn();
      
              $("#event-container").effect("highlight", 800);
      
              initDrag(html);
          };
      
          /* initialize the external events
         -----------------------------------------------------------------*/
      
          $('#external-events > li').each(function () {
              initDrag($(this));
          });
      
          $('#add-event').click(function () {
              var title = $('#title').val(),
                  priority = $('input:radio[name=priority]:checked').val(),
                  description = $('#description').val(),
                  icon = $('input:radio[name=iconselect]:checked').val();
      
              addEvent(title, priority, description, icon);
          });
      
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

              drop: function (date, allDay) { // this function is called when something is dropped
      
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
              },
              

              eventDrop: function(event, delta, revertFunc, jsEvent, ui, view ) {
                if (!confirm("Seguro que desea mover el presente evento a una nueva fecha de inicio?")) {
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
                  $("#eventUnidad").html(event.id);
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
      
              /*events: [{
                  title: 'All Day Event',
                  description: 'long description',                  
                  start: new Date(y, m, d, 12, 0),
                  end: new Date(y, m, d, 14, 0),
                  allDay: false,
                  className: ["event", "bg-color-greenLight"],
                  icon: 'fa-check'
              }],*/
      
              events: { url:"cargaEventos" },              

              eventRender: function (event, element, icon) {
                  if (!event.description == "") {
                      element.find('.fc-event-title').append("<br/><span class='ultra-light'>" + event.description +
                          "</span>");
                  }
                  if (!event.icon == "") {
                      element.find('.fc-event-title').append("<i class='air air-top-right fa " + event.icon +
                          " '></i>");
                  }
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