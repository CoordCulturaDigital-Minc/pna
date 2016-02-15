<?php

// classes
require_once( dirname(__FILE__). '/validator.class.php' );
require_once( dirname(__FILE__). '/filter.class.php' );

/** save field in database, if field is valid */
function cdbr_register_verify_field() {

    $reponse = array();

    $filter = new Filter();
    $validator = new Validator();

    foreach($_POST as $stepfield => $value) {
        
        $field = $stepfield;
    
        $filter->apply('register', $field, $value);

        $result = $validator->validate_field('register', $field, $value, $type_user );

        $response[$field] = $result;
    }
    print json_encode($response);
    die; // or wordpress will print 0
}
add_action('wp_ajax_cdbr_register_verify_field', 'cdbr_register_verify_field');
add_action('wp_ajax_nopriv_cdbr_register_verify_field', 'cdbr_register_verify_field');

/* Campos adicionais do usuário */

// add_action('edit_user_profile', 'consulta_edit_user_details');
// add_action('show_user_profile', 'consulta_edit_user_details');

function consulta_edit_user_details($user) {

    ?>
    <table class="form-table">
    
    <tr>
    
        <th><label>Estado</label></th>
        <td>
            <select  tabindex='16'  name="estado" id="estado"  >
                <option value=""> Selecione </option>
                
                <?php $states = consulta_get_states(); ?>
                <?php foreach ($states as $s): ?>
                
                    <option value="<?php echo $s->nome; ?>"  <?php if(get_user_meta($user->ID, 'estado', true) == $s->nome) echo 'selected'; ?>  >
                        <?php echo $s->nome; ?>
                    </option>
                
                <?php endforeach; ?>
                
            </select>
        </td>
    
    </tr>
    
    <tr>
    
        <th><label>Município</label></th>
        <td>
            <input type="hidden" id="disable_first_municipio_ajax_call" value="1" />
            <select name="municipio" id="municipio">
                <?php echo consulta_get_cities_options(get_user_meta($user->ID, 'estado', true), get_user_meta($user->ID, 'municipio', true)); ?>
            </select>
        </td>
    
    </tr>
    
    </table>
    
    <?php
    
}

// add_action('personal_options_update', 'consulta_save_user_details');
// add_action('edit_user_profile_update', 'consulta_save_user_details');
/**
 * Save creators custom fields add via 
 * administrative profile edit page.
 * 
 * @param int $user_id
 * @return null
 */
function consulta_save_user_details($user_id) {

    update_user_meta($user_id, 'estado', $_POST['estado']);
    update_user_meta($user_id, 'municipio', $_POST['municipio']);
}

function consulta_get_cities_options($uf, $selected = '') {
    global $wpdb;

// var_dump($selected);

    $uf_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM uf WHERE nome LIKE %s", $uf));

    if (!$uf_id) {
        return "<option value=''>Selecione um estado...</option>";
    }

    $cidades = $wpdb->get_results($wpdb->prepare("SELECT * FROM municipio WHERE ufid = %d order by nome", $uf_id));
    
    $o = '';
    
    
    if (is_array($cidades) && count($cidades) > 0) {

        foreach ($cidades as $cidade) {
            $sel = $cidade->nome == $selected ? 'selected' : '';
            $o .= "<option value='{$cidade->nome}' $sel>{$cidade->nome}</option>";
        }

    }
    
    return $o;
    
}

function consulta_print_cities_options() {

    echo consulta_get_cities_options($_POST['uf'], $_POST['selected']);
    die;
}
add_action('wp_ajax_nopriv_consulta_get_cities_options', 'consulta_print_cities_options');
add_action('wp_ajax_consulta_get_cities_options', 'consulta_print_cities_options');

function cdbr_get_states() {
    global $wpdb;
    return $wpdb->get_results("SELECT * from uf ORDER BY sigla");
}

