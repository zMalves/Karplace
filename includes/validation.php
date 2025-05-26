
<?php
/**
 * Classe para validação de dados
 */
class Validation {
    private $errors = [];
    
    /**
     * Valida se um campo é obrigatório
     */
    public function required($value, $field_name) {
        if (empty(trim($value))) {
            $this->errors[$field_name] = "O campo {$field_name} é obrigatório.";
            return false;
        }
        return true;
    }
    
    /**
     * Valida formato de email
     */
    public function email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Email inválido.";
            return false;
        }
        return true;
    }
    
    /**
     * Valida telefone brasileiro
     */
    public function phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) < 10 || strlen($phone) > 11) {
            $this->errors['phone'] = "Telefone inválido.";
            return false;
        }
        return true;
    }
    
    /**
     * Valida valor monetário
     */
    public function money($value) {
        $value = str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $value);
        if (!is_numeric($value) || $value <= 0) {
            $this->errors['price'] = "Valor monetário inválido.";
            return false;
        }
        return true;
    }
    
    /**
     * Valida ano
     */
    public function year($year) {
        $current_year = date('Y');
        if (!is_numeric($year) || $year < 1900 || $year > ($current_year + 1)) {
            $this->errors['year'] = "Ano inválido.";
            return false;
        }
        return true;
    }
    
    /**
     * Valida quilometragem
     */
    public function mileage($mileage) {
        if (!is_numeric($mileage) || $mileage < 0) {
            $this->errors['mileage'] = "Quilometragem inválida.";
            return false;
        }
        return true;
    }
    
    /**
     * Retorna todos os erros
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Verifica se há erros
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Limpa os erros
     */
    public function clearErrors() {
        $this->errors = [];
    }
}
