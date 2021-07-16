<?php

namespace app\Core\middlewares;

abstract class BaseMiddleware
{
    abstract public function execute();
}