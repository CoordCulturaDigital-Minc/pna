(function($) {
    $(document).ready(function(e) {

    $('.logged-in p.commentable-section, .logged-in textarea#comment, .logged-in #submit').on('click', function(){
        $('#cdbr_dialog').remove();

        var widget_title = 'Atualizar cadastro';
        var widget_text = 'Para participar você deve completar o seu cadastro!';
        
        $.post(
            vars.ajaxurl, 
            {
                action: 'current_user_updated_profile'
            },
            function(response) {
                console.log(response);
                if( response == false){


                    $('<div id="cdbr_dialog"></div>').appendTo( $( "body" ) )
                      .html('<div id="dialog-confirm" title="'+widget_title+'"><p>'+widget_text+'</p></div');

                    $('#dialog-confirm').dialog({
                        resizable: false,
                        draggable: false,
                        modal: true,
                        closeOnEscape: false,
                        buttons: {
                            "Atualizar cadastro": function() {
                                $( this ).dialog( "close" );
                                $('#cdbr_dialog').remove();
                                window.location.replace(vars.cadastro_url);
                                return false;
                            },
                            "Depois": function() {
                              $( this ).dialog( "close" );
                               $('#cdbr_dialog').remove();
                              return false;
                            }
                        }
                    });        
                }
            });
        });       
    });
})(jQuery);