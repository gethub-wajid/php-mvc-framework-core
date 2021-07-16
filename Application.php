<?php

namespace app\Core;

use app\Core\Router;
use app\Core\Request;
use app\Core\db\Database;
use app\Core\db\DBModel;
use app\Core\Session;
use app\Models\User;

class Application
{
    /** @var string $layout */
    public $layout = '';
    /** @var string $userClass */
    public $userClass;
    /** @var string $userType */
    public $userType;
    /** @var string $ROOT_DIR */
    public static $ROOT_DIR;
    /** @var \app\Core\Router $router */
    public $router;
    /** @var \app\Core\Request $request */
    public $request;
    /** @var \app\Core\Response $response */
    public $response;
    /** @var Session $session */
    public $session;
    /** @var Application $app */
    public static $app;
    /** @var Controller $controller */
    public $controller = null;
    /** @var Database $db */
    public $db;
    /** @var ?UserModel $user */
    public $user;
    /** @var View $view */
    public $view;

    public function __construct($rootPath)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request , $this->response);
        $this->db = new Database();
        $this->userClass = \app\Models\User::class;
        $this->userType = 'user';
        $this->view = new View();
        $primaryValue = $this->session->get('user');
        if(!$primaryValue) {
            $primaryValue = $this->session->get('student');
            $this->userClass = \app\Models\Student::class;
            $this->userType = 'student';
        }
        if(!$primaryValue){
            $primaryValue = $this->session->get('teacher');
            $this->userClass = \app\Models\Teacher::class;
            $this->userType = 'teacher';
        }
        if($primaryValue){
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        }
        else {
            $this->user = null;
            $this->userType = null;
        }
    }
    public function getController():Controller
    {
        return $this->controller;
    }
    public function setController(Controller $controller):void
    {
        $this->controller = $controller;
    }
    public function run(){
        try {
            echo $this->router->resolve();
        }
        catch (\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error' , ['exception' => $e]);
        }
    }
    public function login(UserModel $user , string $userType){
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set($userType , $primaryValue);
        return true;
    }
    public function logout(){
        $this->user = null;
        $this->userType = '';
        $this->session->remove('user');
        $this->session->remove('student');
        $this->session->remove(('teacher'));
    }
    public function isGuest(){
        if(isset($_SESSION['student']) || isset($_SESSION['user']) || isset($_SESSION['teacher'] ))
            return false;
        return true;
    }
}