function cdbr_get_segmentos() {

    // Área de atuação - Peguei da consulta anterior, verifcar se vai continuar
    $segmentos = array( 'academia'                  => 'Academia',
                        'advocacia'                 => 'Advocacia',
                        'agregador_conteudo'        => 'Agregador de conteúdo',
                        'artista'                   => 'Artista',
                        'associacao_titulares'      => 'Associação de titulares',
                        'autor'                     => 'Autor',
                        'editora'                   => 'Editora',
                        'gravadora'                 => 'Gravadora',
                        'outro_segmento'            => 'Outro segmento',
                        'outro_tipo_usuario'        => 'Outro tipo de usuário',
                        'plataforma_digital'        => 'Plataforma digital',
                        'profissional_area_cultura' => 'Profissional da área de cultura',
                        'radiodifusao'              => 'Radiodifusão',
                        'sindicato'                 => 'Sindicato',
                        'sociedade_civil'           => 'Sociedade civil');
    return $segmentos;
}

function cdbr_get_label_segmento( $segmento ) {

    $segmentos = cdbr_get_segmentos();

    return $segmentos[$segmento];

}

function cdbr_is_a_valid_cpf($cpf) {
    $error = __("O CPF fornecido é inválido.");
    $cpf = preg_replace('/[^0-9]/','',$cpf);

    if( !is_numeric( $cpf ) or $cpf == '00000000000' or $cpf == '11111111111' or $cpf == '22222222222' or $cpf == '33333333333' or $cpf == '44444444444' or $cpf == '55555555555' or $cpf == '66666666666' or $cpf == '77777777777' or $cpf == '88888888888' or $cpf == '99999999999' )
        return $error;

    if(strlen($cpf) !=  11 || preg_match('/^([0-9])\1+$/', $cpf)) {
        return $error;
    }

    // 9 primeiros digitos do cpf
    $digit = substr($cpf, 0, 9);

    // calculo dos 2 digitos verificadores
    for($j=10; $j <= 11; $j++){
        $sum = 0;
        for($i=0; $i< $j-1; $i++) {
            $sum += ($j-$i) * ((int) $digit[$i]);
        }

        $summod11 = $sum % 11;
        $digit[$j-1] = $summod11 < 2 ? 0 : 11 - $summod11;
    }

    if($digit[9] == ((int)$cpf[9]) && $digit[10] == ((int)$cpf[10])) {
        return true;
    } else {
        return $error;
    }
}

function cdbr_is_a_valid_cnpj($cnpj) {
    $error = __("O CNPJ fornecido é inválido.");
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    if( !is_numeric( $cnpj ) or $cnpj == '00000000000000' or $cnpj == '11111111111111' or $cnpj == '22222222222222' or $cnpj == '33333333333333' or $cnpj == '44444444444444' or $cnpj == '55555555555555' or $cnpj == '66666666666666' or $cnpj == '77777777777777' or $cnpj == '88888888888888' or $cnpj == '99999999999999' )
        return $error;

    if(strlen($cnpj) != 14) {
        return $error;
    }

    $mask = array(6,5,4,3,2,9,8,7,6,5,4,3,2);

    $a = array();
    $b = 0;
    for($i=0; $i < 12; $i++) {
        $a[] = (int) $cnpj[$i];
        $b += $a[$i] * $mask[$i+1];
    }

    $x = $b % 11;
    if($x < 2) {
        $a[12] = 0;
    } else {
        $a[12] = 11 - $x;
    }

    $b = 0;
    for($i=0; $i < 13; $i++) {
        $b += $a[$i] * $mask[$i];
    }

    $x = $b % 11;
    if($x < 2) {
        $a[13] = 0;
    } else {
        $a[13] = 11 - $x;
    }

    if($cnpj[12] == $a[12] && $cnpj[13] == $a[13]) {
        return true;
    }
    return false;
}

