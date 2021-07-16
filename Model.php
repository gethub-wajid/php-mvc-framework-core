<?php

namespace app\Core;

use app\Models\User;

abstract class Model
{
    /** @var array errors */
    public $errors = [];
    /** @var const $RULL_REQ */
    public $RULL_REQ = "Required";
    /** @var const $RULL_EMAIL */
    public $RULL_EMAIL = "CorrectEmail";
    /** @var const $RULL_MIN */
    public $RULL_MIN = "MinLength";
    /** @var const $RULL_MAX */
    public $RULL_MAX = "MaxLength";
    /** @var const $RULL_MATCH */
    public $RULL_MATCH = "MustMatch";
    /** @var const $RULL_UNIQUE */
    public $RULL_UNIQUE = "Unique";

    public function labels():array{
        return [];
    }
    public function validateData(){
        foreach ($this->rules() as $attribute => $rules){
            $value = $this->{$attribute};
            foreach ($rules as $rule){
                $ruleName = $rule;
                if(!is_string($ruleName)){
                    $ruleName = $rule[0];
                }
                if($ruleName === $this->RULL_REQ && !$value){
                    $this->addErrorForRule($attribute , $this->RULL_REQ);
                }
                if($ruleName === $this->RULL_EMAIL && !filter_var($value , FILTER_VALIDATE_EMAIL)){
                    $this->addErrorForRule($attribute , $this->RULL_EMAIL);
                }
                if($ruleName === $this->RULL_MIN && strlen($value) < $rule['min']){
                    $this->addErrorForRule($attribute , $this->RULL_MIN , $rule);
                }
                if($ruleName === $this->RULL_MAX && strlen($value) > $rule['max']){
                    $this->addErrorForRule($attribute, $this->RULL_MAX , $rule);
                }
                if($ruleName === $this->RULL_MATCH && $value !== $this->{$rule['match']}){
                    $this->addErrorForRule($attribute , $this->RULL_MATCH , $rule);
                }
                if($ruleName === $this->RULL_UNIQUE){
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(":attr" , $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if($record){
                        $this->addErrorForRule($attribute , $this->RULL_UNIQUE , ['field' => $attribute]);
                    }

                }
            }
        }
        if($this->errors == [])
            return true;
        return false;
    }
    public function hasError($attribute){
        return $this->errors[$attribute] ?? false;
    }
    public function getFirstError($attribute){
        return $this->errors[$attribute][0] ?? false;
    }
    public function addErrorForRule($attribute , $rule , $params = []){
        $message = $this->errorMessage()[$rule] ?? '';
        foreach ($params as $key => $value){
            $message = str_replace("{{$key}}" , $value , $message);
        }
        $this->errors[$attribute][] = $message;
    }
    public function addError(string $attribute , string $message){
        $this->errors[$attribute][] = $message;
    }
    public function errorMessage(){
        return[
          $this->RULL_REQ => "This field is required",
          $this->RULL_MATCH => "This field must be same as {match}",
          $this->RULL_EMAIL => "Please enter a valid email",
          $this->RULL_MIN => "Minimum password length is {min}",
          $this->RULL_MAX => "Maximum password length is {max}",
          $this->RULL_UNIQUE => "Record with this {field} already exist",
        ];
    }
    public function loadData($data){
        foreach ($data as $key => $value){
            if(property_exists($this , $key)){
                $this->{$key} = $value;
            }
        }
    }
    abstract public function rules():array;
}