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

function cdbr_get_categorias() {

    // Empresa, Associação, Coletivo, Players, Editores, Usuário...?
    $categorias = array('empresa' => 'Empresa',
                        'associacao' => 'Associação',
                        'coletivo' => 'Coletivo',
                        'players' => 'Players',
                        'editores' => 'Editores',
                        'usuario' => 'Usuário');
    return $categorias;

}

function cdbr_get_segmentos() {

    // Área de atuação - Peguei da consulta anterior, verifcar se vai continuar
    $segmentos = array( 'academia'                  => 'Academia',
                        'advocacia'                 => 'Advocacia',
                        'agregadores_conteúdo'      => 'Agregadores de conteúdo',
                        'artista'                   => 'Artista',
                        'associacao_titulares'      => 'Associação de titulares',
                        'autor'                     => 'Autor',
                        'editoras'                  => 'Editoras',
                        'gravadoras'                => 'Gravadoras',
                        'outro_segmento'            => 'Outro segmento',
                        'outros_tipos_usuario'      => 'Outros tipos de usuário',
                        'plataformas_digitais'      => 'Plataformas digitais',
                        'profissional_area_cultura' => 'Profissional da área de cultura',
                        'radiodifusao'              => 'Radiodifusão',
                        'sindicato'                 => 'Sindicato',
                        'sociedade_civil'           => 'Sociedade civil');
    return $segmentos;
}

