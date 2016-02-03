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

    jQuery("#cpf_cnpj").keypress(function(){
        if( jQuery(this).hasClass('cpf')) {
            jQuery(this).mask("999.999.999-99");
        }else {
            jQuery(this).mask("99.999.999/9999-99");
        }
    });

    jQuery("input[name=manifestacao]:radio").change(
        function(){

            if( jQuery("input[name=manifestacao]:radio:checked").val() == 'individual' )
                jQuery("#instituicao").slideUp('fast');
            else
                jQuery("#instituicao").hide().slideDown('fast');
        }   
    );

    jQuery("select#country").change(
        function(){

            if( jQuery(this).val() == 'Brasil' )
                jQuery("#instituicao").slideUp('fast');
            else
                jQuery("#instituicao").hide().slideDown('fast');
        }   
    );
   
});

