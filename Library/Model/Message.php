<?php
/**
 * SS-Panel
 * A simple Shadowsocks management system
 * Author: Sendya <18x@loacg.com>
 */
namespace Model;

use Core\Database;

class Message
{
    public $id;
    public $content; //����
    public $pushTime; //����ʱ��
    public $addTime; //���ʱ��
    public $pushUsers; //�����û� Ϊ�ջ�-1ֵ���͸������û� ָ���û���ʹ��json��ʽ,����{1,3,4,5,6,7,10,11}
    public $type; //��Ϣ���ͣ� 0 ������Ϣ(����һ�κ󽫲����ٴ���ʾ) 1 �ظ�������Ϣ
    public $pushEndTime; //��������ʱ��
    public $order;

    /**
     * Get message by Id
     * @param $msgId
     * @return message
     */
    public static function GetNodeById($msgId) {
        $statement = Database::prepare("SELECT * FROM message WHERE id=?");
        $statement->bindValue(1, $msgId);
        $statement->execute();
        $statement->setFetchMode(\PDO::FETCH_CLASS, '\\Model\\Message');
        return $statement->fetch(\PDO::FETCH_CLASS);
    }

    /**
     * Get push message array
     * @param $pushEndTime
     * @return message[]
     */
    public static function GetPushMsg($pushEndTime = 0) {
        if($pushEndTime == 0) $pushEndTime = time();
        $statement = Database::prepare("SELECT * FROM message WHERE pushEndTime=?");
        $statement->bindValue(1, $pushEndTime);
        $statement->execute();
        $list = $statement->fetchAll(\PDO::FETCH_CLASS, '\\Model\\Message');
        return $list;
    }

    /**
     * Delete push time out message
     */
    public static function DeleteOutTimeMsg() {
        $inTransaction = Database::inTransaction();
        if(!$inTransaction) Database::beginTransaction();
        $statement = Database::prepare("DELETE * FROM node WHERE pushEndTime=".time());
        $statement->execute();
        if(!$inTransaction) Database::commit();
    }

    /**
     * Add message
     * @return string
     */
    public function insertToDB() {
        $statement = null;

        $inTransaction = Database::inTransaction();
        if(!$inTransaction) Database::beginTransaction();
        $statement = Database::prepare("INSERT INTO message SET `content`=:content, `pushTime`=:pushTime,
			`addTime`=:addTime, `pushUsers`=:pushUsers, `type`=:type, `pushEndTime`:=pushEndTime, `order`=:order");
        $statement->bindValue(':content', $this->name, \PDO::PARAM_STR);
        $statement->bindValue(':pushTime', $this->type, \PDO::PARAM_INT);
        $statement->bindValue(':addTime', $this->server, \PDO::PARAM_INT);
        $statement->bindValue(':pushUsers', $this->method, \PDO::PARAM_STR);
        $statement->bindValue(':type', $this->info, \PDO::PARAM_INT);
        $statement->bindValue(':pushEndTime', $this->status, \PDO::PARAM_INT);
        $statement->bindValue(':order', $this->order, \PDO::PARAM_INT);
        $statement->execute();
        $this->id = Database::lastInsertId();
        if(!$inTransaction) Database::commit();

        return $this->id;
    }

    /**
     * Update message
     */
    public function update() {
        $inTransaction = Database::inTransaction();
        if(!$inTransaction) Database::beginTransaction();
        $statement = Database::prepare("UPDATE message SET `content`=:content, `pushTime`=:pushTime,
			`addTime`=:addTime, `pushUsers`=:pushUsers, `type`=:type, `pushEndTime`:=pushEndTime,
			 `order`=:order WHERE id=:id");
        $statement->bindValue(':content', $this->name, \PDO::PARAM_STR);
        $statement->bindValue(':pushTime', $this->type, \PDO::PARAM_INT);
        $statement->bindValue(':addTime', $this->server, \PDO::PARAM_INT);
        $statement->bindValue(':pushUsers', $this->method, \PDO::PARAM_STR);
        $statement->bindValue(':type', $this->info, \PDO::PARAM_INT);
        $statement->bindValue(':pushEndTime', $this->status, \PDO::PARAM_INT);
        $statement->bindValue(':order', $this->order, \PDO::PARAM_INT);
        $statement->bindValue(':id', $this->order, \PDO::PARAM_INT);
        $statement->execute();
        if(!$inTransaction) Database::commit();
    }

}