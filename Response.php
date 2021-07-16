<?php

namespace app\Core;

class Response
{
    public function setStatusCode(int $code){
        http_response_code($code);
    }
    public function redirect(string $url){
        echo $url;
        header('Location: '.$url);
    }
}