<?php

/* Campos adicionais do usuário */

add_action('edit_user_profile', 'consulta_edit_user_details');
add_action('show_user_profile', 'consulta_edit_user_details');

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
                
                    <option value="<?php echo $s->sigla; ?>"  <?php if(get_user_meta($user->ID, 'estado', true) == $s->sigla) echo 'selected'; ?>  >
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

add_action('personal_options_update', 'consulta_save_user_details');
add_action('edit_user_profile_update', 'consulta_save_user_details');
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

    $uf_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM uf WHERE sigla LIKE %s", $uf));

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

function consulta_get_states() {
    global $wpdb;
    return $wpdb->get_results("SELECT * from uf ORDER BY sigla");
}

function da_get_categorias() {

    // Empresa, Associação, Coletivo, Players, Editores, Usuário...?
    $categorias = array('empresa' => 'Empresa',
                        'associacao' => 'Associação',
                        'coletivo' => 'Coletivo',
                        'players' => 'Players',
                        'editores' => 'Editores',
                        'usuario' => 'Usuário');
    return $categorias;

}



function da_get_segmentos() {
    
    // Área de atuação - Peguei da consulta anterior, verifcar se vai continuar
    $segmentos = array( 'advocacia'                         => 'Advocacia',
                        'artista_conexo'                    => 'Artista conexo',
                        'associacao_titulares'              => 'Associação de titulares',
                        'autoria'                           => 'Autoria',
                        'educacao_pesquisa'                 => 'Educação e Pesquisa',
                        'edicao_musical'                    => 'Edição musical',
                        'imprensa_escrita'                  => 'Imprensa escrita',
                        'profissional_area_cultura'         => 'Profissional da área da Cultura',
                        'preservacao_conservacao'           => 'Preservação e conservação',
                        'producao_gravacao_musical'         => 'Edição musical',
                        'radiofusao_audiovisual'            => 'Radiodifusão ou exibição audiovisual',
                        'titular_direitos_patrimoniais'     => 'Titular de direitos patrimoniais',
                        'usuario'                           => 'Usuário',
                        'turismo_diversao'                  => 'Turismo e diversão',
                        'outro_segmento'                    => 'Outro segmento');
    return $segmentos;
}



function is_valid_cpf_or_cnpj($cpf_cnpj){

    if( empty($cpf_cnpj))
        return false;

    if( sizeof($cpf_cnpj ) < 11 )
        return is_a_valid_cpf($cpf_cnpj);
    else
        return is_a_valid_cnpj($cpf_cnpj);
}

function is_a_valid_cpf($cpf) {
    $error = __("O CPF fornecido é inválido.");
    $cpf = preg_replace('/[^0-9]/','',$cpf);

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

function is_a_valid_cnpj($cnpj) {
    $error = __("O CNPJ fornecido é inválido.");
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

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

function user_cpf_cnpj_does_not_exist($c) {
    global $wpdb;

    $result = $wpdb->get_var($wpdb->prepare("SELECT count(1) FROM {$wpdb->usermeta} WHERE"
                                        ." meta_key='cpf_cnpj' and meta_value='%s';",$c));
    
    if( $result > 0 ) {
        return false;
    }

    return true;
}