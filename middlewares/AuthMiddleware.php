<?php

namespace app\Core\middlewares;

use app\Core\Application;
use app\Core\exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    /** @var array $actions*/
    public $actions = [];

    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }
    public function execute()
    {
        if(Application::isGuest()){
            if(empty($this->actions) || in_array(Application::$app->controller->action , $this->actions)){
                throw new ForbiddenException();
            }
        }
    }
}