@extends('templates.frontend._layouts.unify')

@section('title', '| Reservaciones')

@section('stylesheets')
{{-- <link href="{{ URL::asset('assets/backend/css/smartadmin-production_unminified.css') }}" rel="stylesheet" type="text/css" media="screen">    
 --}}
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

  /********** VJ - Move to Less - End *************/
  /*
   * jQuery UI Dialog 1.10.3
   *
   * Copyright 2013, AUTHORS.txt (http://jqueryui.com/about)
   * Dual licensed under the MIT or GPL Version 2 licenses.
   * http://jquery.org/license
   *
   * http://docs.jquery.com/UI/Dialog#theming
   */
  .ui-dialog {
    position: absolute;
    top: 0;
    left: 0;
    padding: 0;
    width: 300px;
    overflow: hidden;
    outline: 0;
    background-clip: padding-box;
    background-color: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.3);
    border-radius: 6px 6px 6px 6px;
    -webkit-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
    /* Safari 4 */
    -moz-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
    /* Firefox 3.6 */
    box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
    /*left: 50%;
     margin-left: -280px;*/
    outline: medium none;
    /*top: 10%;
     width: 560px;*/
    z-index: 1050;
  }
  .ui-dialog .ui-dialog-titlebar {
    /*padding: .4em 1em;*/
    position: relative;
    border: 0 0 0 1px solid;
    border-color: white;
    padding: 5px 15px;
    font-size: 18px;
    text-decoration: none;
    -webkit-border-bottom-right-radius: 0;
    /* Safari 4 */
    -moz-border-radius-bottomright: 0;
    /* Firefox 3.6 */
    border-bottom-right-radius: 0;
    -webkit-border-bottom-left-radius: 0;
    /* Safari 4 */
    -moz-border-radius-bottomleft: 0;
    /* Firefox 3.6 */
    border-bottom-left-radius: 0;
    border-bottom: 1px solid #ccc;
  }
  .ui-dialog .ui-dialog-title {

    color: #404040;
    font-weight: bold;
    margin-top: 5px;
    margin-bottom: 5px;
    padding: 5px;
    text-overflow: ellipsis;
    overflow: hidden;
  }
  .ui-dialog .ui-dialog-titlebar-close {
    position: absolute;
    right: .3em;
    top: 50%;
    width: 19px;
    margin: -20px 0 0 0;
    padding: 1px;
    height: 18px;
    font-size: 20px;
    font-weight: bold;
    line-height: 13.5px;
    text-shadow: 0 1px 0 #ffffff;
    filter: alpha(opacity=25);
    -khtml-opacity: 0.25;
    -moz-opacity: 0.25;
    opacity: 0.25;
    background: #fff;
    border-width: 0;
    border: none;
    -webkit-box-shadow: none;
    /* Safari 4 */
    -moz-box-shadow: none;
    /* Firefox 3.6 */
    box-shadow: none;
  }
  .ui-dialog .ui-dialog-titlebar-close span {
    display: block;
    margin: 1px;
    text-indent: 9999px;
  }
  .ui-dialog .ui-dialog-titlebar-close:hover,
  .ui-dialog .ui-dialog-titlebar-close:focus {
    padding: 1px;
    filter: alpha(opacity=90);
    -moz-opacity: 0.90;
    opacity: 0.90;
  }
  .ui-dialog .ui-dialog-content {
    position: relative;
    border: 0;
    padding: .5em 1em;
    background: none;
    overflow: auto;
  }
  .ui-dialog .ui-dialog-buttonpane {
    text-align: left;
    border-width: 1px 0 0 0;
    background-image: none;
    margin: .5em 0 0 0;
    background-color: #ffffff;
    padding: 5px 15px 5px;
    border-top: 1px solid #ddd;
    -webkit-border-radius: 0 0 6px 6px;
    /* Safari 4 */
    -moz-border-radius: 0 0 6px 6px;
    /* Firefox 3.6 */
    border-radius: 0 0 6px 6px;
    -webkit-box-shadow: inset 0 1px 0 #ffffff;
    /* Safari 4 */
    -moz-box-shadow: inset 0 1px 0 #ffffff;
    /* Firefox 3.6 */
    box-shadow: inset 0 1px 0 #ffffff;
    margin-bottom: 0;
  }
  .ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
    float: right;
  }
  .ui-dialog .ui-dialog-buttonpane button {
    margin: .5em .4em .5em 0;
    cursor: pointer;
  }
  .ui-dialog .ui-resizable-se {
    width: 14px;
    height: 14px;
    right: 3px;
    bottom: 3px;
  }
  .ui-draggable .ui-dialog-titlebar {
    cursor: move;
  }
  .ui-dialog-buttonpane .ui-dialog-buttonset .ui-button {
    color: #ffffff;
    background-color: #428bca;
    border-color: #357ebd;
  }
  .ui-dialog-buttonpane .ui-dialog-buttonset .ui-button.ui-state-hover {
    color: #ffffff;
    background-color: #3276b1;
    border-color: #285e8e;
  }
  /***Dialog fixes**/
  .ui-dialog-buttonset .ui-button:not(:first-child) {
    cursor: pointer;
    display: inline-block;
    color: #333333;
    background-color: #ffffff;
    border: 1px solid #cccccc;
    -webkit-transition: 0.1s linear all;
    -moz-transition: 0.1s linear all;
    -o-transition: 0.1s linear all;
    transition: 0.1s linear all;
    overflow: visible;
  }
  .ui-dialog-buttonset .ui-button:not(:first-child) .ui-state-hover {
    color: #333333;
    background-color: #ebebeb;
    border-color: #adadad;
    text-decoration: none;
  }
  /* ui-dialog-buttonset UI info */
  .ui-dialog-buttonset .ui-button.ui-button-info {
    color: #ffffff;
    background-color: #5bc0de;
    border-color: #46b8da;
  }
  .ui-dialog-buttonset .ui-button.ui-button-info.ui-state-hover {
    color: #ffffff;
    background-color: #39b3d7;
    border-color: #269abc;
  }
  /* ui-dialog-buttonset UI success */
  .ui-dialog-buttonset .ui-button.ui-button-success {
    color: #ffffff;
    background-color: #5cb85c;
    border-color: #4cae4c;
  }
  .ui-dialog-buttonset .ui-button.ui-button-success.ui-state-hover {
    color: #ffffff;
    background-color: #47a447;
    border-color: #398439;
  }
  /* ui-dialog-buttonset UI warning */
  .ui-dialog-buttonset .ui-button.ui-button-warning {
    color: #ffffff;
    background-color: #f0ad4e;
    border-color: #eea236;
  }
  .ui-dialog-buttonset .ui-button.ui-button-warning.ui-state-hover {
    color: #ffffff;
    background-color: #ed9c28;
    border-color: #d58512;
  }
  /* ui-dialog-buttonset UI Danger */
  .ui-dialog-buttonset .ui-button.ui-button-danger {
    color: #ffffff;
    background-color: #d9534f;
    border-color: #d43f3a;
  }
  .ui-dialog-buttonset .ui-button.ui-button-danger.ui-state-hover {
    color: #ffffff;
    background-color: #d2322d;
    border-color: #ac2925;
  }
  /* ui-dialog-buttonset UI Inverse */
  .ui-dialog-buttonset .ui-button.ui-button-inverse {
    color: #ffffff;
    background-color: #222222;
    border-color: #080808;
  }
  .ui-dialog-buttonset .ui-button.ui-button-inverse.ui-state-hover {
    color: #ffffff;
    background-color: #363636;
    border-color: #000000;
  }

  /* BACKGROUNDS     */
  .bg-color-blue {
    background-color: #57889c !important;
  }
  .bg-color-blueLight {
    background-color: #92a2a8 !important;
  }
  .bg-color-blueDark {
    background-color: #4c4f53 !important;
  }
  .bg-color-green {
    background-color: #356e35 !important;
  }
  .bg-color-greenLight {
    background-color: #71843f !important;
  }
  .bg-color-greenDark {
    background-color: #496949 !important;
  }
  .bg-color-red {
    background-color: #a90329 !important;
  }
  .bg-color-yellow {
    background-color: #b09b5b !important;
  }
  .bg-color-orange {
    background-color: #c79121 !important;
  }
  .bg-color-orangeDark {
    background-color: #a57225 !important;
  }
  .bg-color-pink {
    background-color: #ac5287 !important;
  }
  .bg-color-pinkDark {
    background-color: #a8829f !important;
  }
  .bg-color-purple {
    background-color: #6e587a !important;
  }
  .bg-color-darken {
    background-color: #404040 !important;
  }
  .bg-color-lighten {
    background-color: #d5e7ec !important;
  }
  .bg-color-white {
    background-color: #ffffff !important;
  }
  .bg-color-grayDark {
    background-color: #525252 !important;
  }
  .bg-color-magenta {
    background-color: #6e3671 !important;
  }
  .bg-color-teal {
    background-color: #568a89 !important;
  }
  .bg-color-redLight {
    background-color: #a65858 !important;
  }

  /*
   * JQUI ADJUSTMENT
   */
  .ui-dialog {
    -webkit-box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    border: 1px solid #999999;
    border: 1px solid rgba(0, 0, 0, 0.2);
  }
  .widget-header > :first-child {
    margin: 13px 0;
  }
  .ui-widget-overlay {
    z-index: 999;
  }
  .ui-dialog .ui-dialog-titlebar {
    padding: 0 10px;
    background: #ffffff;
    border-bottom-color: #eeeeee;
  }
  .ui-dialog .ui-dialog-title {
    margin: 0;
  }
  .ui-dialog .ui-dialog-titlebar-close {
    margin-top: -16px;
    margin-right: 4px;
  }
  .ui-dialog-titlebar-close:before {
    content: "\f00d";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-size: 13px;
  }
  .ui-dialog .ui-dialog-buttonpane button {
    margin: 0 .4em 0 0;
  }
  .ui-dialog .ui-dialog-buttonpane {
    margin-top: 13px;
    padding: 19px 15px 20px;
    text-align: right;
    border-top: 1px solid #eeeeee;
  }
  
  a.fc-event, .fc-event-draggable {
      cursor: pointer;
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
          editable: false,
          droppable: false,
          timeFormat: "h:mma",
          slotLabelFormat: "h:mma",
         
          eventClick:  function(event, jsEvent, view) {
            //alert('aqui');
            //set the values and open the modal
            $("#startTime").html(moment(event.start).format('MMM Do h:mm A'));
            $("#endTime").html(moment(event.end).format('MMM Do h:mm A'));
            $("#eventUnidad").html(event.un_id);
            $("#eventInfo").html(event.description);
            $("#eventLink").attr('href', event.url);
            
            //$("#eventContent").dialog({ modal: true, title: event.title });
            $("#eventContent").dialog({ modal: true, title: event.title, width:350, resizable: false, draggable: false});
          },

          events: { url:"cargaEventos" },                
     
          windowResize: function (event, ui) {
              $('#calendar').fullCalendar('render');
          }
      });
    
    })
  </script>
@stop