function cdbr_get_countries_array() {

    $countries = array(
        "Brasil"                                    => "Brasil",
        "Afeganistão"                               => "Afeganistão",
        "África do Sul"                             => "África do Sul",
        "Albânia"                                   => "Albânia",
        "Alemanha"                                  => "Alemanha",
        "Andorra"                                   => "Andorra",
        "Angola"                                    => "Angola",
        "Anguilla"                                  => "Anguilla",
        "Antilhas Holandesas"                       => "Antilhas Holandesas",
        "Antárctida"                                => "Antárctida",
        "Antígua e Barbuda"                         => "Antígua e Barbuda",
        "Argentina"                                 => "Argentina",
        "Argélia"                                   => "Argélia",
        "Armênia"                                   => "Armênia",
        "Aruba"                                     => "Aruba",
        "Arábia Saudita"                            => "Arábia Saudita",
        "Austrália"                                 => "Austrália",
        "Áustria"                                   => "Áustria",
        "Azerbaijão"                                => "Azerbaijão",
        "Bahamas"                                   => "Bahamas",
        "Bahrein"                                   => "Bahrein",
        "Bangladesh"                                => "Bangladesh",
        "Barbados"                                  => "Barbados",
        "Belize"                                    => "Belize",
        "Benim"                                     => "Benim",
        "Bermudas"                                  => "Bermudas",
        "Bielorrússia"                              => "Bielorrússia",
        "Bolívia"                                   => "Bolívia",
        "Botswana"                                  => "Botswana",
        "Brunei"                                    => "Brunei",
        "Bulgária"                                  => "Bulgária",
        "Burkina Faso"                              => "Burkina Faso",
        "Burundi"                                   => "Burundi",
        "Butão"                                     => "Butão",
        "Bélgica"                                   => "Bélgica",
        "Bósnia e Herzegovina"                      => "Bósnia e Herzegovina",
        "Cabo Verde"                                => "Cabo Verde",
        "Camarões"                                  => "Camarões",
        "Camboja"                                   => "Camboja",
        "Canadá"                                    => "Canadá",
        "Catar"                                     => "Catar",
        "Cazaquistão"                               => "Cazaquistão",
        "Chade"                                     => "Chade",
        "Chile"                                     => "Chile",
        "China"                                     => "China",
        "Chipre"                                    => "Chipre",
        "Colômbia"                                  => "Colômbia",
        "Comores"                                   => "Comores",
        "Coreia do Norte"                           => "Coreia do Norte",
        "Coreia do Sul"                             => "Coreia do Sul",
        "Costa do Marfim"                           => "Costa do Marfim",
        "Costa Rica"                                => "Costa Rica",
        "Croácia"                                   => "Croácia",
        "Cuba"                                      => "Cuba",
        "Dinamarca"                                 => "Dinamarca",
        "Djibouti"                                  => "Djibouti",
        "Dominica"                                  => "Dominica",
        "Egito"                                     => "Egito",
        "El Salvador"                               => "El Salvador",
        "Emirados Árabes Unidos"                    => "Emirados Árabes Unidos",
        "Equador"                                   => "Equador",
        "Eritreia"                                  => "Eritreia",
        "Escócia"                                   => "Escócia",
        "Eslováquia"                                => "Eslováquia",
        "Eslovênia"                                 => "Eslovênia",
        "Espanha"                                   => "Espanha",
        "Estados Federados da Micronésia"           => "Estados Federados da Micronésia",
        "Estados Unidos"                            => "Estados Unidos",
        "Estônia"                                   => "Estônia",
        "Etiópia"                                   => "Etiópia",
        "Fiji"                                      => "Fiji",
        "Filipinas"                                 => "Filipinas",
        "Finlândia"                                 => "Finlândia",
        "França"                                    => "França",
        "Gabão"                                     => "Gabão",
        "Gana"                                      => "Gana",
        "Geórgia"                                   => "Geórgia",
        "Gibraltar"                                 => "Gibraltar",
        "Granada"                                   => "Granada",
        "Gronelândia"                               => "Gronelândia",
        "Grécia"                                    => "Grécia",
        "Guadalupe"                                 => "Guadalupe",
        "Guam"                                      => "Guam",
        "Guatemala"                                 => "Guatemala",
        "Guernesei"                                 => "Guernesei",
        "Guiana"                                    => "Guiana",
        "Guiana Francesa"                           => "Guiana Francesa",
        "Guiné"                                     => "Guiné",
        "Guiné Equatorial"                          => "Guiné Equatorial",
        "Guiné-Bissau"                              => "Guiné-Bissau",
        "Gâmbia"                                    => "Gâmbia",
        "Haiti"                                     => "Haiti",
        "Honduras"                                  => "Honduras",
        "Hong Kong"                                 => "Hong Kong",
        "Hungria"                                   => "Hungria",
        "Ilha Bouvet"                               => "Ilha Bouvet",
        "Ilha de Man"                               => "Ilha de Man",
        "Ilha do Natal"                             => "Ilha do Natal",
        "Ilha Heard e Ilhas McDonald"               => "Ilha Heard e Ilhas McDonald",
        "Ilha Norfolk"                              => "Ilha Norfolk",
        "Ilhas Cayman"                              => "Ilhas Cayman",
        "Ilhas Cocos (Keeling)"                     => "Ilhas Cocos (Keeling)",
        "Ilhas Cook"                                => "Ilhas Cook",
        "Ilhas Feroé"                               => "Ilhas Feroé",
        "Ilhas Geórgia do Sul e Sandwich do Sul"    => "Ilhas Geórgia do Sul e Sandwich do Sul",
        "Ilhas Malvinas"                            => "Ilhas Malvinas",
        "Ilhas Marshall"                            => "Ilhas Marshall",
        "Ilhas Menores Distantes dos Estados Unidos"=> "Ilhas Menores Distantes dos Estados Unidos",
        "Ilhas Salomão"                             => "Ilhas Salomão",
        "Ilhas Virgens Americanas"                  => "Ilhas Virgens Americanas",
        "Ilhas Virgens Britânicas"                  => "Ilhas Virgens Britânicas",
        "Ilhas Åland"                               => "Ilhas Åland",
        "Indonésia"                                 => "Indonésia",
        "Inglaterra"                                => "Inglaterra",
        "Índia"                                     => "Índia",
        "Iraque"                                    => "Iraque",
        "Irlanda do Norte"                          => "Irlanda do Norte",
        "Irlanda"                                   => "Irlanda",
        "Irã"                                       => "Irã",
        "Islândia"                                  => "Islândia",
        "Israel"                                    => "Israel",
        "Itália"                                    => "Itália",
        "Iêmen"                                     => "Iêmen",
        "Jamaica"                                   => "Jamaica",
        "Japão"                                     => "Japão",
        "Jersey"                                    => "Jersey",
        "Jordânia"                                  => "Jordânia",
        "Kiribati"                                  => "Kiribati",
        "Kuwait"                                    => "Kuwait",
        "Laos"                                      => "Laos",
        "Lesoto"                                    => "Lesoto",
        "Letônia"                                   => "Letônia",
        "Libéria"                                   => "Libéria",
        "Liechtenstein"                             => "Liechtenstein",
        "Lituânia"                                  => "Lituânia",
        "Luxemburgo"                                => "Luxemburgo",
        "Líbano"                                    => "Líbano",
        "Líbia"                                     => "Líbia",
        "Macau"                                     => "Macau",
        "Macedônia"                                 => "Macedônia",
        "Madagáscar"                                => "Madagáscar",
        "Malawi"                                    => "Malawi",
        "Maldivas"                                  => "Maldivas",
        "Mali"                                      => "Mali",
        "Malta"                                     => "Malta",
        "Malásia"                                   => "Malásia",
        "Marianas Setentrionais"                    => "Marianas Setentrionais",
        "Marrocos"                                  => "Marrocos",
        "Martinica"                                 => "Martinica",
        "Mauritânia"                                => "Mauritânia",
        "Maurícia"                                  => "Maurícia",
        "Mayotte"                                   => "Mayotte",
        "Moldávia"                                  => "Moldávia",
        "Mongólia"                                  => "Mongólia",
        "Montenegro"                                => "Montenegro",
        "Montserrat"                                => "Montserrat",
        "Moçambique"                                => "Moçambique",
        "Myanmar"                                   => "Myanmar",
        "México"                                    => "México",
        "Mônaco"                                    => "Mônaco",
        "Namíbia"                                   => "Namíbia",
        "Nauru"                                     => "Nauru",
        "Nepal"                                     => "Nepal",
        "Nicarágua"                                 => "Nicarágua",
        "Nigéria"                                   => "Nigéria",
        "Niue"                                      => "Niue",
        "Noruega"                                   => "Noruega",
        "Nova Caledônia"                            => "Nova Caledônia",
        "Nova Zelândia"                             => "Nova Zelândia",
        "Níger"                                     => "Níger",
        "Omã"                                       => "Omã",
        "Palau"                                     => "Palau",
        "Palestina"                                 => "Palestina",
        "Panamá"                                    => "Panamá",
        "Papua-Nova Guiné"                          => "Papua-Nova Guiné",
        "Paquistão"                                 => "Paquistão",
        "Paraguai"                                  => "Paraguai",
        "País de Gales"                             => "País de Gales",
        "Países Baixos"                             => "Países Baixos",
        "Peru"                                      => "Peru",
        "Pitcairn"                                  => "Pitcairn",
        "Polinésia Francesa"                        => "Polinésia Francesa",
        "Polônia"                                   => "Polônia",
        "Porto Rico"                                => "Porto Rico",
        "Portugal"                                  => "Portugal",
        "Quirguistão"                               => "Quirguistão",
        "Quênia"                                    => "Quênia",
        "Reino Unido"                               => "Reino Unido",
        "República Centro-Africana"                 => "República Centro-Africana",
        "República Checa"                           => "República Checa",
        "República Democrática do Congo"            => "República Democrática do Congo",
        "República do Congo"                        => "República do Congo",
        "República Dominicana"                      => "República Dominicana",
        "Reunião"                                   => "Reunião",
        "Romênia"                                   => "Romênia",
        "Ruanda"                                    => "Ruanda",
        "Rússia"                                    => "Rússia",
        "Saara Ocidental"                           => "Saara Ocidental",
        "Saint Martin"                              => "Saint Martin",
        "Saint-Barthélemy"                          => "Saint-Barthélemy",
        "Saint-Pierre e Miquelon"                   => "Saint-Pierre e Miquelon",
        "Samoa Americana"                           => "Samoa Americana",
        "Samoa"                                     => "Samoa",
        "Santa Helena, Ascensão e Tristão da Cunha" => "Santa Helena, Ascensão e Tristão da Cunha",
        "Santa Lúcia"                               => "Santa Lúcia",
        "Senegal"                                   => "Senegal",
        "Serra Leoa"                                => "Serra Leoa",
        "Seychelles"                                => "Seychelles",
        "Singapura"                                 => "Singapura",
        "Somália"                                   => "Somália",
        "Sri Lanka"                                 => "Sri Lanka",
        "Suazilândia"                               => "Suazilândia",
        "Sudão"                                     => "Sudão",
        "Suriname"                                  => "Suriname",
        "Suécia"                                    => "Suécia",
        "Suíça"                                     => "Suíça",
        "Svalbard e Jan Mayen"                      => "Svalbard e Jan Mayen",
        "São Cristóvão e Nevis"                     => "São Cristóvão e Nevis",
        "São Marino"                                => "São Marino",
        "São Tomé e Príncipe"                       => "São Tomé e Príncipe",
        "São Vicente e Granadinas"                  => "São Vicente e Granadinas",
        "Sérvia"                                    => "Sérvia",
        "Síria"                                     => "Síria",
        "Tadjiquistão"                              => "Tadjiquistão",
        "Tailândia"                                 => "Tailândia",
        "Taiwan"                                    => "Taiwan",
        "Tanzânia"                                  => "Tanzânia",
        "Terras Austrais e Antárticas Francesas"    => "Terras Austrais e Antárticas Francesas",
        "Território Britânico do Oceano Índico"     => "Território Britânico do Oceano Índico",
        "Timor-Leste"                               => "Timor-Leste",
        "Togo"                                      => "Togo",
        "Tonga"                                     => "Tonga",
        "Toquelau"                                  => "Toquelau",
        "Trinidad e Tobago"                         => "Trinidad e Tobago",
        "Tunísia"                                   => "Tunísia",
        "Turcas e Caicos"                           => "Turcas e Caicos",
        "Turquemenistão"                            => "Turquemenistão",
        "Turquia"                                   => "Turquia",
        "Tuvalu"                                    => "Tuvalu",
        "Ucrânia"                                   => "Ucrânia",
        "Uganda"                                    => "Uganda",
        "Uruguai"                                   => "Uruguai",
        "Uzbequistão"                               => "Uzbequistão",
        "Vanuatu"                                   => "Vanuatu",
        "Vaticano"                                  => "Vaticano",
        "Venezuela"                                 => "Venezuela",
        "Vietname"                                  => "Vietname",
        "Wallis e Futuna"                           => "Wallis e Futuna",
        "Zimbabwe"                                  => "Zimbabwe",
        "Zâmbia"                                    => "Zâmbia");

    return $countries;
}

// function cdbr_is_valid_cpf_or_cnpj($cpf_cnpj){

//     if( empty($cpf_cnpj))
//         return false;

//     if( sizeof($cpf_cnpj ) < 11 )
//         return is_a_valid_cpf($cpf_cnpj);
//     else
//         return is_a_valid_cnpj($cpf_cnpj);
// }

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

   $user_id = get_current_user_id();   

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
    $msg = "Você foi cadastrado com sucesso no site " . get_bloginfo('name')
     ."\nDetalhes para acesso"
     ."\nNome de usuário: $user_login"
     ."\nSenha: $user_password"
     ."\nAcesse: ". get_bloginfo('url');

    wp_mail( $user_email, $subject, $msg, $headers );
}


function cdbr_get_user_all_data($user_id) {

    $user       = get_user_by( 'id', $user_id);
    $user_meta  = array_map( function( $a ){ return $a[0]; }, get_user_meta( $user_id ) );

    if( is_object( $user  ) )
      $user = get_object_vars( $user->data);

    return array_merge($user, $user_meta);
}