function cdbr_user_cpf_does_not_exist($c) {
    global $wpdb;

    $result = $wpdb->get_var($wpdb->prepare("SELECT count(1) FROM {$wpdb->usermeta} WHERE"
                                        ." meta_key='user_cpf' and meta_value='%s';",$c));
    
    if( $result > 0 ) {
        return false;
    }

    return true;
}

function cdbr_get_user_meta($user_id, $field_id, $single=true) {

    // if( function_exists('bp_is_active') )
    //     $meta= xprofile_get_field_data( $field_id, $user_id );
    // else
    $meta = get_user_meta($user_id, $field_id, $single);

    return $meta; 
}

function cdbr_add_user_meta( $user_id, $field_id, $current_field) {
    if( empty( $user_id ) )
        return false;

    if( empty( $field_id ))
        return false;

    if( function_exists('bp_is_active') )
        xprofile_set_field_data( $field_id, $user_id, $current_field);
    
    $meta = update_user_meta($user_id, $field_id,$current_field);

    return $meta;
}

function cdbr_update_user_terms_current_site( $user_id ) {
    
    if( empty( $user_id ) )
        return false;

   global $wpdb;

   $today = gmdate('Y-m-d H:i:s' );

   return update_user_meta( $user_id, $wpdb->prefix . 'accept_the_terms_of_site', $today);  
}

function cdbr_get_user_terms_current_site( $user_id ) {

    if( empty( $user_id ) )
        return false;

    global $wpdb;

    if( get_user_meta( $user_id, $wpdb->prefix . 'accept_the_terms_of_site', true ) )
        return true;
    else
        return false;
}

function cdbr_send_email_register( $user_email, $user_login, $user_password ) {

    if( empty( $user_email ) )
        return false;

    $from = get_option('admin_email');
    $headers = 'From: '.$from . "\r\n";
    $subject = "Cadastro " . get_bloginfo('name');
    $redefine_pass = network_site_url(bp_get_members_slug() ."/" . $user_login . "/settings");

    $msg = "Você foi cadastrado com sucesso no site " . get_bloginfo('name')
     ."\nDetalhes para acesso"
     ."\nNome de usuário: $user_login"
     ."\nSenha: $user_password"
     ."\nAcesse: ". get_bloginfo('url')
     ."\n\nPara redefinir sua senha acesse: " . $redefine_pass; 

    wp_mail( $user_email, $subject, $msg, $headers );
}

function cdbr_get_user_all_data($user_id) {

    $user       = get_user_by( 'id', $user_id);
    $user_meta  = array_map( function( $a ){ return $a[0]; }, get_user_meta( $user_id ) );

    if( is_object( $user  ) )
      $user = get_object_vars( $user->data);

    return array_merge($user, $user_meta);
}

//usuario atualizou o perfil?
function cdbr_current_user_updated_profile()  { 
    
    $user_ID        = get_current_user_id();
    $update_profile = false;

    if( empty($user_ID))
        return false;

    if( $user_ID ) {
        $cpf_registered = get_user_meta($user_ID, 'user_cpf', true);
        $is_foreign     = get_user_meta($user_ID, 'estrangeiro', true);

        if( !empty( $cpf_registered ) || !empty($is_foreign) )
            $update_profile = true;
    }

    return $update_profile;   
}


function cdbr_ajax_current_user_updated_profile() {

    if( !is_user_logged_in() )
        return true;

    if( !cdbr_admin_user_update_profile() )
        return true;

    echo cdbr_current_user_updated_profile();
               
    die;
}
add_action('wp_ajax_current_user_updated_profile', 'cdbr_ajax_current_user_updated_profile',2);

//TODO: temporário, aqui ele pega as configuracoes do wp-side-comments, trocar para uma opcao do tema
function cdbr_admin_user_update_profile() {

    global $WPSideCommentsAdmin;

    if( isset($WPSideCommentsAdmin) ) {
        if( $WPSideCommentsAdmin->isConfirmTermsAllowed())
            return true;
    }

    return false;
}

