<?php

namespace app\Core\Forms;

use app\Core\Model;

abstract class BaseField
{
    /** @var Model $model */
    public $model;
    /** @var string $attribute*/
    public $attribute;

    /**
     * InputField constructor.
     * @param Model $model
     */
    public function __construct(Model $model , $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }
    public function __toString(){
        return sprintf(
            '
            <div class="form-group">
                <label >%s</label>
                    %s
                <div class="invalid-feedback">%s</div>
            </div>
            '
            ,
            $this->model->labels()[$this->attribute] ?? $this->attribute,
            $this->renderInput(),
            $this->model->getFirstError($this->attribute));
    }
    abstract public function renderInput(): string;
}