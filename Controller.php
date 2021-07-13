<?php


namespace app\Core;
use app\Core\middlewares\BaseMiddleware;

class Controller
{
    /** @var string $layout */
    public $layout = 'main';
    /** @var string $action */
    public $action = '';

    /** @var BaseMiddleware[] $middlewares */
    protected $middlewares = [];

    public function setLayout($layout){
        $this->layout = $layout;
    }
    public function render($view , $params = []){
        return Application::$app->view->renderView($view , $params);
    }
    public function registerMiddleware(BaseMiddleware $middleware){
        $this->middlewares[] = $middleware;
    }

    /**
     * @return BaseMiddleware[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

}