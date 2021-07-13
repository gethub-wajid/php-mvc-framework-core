<?php


namespace app\Core;


class View
{
    /** @var string $title */
    public $title = '';

    public function renderView($view , $params =[]){
        $viewContent = $this->renderViewOnly($view , $params);
        $layoutContent = $this->layoutContent();
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
    }


}