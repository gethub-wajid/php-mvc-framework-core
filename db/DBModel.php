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
            if(Application::$app->userType === 'teacher' && $attribute ==='TeacherId'){
                $teacherId = $_SESSION['teacher'];
                $statement->bindValue(":TeacherId" , $teacherId);
            }
            else{
                $statement->bindValue(":$attribute" , $this->{$attribute});
            }
        }
        $statement->execute();
        return true;
    }
    public function findStudentCourse($courseID , $stuID){
        $sql = "SELECT * FROM studentcourses WHERE CourseId = $courseID  AND StudentId = $stuID";
        $statement = $this->prepare($sql);
        $statement->execute();
        return $statement->fetchObject(static::class);
    }
    public function findClash($stuId , $newStartTime , $newEndTime){
        $sql = "SELECT * FROM studentcourses m, teachercourses n WHERE m.CourseId = n.id 
                AND m.StudentId = $stuId AND (('$newStartTime' >= n.StartTime AND 
                '$newStartTime' <= n.EndTime) OR ('$newEndTime' >= n.StartTime AND '$newEndTime' <= n.EndTime) 
                OR (n.StartTime >= '$newStartTime' AND n.StartTime <= '$newEndTime') OR 
                (n.EndTime >= '$newStartTime' AND n.EndTime <= '$newEndTime'))";
        $statement = $this->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }
    public function joinCourse($courseID , $crsName , $stuID){
        $sql = "INSERT INTO studentcourses (CourseId , CourseName , StudentId ) VALUES ($courseID , '$crsName' , $stuID)";
        $statement = $this->prepare($sql);
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
        $sql = implode(" AND ",  $params);
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item){
            $statement->bindValue(":$key" , $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }
    public function getAllObjects($table){
        $sql = "SELECT * FROM $table;";
        $statement = self::prepare($sql);
        $statement->execute();
        return  $statement->fetchAll();
    }
    public function prepare($sql){
        return Application::$app->db->pdo->prepare($sql);
    }

}