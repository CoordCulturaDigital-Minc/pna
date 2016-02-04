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

    jQuery("select#pais").change(
        function(){

            if( jQuery(this).val() == 'Brasil' ){
                jQuery("#endereco_nacional").hide().slideDown('fast');
                // jQuery(".disable_first_municipio_ajax_call").removeAttr( "id" );
                jQuery("#estado").attr("required","required");
                jQuery("#municipio").attr("required","required");
            } else {
                jQuery("#endereco_nacional").slideUp('fast');
                 jQuery("#estado").removeAttr("required");
                 jQuery("#municipio").removeAttr("required");
                // jQuery(".disable_first_municipio_ajax_call").attr( "id", "disable_first_municipio_ajax_call");
            } 
        }
    );
   
});

