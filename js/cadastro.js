jQuery(document).ready(function() {

    var municipio_ajax_first_call = true;
    
    jQuery('#estado').change(function() {
        
        if (jQuery(this).val() != '') {
            jQuery('#municipio').html('<option value="">Carregando...</option>');
            
            jQuery.ajax({
                url: vars.ajaxurl, 
                type: 'post',
                data: {action: 'consulta_get_cities_options', uf: jQuery('#estado').val(), selected: jQuery('#municipio').val()},
                success: function(data) {
                    jQuery('#municipio').html(data);
                } 
            });
        }
        
        
    })
    
    if (jQuery('#disable_first_municipio_ajax_call').size() == 0)
        jQuery('#estado').change();

    // jQuery("#cpf_cnpj").keypress(function(){
    //     if( jQuery(this).hasClass('cpf')) {
    //         jQuery(this).mask("999.999.999-99");
    //     }else {
            
    //     }
    // });

    jQuery("#user_cpf").mask("999.999.999-99");
    jQuery("#cnpj_instituicao").mask("99.999.999/9999-99");


    jQuery("input[name=tipo_manifestacao]:radio").change(
        function(){

            if( jQuery("input[name=tipo_manifestacao]:radio:checked").val() == 'individual' ) {
                jQuery("#instituicao").slideUp('fast');
                jQuery("#nome_instituicao").removeAttr("required");
                jQuery("#cnpj_instituicao").removeAttr("required");
            } else{
                jQuery("#instituicao").hide().slideDown('fast');
                jQuery("#nome_instituicao").attr("required","required");
                jQuery("#cnpj_instituicao").attr("required","required");
                
            }
        }   
    );

    jQuery('.nao_tenho_cpf').on('click', function(){
        jQuery('#cdbr_dialog').remove();

        var widget_title = 'Ajuda';
        var widget_text = "Se você é estrangeiro e não tem CPF, envie um email com uma cópia do <br>seu documento para <a href='mailto:" + vars.admin_email + "'>"+ vars.admin_email +".<a>";

        jQuery('<div id="cdbr_dialog"></div>').appendTo( jQuery( "body" ) )
          .html('<div id="dialog-confirm" title="'+widget_title+'"><p>'+widget_text+'</p></div');

        jQuery('#dialog-confirm').dialog({
            resizable: false,
            draggable: false,
            modal: true,
            // closeOnEscape: false,
            buttons: {
                "Ok": function() {
                    jQuery( this ).dialog( "close" );
                    jQuery('#cdbr_dialog').remove();
                }
            }
        });
        
        return false;        
    });
});

(function($) {
    $(document).ready(function(e) {

    $form = $("#register");

      // callback to verify field through
    var verify_register_field = function(e) {

        var $me = $(this);
        
        var values = {'action': 'cdbr_register_verify_field'};
        
        // no checkbox o ajax pega o valor mesmo sem estar selecionado  
        if( $me.is('input[type="checkbox"]')) {
           if( $me.prop("checked") )
                values[this.name] = $me.val();
        }else 
            values[this.name] = $me.val();

        // values[this.name] = $me.val();
        $.post(vars.ajaxurl, values,

            function(data) {
                console.log(data);
                for(var field in data) {

                    if(data[field] === true && $me.val().length > 0) {
                        $form.find('#'+field+'-error').hide().html('');
                        
                    } else {
                        // $form.find('#'+field).val('');
                        $form.find('#'+field+'-error').html(data[field]).show(); 
                    }
                }
            }, 'json');
    };

    $form.find(':input').blur(verify_register_field)
                        .keydown(function(e){ if(e.keyCode===13){ //enter
                            $(this).trigger('blur').focus();
                        }});

    });
})(jQuery);