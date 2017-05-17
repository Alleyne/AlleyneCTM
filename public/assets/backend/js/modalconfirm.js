$(document).ready(function() {

  //<!-- Dialog show event handler -->
  $('#confirmAction').on('show.bs.modal', function (e) {
    $message = $(e.relatedTarget).attr('data-message');
    $(this).find('.modal-body p').text($message);
    
    $title = $(e.relatedTarget).attr('data-title');
    $(this).find('.modal-title').text($title);
    
    $btntxt = $(e.relatedTarget).attr('data-btntxt');
    $(this).find('.btn-action').text($btntxt);          

    $btncolor = $(e.relatedTarget).attr('data-btncolor');
    $(this).find('.btn-action').addClass($btncolor);           
    
    //$btnicono = $(e.relatedTarget).attr('data-btnicono');
    //$(this).find('.fa-icono').addClass($btnicono); 

    // Pass form reference to modal for submission on yes/ok
    var form = $(e.relatedTarget).closest('form');
    $(this).find('.modal-footer #confirm').data('form', form);
  });

  //<!-- Form confirm (yes/ok) handler, submits form -->
  $('#confirmAction').find('.modal-footer #confirm').on('click', function(){
    $(this).find('#confirm').prop('disabled',true);   
    $(this).data('form').submit();
  });
}); 

