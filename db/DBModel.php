<?php


namespace app\Core\db;
use app\Core\Application;
use app\Core\Model;

abstract class DBModel extends Model
{
    abstract public function tableName(): string;
    abstract public function attributes(): array;
    abstract public function primaryKey(): string;
    public function save(){
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params=[];
        $index = 0;
        foreach ($attributes as $attribute){
            $params[$index] = ":$attribute";
            $index++;
        }
        $query = "INSERT INTO $tableName (".implode(',' , $attributes).
            ") VALUES (".implode(',' , $params).")";
        $statement = $this->prepare($query);
        foreach ($attributes as $attribute){
            $statement->bindValue(":$attribute" , $this->{$attribute});
        }
        $statement->execute();
        return true;
    }
    public function findOne($where){
        $tableName = static ::tableName();
        $attributes = array_keys($where);
        $params=[];
        $index = 0;
        foreach ($attributes as $attribute){
            $params[$index] = "$attribute = :$attribute";
            $index++;
        }
        $sql = implode("AND ",  $params);
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item){
            $statement->bindValue(":$key" , $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }
    public function prepare($sql){
        return Application::$app->db->pdo->prepare($sql);
    }

}