<?php
namespace app\Core;
use app\Controllers\SiteController;
use app\Core\Application;
use app\Core\exception\ForbiddenException;
use app\Core\exception\NotFoundException;

class Router
{
    /** @var Request $request */
    protected $routes = [];

    /**
     * Router constructor.
     * @param Request $request
     */
    /** @var Response $response */
    /**
     * Router constructor.
     * @param Response $response
     */
    public function __construct(Request $request , Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    public function get($path,$callback){
        $this->routes['get'][$path] = $callback;
    }
    public function post($path , $callback){
        $this->routes['post'][$path] = $callback;
    }
    public function resolve(){
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if($callback === false){
            throw new NotFoundException();
        }
        if(is_string($callback)){
            return Application::$app->view->renderView($callback);
        }
        if(is_array($callback)){
            //$controller = Application::$app->controller;
            Application::$app->controller = new $callback[0]();
            Application::$app->controller->action = $callback[1];
            $callback[0] = Application::$app->controller;
            foreach (Application::$app->controller->getMiddlewares() as $middleware){
                $middleware->execute();
            }
        }
        return call_user_func($callback , $this->request , $this->response);
    }
/*    public function renderView($view , $params =[]){
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderViewOnly($view , $params);
        return str_replace("{{content}}" , $viewContent , $layoutContent);
    }
    public function renderContent($viewContent){
        $layoutContent = $this->layoutContent();
        return str_replace("{{content}}" , $viewContent , $layoutContent);
    }
    protected function layoutContent(){
        $layout = Application::$app->layout;
        if(Application::$app->controller){
            $layout = Application::$app->controller->layout;
        }
        if($layout === null)
            $layout = 'main';
        ob_start();
        include_once Application::$ROOT_DIR."/Views/layouts/$layout.php";
        return ob_get_clean();
    }
    protected function renderViewOnly($view , $params=[]){
        foreach ($params as $key=> $value){
            $$key = $value;
        }
        ob_start();
        include_once Application::$ROOT_DIR."/Views/$view.php";
        return ob_get_clean();
    }*/
}