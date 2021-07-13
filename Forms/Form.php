<?php

namespace app\Core\Forms;

use app\Core\Model;
use app\Models\User;
use app\Core\Forms;

class Form
{
    public static function begin($action , $method){
        echo sprintf('<form action="%s" method="%s">' , $action , $method);
        return new Form();
    }
    public static function end(){
        echo '</form>';
    }
    public function field(Model $model , $attribute){
        return new InputField($model , $attribute);
    }

}