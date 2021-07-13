<?php


namespace app\Core\exception;


class ForbiddenException extends \Exception
{
    protected $message = 'Not allowed';
    protected $code = 403;

}