<?php 
class Validator {
    public $fields_rules = array(
        'register' => array(
            'user_login' => array('not_empty','is_a_valid_username','is_username_exists'),
            'user_cpf' => array('not_empty','is_a_valid_cpf', 'user_cpf_does_not_exist'),
            'user_name' => array('not_empty'),
            'user_email' => array('not_empty','is_valid_email','is_email_does_not_exist'),
            'estado' => array('not_empty'),
            'municipio' => array('not_empty'),
            'segmento' => array('not_empty'),
            'nome_instituicao' => array('not_empty'),
            'cnpj_instituicao' => array('not_empty','is_a_valid_cnpj'),
            'tipo_manifestacao' => array('not_empty'),
            'accept_the_terms_of_site' => array('not_empty')
            
        )
    );

    /**
    * Return 'true' if field is valid, an error message if field is invalid
    * or 'null' if field is not recognized
    *
    * @param String $s the step
    * @param String $f the field
    * @param String $v the values ...
    */
    function validate_field($s, $f, $v) {
        $args_v = array_slice(func_get_args(), 2);

        if(isset($this->fields_rules[$s]) && isset($this->fields_rules[$s][$f])) {
            foreach($this->fields_rules[$s][$f] as $function) {
                $result = call_user_func_array(array($this, $function), $args_v);

                if($result !== true) {
                    return $result;
                }
            }
            return true;
        }
        return null;
    }

    /** @return true if field is require and false otherwise */
    function is_required_field($s, $f) {
        return isset($this->fields_rules[$s])
               && isset($this->fields_rules[$s][$f])
               && in_array('not_empty',($this->fields_rules[$s][$f]));
    }

    /** Return true if parameter is not empty or a message otherwise */
    static function not_empty($v) {
        if(!isset($v) || empty($v)) {
            return __('Este item não pode ser vazio');
        }
        return true;
    }

    /** Return true if supplied email is valid or give an error message otherwise */
    static function is_valid_email($e) {
        if(filter_var($e, FILTER_VALIDATE_EMAIL) === $e) {
            return true;
        }
        return __('O e-mail não tem um formato válido');
    }

    /** Return true if supplied email is valid or give an error message otherwise */
    static function is_email_does_not_exist($e) {

        if( email_exists( $e ) ) {
            return __('Já existe um usuário com o e-mail informado'); 
        }
        return true;
       
    }

    /** Return true if supplied cpf is valid or give an error message otherwise */
    static function is_a_valid_cpf($cpf) {
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

    static function user_cpf_does_not_exist($c) {
        global $wpdb;

        $result = $wpdb->get_var($wpdb->prepare("SELECT count(1) FROM {$wpdb->usermeta} WHERE"
                                            ." meta_key='user_cpf' and meta_value='%s';",$c));
        
        if($result > 0) {
            return __('Já existe um usuário cadastrado com este CPF. <a href="' . wp_lostpassword_url() .'">Recuperar senha?</a>');
        }
        return $result == 0; // $result provavelmente é String
    }

    static function is_a_valid_cnpj($cnpj) {
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
    

    static function is_a_valid_date($d) {

        $format = "d/m/Y";

        $dateTime = DateTime::createFromFormat($format, $d);
        
        $errors = DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return __( 'Formato de data inválido. Por favor apague e tente novamente.');
        }
        return true;
    }

   
    static function str_length_less_than_400($v) {
        if(strlen(utf8_decode($v)) > 400) { // php não sabe contar utf8
            return __('O texto não deve exceder 400 caracteres.');
        }
        return true;
    }

    static function is_a_valid_username($u) {
        if(!validate_username($u)) { 
            return __('Nome de usuário é inválido. Remova caracteres especiais e espaços.');
        }
        return true;
    }

    static function is_username_exists($u) {
        if(username_exists($u)) { 
            return __('Este nome de usuário não está disponível.');
        }
        return true;
    }

    

}

 ?>