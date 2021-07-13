<?php


namespace app\Core;
use app\Core\db\DBModel;


abstract class UserModel extends DBModel
{
    abstract public function getDisplayName():string;
}