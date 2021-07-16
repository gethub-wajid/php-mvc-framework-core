<?php
namespace app\Core\Forms;
use app\Core\Model;

class InputField extends BaseField
{
    /** @var const $TYPE_TEXT*/
    public $TYPE_TEXT = 'text';

    /** @var const $TYPE_PASSWORD*/
    public $TYPE_PASSWORD = 'password';

    /** @var const $TYPE_NUMBER*/
    public $TYPE_NUMBER = 'number';

    /** @var const $TYPE_TIME*/
    public $TYPE_TIME = 'time';


    /** @var Model $model */
    public $model;
    /** @var string $attribute*/
    public $attribute;

    /** @var string $type*/
    public $type;

    /**
     * InputField constructor.
     * @param Model $model
     */
    public function __construct(Model $model , $attribute)
    {
        $this->type = $this->TYPE_TEXT;
        parent::__construct($model , $attribute);
    }
    public function passwordField(){
            $this->type = $this->TYPE_PASSWORD;
            return $this;
    }
    public function timeField(){
        $this->type = $this->TYPE_TIME;
        return $this;
    }
    public function renderInput():string
    {
        return sprintf('<input type="%s" id="%s" name="%s" value="%s" class="form-control%s">' ,
            $this->type,
            $this->attribute ,
            $this->attribute,
            $this->model->{$this->attribute} ,
            $this->model->hasError($this->attribute) ? ' is-invalid':'');
    }
}