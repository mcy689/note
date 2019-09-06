<?php

namespace MongoDB;

use \MongoDB\Driver\BulkWrite;
use \MongoDB\Driver\Query;
use \MongoDB\Driver\Command;
use \MongoDB\BSON\ObjectId;
use \MongoDB\BSON\Regex;

class Manager
{
    const TIMEOUT = 3000;//超时时间
    public $_connection = '';
    private $_db = '';
    private $_collection = '';
    private $_namespace = '';

    public function __construct($_db,$_collection) //依赖一个对象，传入构造的连接
    {
        $manager = new \MongoDB\Driver\Manager('mongodb://localhost:27017');
        $this->_connection = $manager;
        $this->_db = $_db;
        $this->_collection = $_collection;
        $this->_namespace = $this->_db.'.'.$this->_collection;
    }

    /**
     * 获取 mongodb 版本
     * @return mixed
     */
    public function getVersion()
    {
        $param = ['buildinfo'=>true];
        try {
            return ($this->command($param))->version;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 单条查询
     * @param $filter
     * @param array $option
     * @return array|bool
     * @throws Driver\Exception\Exception
     */
    public function get($filter, $option=[])
    {
        try{
            $option = array_merge($option,['limit'=>1]);
            $query = new Query($filter,$option);
            $cursor = $this->_connection->executeQuery($this->_namespace, $query);
            $cursor->setTypeMap(array('root'=>'array','document'=>'array','array'=>'array'));
            foreach($cursor as $v){
                return $v;
            }
        } catch (\Exception $e){
            return false;
        }
    }

    /**
     * 多条查询
     * @param $filter
     * @param $option
     * @return array|bool
     * @throws Driver\Exception\Exception
     */
    public function select($filter, $option=[])
    {
        try{
            $query = new Query($filter,$option);
            $cursor = $this->_connection->executeQuery($this->_namespace, $query);
            $cursor->setTypeMap(array('root'=>'array','document'=>'array','array'=>'array'));
            $return = array();$i = 0;
            foreach($cursor as $v){
                $return[$i++] = $v;
            }
            return $return;
        } catch (\Exception $e){
           return false;
        }
    }

    /**
     * 统计数量
     * @param array $filter
     * @return bool
     * @throws Driver\Exception\Exception
     */
    public function count($filter=[])
    {
        $filter = (object)$filter;
        try{
            $command = new Command(['count'=>$this->_collection,'query'=>$filter]);
            $cursor = $this->_connection->executeCommand($this->_db,$command);
            return $cursor->toArray()[0]->n;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 单条写入
     * @param $data
     * @return array
     */
    public function insert($data)
    {
        try {
            $bulk = new BulkWrite();
            $id = $bulk->insert($data);
            $result = $this->_connection->executeBulkWrite($this->_namespace, $bulk);
            return array("ok"=>1,"count"=>$result->getInsertedCount(),"_id" => $id);
        } catch (\Exception $e) {
            return ['ok'=>0,'err_msg'=>$e->getMessage()];
        }
    }

    /**
     * 多条写入
     * @param $data
     * @param bool $isOrder
     *          //顺序写，串行执行。无序写，可并发执行。
     * @return array
     */
    public function insertMany($data,$isOrder=false)
    {
        $isOrder = $isOrder ? true : false ;
        try {
            $bulk = new BulkWrite(['ordered' => $isOrder]);
            foreach ($data as $item){
                $bulk->insert($item);
            }
            $result = $this->_connection->executeBulkWrite($this->_namespace, $bulk);
            return array("ok"=>1,"count"=>$result->getInsertedCount());
        } catch (\Exception $e) {
            return ['ok'=>0,'err_msg'=>$e->getMessage()];
        }
    }

    /**
     * 修改
     * @param $filter
     * @param $data
     *   eg:
     *      multi   false 默认更新一条数据，true 更新多条数据
     *      upsert  如果过滤器与现有文档不匹配，请插入单个文档
     * @return array
     */
    public function update($filter,$data)
    {
        try {
            $bulk = new BulkWrite();
            $bulk->update($filter,$data,['upsert'=>false,'multi'=>false]);
            $result = $this->_connection->executeBulkWrite($this->_namespace, $bulk);
            return array("ok"=>1,"count"=>$result->getModifiedCount());
        } catch (\Exception $e) {
            return ['ok'=>0,'err_msg'=>$e->getMessage()];
        }
    }

    /**
     * 批量修改
     * @param $filter
     * @param $data
     * @return array
     */
    public function updateMany($filter,$data)
    {
        try {
            $bulk = new BulkWrite();
            $bulk->update($filter,$data,['multi'=>true,'upsert'=>false]);
            $result = $this->_connection->executeBulkWrite($this->_namespace, $bulk);
            return array("ok"=>1,"count"=>$result->getModifiedCount());
        } catch (\Exception $e) {
            return ['ok'=>0,'err_msg'=>$e->getMessage()];
        }
    }

    /**
     * 如果查询条件匹配就修改，不匹配就添加
     * @param $filter
     * @param $data
     * @return array
     */
    public function updateInsert($filter,$data)
    {
        try {
            $bulk = new BulkWrite();
            $bulk->update($filter,$data,['multi'=>false,'upsert'=>true]);
            $result = $this->_connection->executeBulkWrite($this->_namespace, $bulk);
            return array("ok"=>1,"count"=>$result->getUpsertedCount(),'_id'=>$result->getUpsertedIds());
        } catch (\Exception $e) {
            return ['ok'=>0,'err_msg'=>$e->getMessage()];
        }
    }

    /**
     * 删除
     * @param $filter
     * @return array
     */
    public function delete($filter)
    {
        try {
            $bulk = new BulkWrite();
            $bulk->delete($filter,['limit'=>false]);//删除所有的
            $result = $this->_connection->executeBulkWrite($this->_namespace, $bulk);
            return array("ok"=>1,"count"=>$result->getDeletedCount());
        } catch (\Exception $e) {
            return ['ok'=>0,'err_msg'=>$e->getMessage()];
        }
    }

    /**
     * 删除数据库
     */
    public function dropDatabase()
    {
        $option = array("dropDatabase" => 1);
        $data = $this->command($option);
        if(is_object($data) && $data->ok){
            return true;
        }
        return false;
    }

    /**
     * 删除集合
     * @return bool
     */
    public function dropCollection()
    {
        $option = array("drop" => $this->_collection);
        $data = $this->command($option);
        if(is_object($data) && $data->ok){
            return true;
        }
        return false;
    }

    /**
     * mongodb 命令行
     * @param $option
     * https://docs.mongodb.com/manual/reference/command
     * @return array|bool
     */
    public function command($option)
    {
        try{
            $command = new Command($option);
            $cursor = $this->_connection->executeCommand($this->_db,$command);
            return $cursor->toArray()[0];
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 部分支持 MongoDB的findAndModify方法
     * @param $filter
     * @param $change
     * @param bool $remove
     * @param bool $new
     * @param bool $upsert
     * https://docs.mongodb.com/manual/reference/method/db.collection.findAndModify/
     * @return array|bool
     */
    public function findAndModify($filter,$change,$remove=false,$new=false,$upsert=false)
    {
        if($remove){
            $param = ['findandmodify'=>$this->_collection,'query'=>$filter,'remove'=>$remove,'maxTimeMS'=>self::TIMEOUT];
            $data = $this->command($param);
        } else {
             $param = [
                "findandmodify" => $this->_collection,
                "query" => $filter,
                "update" => $change,
                "new" => $new,
                "upsert" => $upsert,
                "maxTimeMS" => self::TIMEOUT
             ];
            $data = $this->command($param);
        }
        if(is_object($data) && $data->ok){
            return (array)$data->value;
        }
        return false;
    }

    /**
     * 创建索引
     * @param $keys
     *          eg:
     *              ['field1'=>1]
     *              ['field1'=>1,'field2'=>-1]
     * @param $option
     *          eg:
     *              ['name'=>'f_d']     索引名字
     *              [background=>true]  后台创建索引
     *              ['unique'=> true]   创建唯一索引，以便集合不接受索引键值与索引中现有值匹配的文档的插入或更新。
     * https://docs.mongodb.com/manual/reference/method/db.collection.createIndex
     * @return mixed
     */
    public function createIndex(array $keys,array $option)
    {
        $index['key'] = $keys;
        $option = array_merge($option,$index);
        $param = [
            'createIndexes' =>$this->_collection,
            'indexes' =>[$option],
        ];
        return $this->command($param);
    }

    /**
     * 创建正则
     * @param $pattern
     *      不应使用分隔符字符包装该模式。
     * @param $flags
     *      eg:
     *          regex('^foo','i')
     * @return bool|Regex
     */
    public function regex($pattern,$flags)
    {
        try{
           return new Regex($pattern,$flags);
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * 获取数据库ID
     * @param $_id
     * @return bool|ObjectId
     */
    public static function ObjectId($_id)
    {
        try{
           return new ObjectId($_id);
        } catch (\Exception $e){
            return false;
        }
    }
}