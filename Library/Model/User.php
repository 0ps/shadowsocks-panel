<?php
/**
 * KK Forum
 * A simple bulletin board system
 * Author: kookxiang <r18@ikk.me>
 */
namespace Model;

use Core\Database;

class User
{
    const ENCRYPT_TYPE_DEFAULT = 0;
    const ENCRYPT_TYPE_ENHANCE = 1;

    public $uid; //user id , pk
    public $email;//�ʼ�,���ڵ�½  pk
    public $nickname;//�ǳ�
    private $password;//����
    public $flow_up;//�ϴ�����
    public $flow_down;//��������
    public $transfer;//������
    public $plan;//�˻�����
    public $enable;//�˻��Ƿ�����SS  0������  1����
    public $invite;//ע��invite,Ϊ�����������.
    public $regDateLine;//ע��ʱ��

    /**
     * Get a user by email
     * @param $email string Email address
     * @return User
     */
    public static function GetUserByEmail($email)
    {
        $statement = Database::prepare("SELECT * FROM member WHERE email=?");
        $statement->bindValue(1, $email);
        $statement->execute();
        $statement->setFetchMode(\PDO::FETCH_CLASS, '\\Model\\User');
        return $statement->fetch(\PDO::FETCH_CLASS);
    }

    /**
     * Get a user by UserId
     * @param $userId int UserID
     * @return User
     */
    public static function GetUserByUserId($userId)
    {
        $statement = Database::prepare("SELECT * FROM member WHERE uid=?");
        $statement->bindValue(1, $userId, \PDO::PARAM_INT);
        $statement->execute();
        $statement->setFetchMode(\PDO::FETCH_CLASS, '\\Model\\User');
        return $statement->fetch(\PDO::FETCH_CLASS);
    }

    /**
     * Insert current user into database
     * @return int Auto-generated UserID for this user
     */
    public function insertToDB()
    {
        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }
        $statement = Database::prepare("INSERT INTO member SET email=:email, `password`=:pwd, nickname=:nickname,
            `flow_up`=:flow_up, `flow_down`=:flow_down, transfer=:transfer, plan=:plan, `enable`=:enable, invite=:invite, regDateLine=:regDateLine");
        $statement->bindValue(':email', $this->email, \PDO::PARAM_STR);
        $statement->bindValue(':pwd', $this->password, \PDO::PARAM_STR);
        $statement->bindValue(':nickname', $this->nickname, \PDO::PARAM_STR);
        $statement->bindValue(':flow_up', $this->flow_up, \PDO::PARAM_INT);
        $statement->bindValue(':flow_down', $this->flow_down, \PDO::PARAM_INT);
        $statement->bindValue(':transfer', $this->transfer, \PDO::PARAM_INT);
        $statement->bindValue(':plan', $this->plan, \PDO::PARAM_STR);
        $statement->bindValue(':enable', $this->enable, \PDO::PARAM_INT);
        $statement->bindValue(':invite', $this->invite, \PDO::PARAM_INT);
        $statement->bindValue(':regDateLine', $this->regDateLine, \PDO::PARAM_INT);

        $statement->execute();
        $this->uid = Database::lastInsertId();
        if (!$inTransaction) {
            Database::commit();
        }
        return $this->uid;
    }

    /**
     * Verify whether the given password is correct
     * @param string $password Password needs to verify
     * @return bool Whether the password is correct
     */
    public function verifyPassword($password)
    {
        list($hashedPassword, $encryptType) = explode('T', $this->password);
        if ($encryptType == self::ENCRYPT_TYPE_DEFAULT) {
            return $hashedPassword == md5(ENCRYPT_KEY . md5($password) . ENCRYPT_KEY);
        } elseif ($encryptType == self::ENCRYPT_TYPE_ENHANCE) {
            $salt = substr(md5($this->uid . $this->email . ENCRYPT_KEY), 8, 16);
            return $hashedPassword == substr(md5(md5($password) . $salt), 0, 30);
        }
        return false;
    }

    /**
     * Save new password
     * @param string $password New password
     */
    public function savePassword($password)
    {
        $salt = substr(md5($this->uid . $this->email . ENCRYPT_KEY), 8, 16);
        $this->password = substr(md5(md5($password) . $salt), 0, 30) . 'T' . self::ENCRYPT_TYPE_ENHANCE;
        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }
        $statement = Database::prepare("UPDATE member SET `password`=:pwd WHERE uid=:userId");
        $statement->bindValue(':pwd', $this->password, \PDO::PARAM_STR);
        $statement->bindValue(':userId', $this->uid, \PDO::PARAM_INT);
        $statement->execute();
        if (!$inTransaction) {
            Database::commit();
        }
    }

    /**
     * update User info
     *
     */
    public function updateUser() {

        $statement = null;
        $statement = Database::prepare("UPDATE member SET email=:email, `password`=:pwd, nickname=:nickname,
            `flow_up`=:flow_up, `flow_down`=:flow_down, transfer=:transfer, plan=:plan, `enable`=:enable, invite=:invite, regDateLine=:regDateLine WHERE uid=:userId");
        $statement->bindValue(':email', $this->email, \PDO::PARAM_STR);
        $statement->bindValue(':pwd', $this->password, \PDO::PARAM_STR);
        $statement->bindValue(':nickname', $this->nickname, \PDO::PARAM_STR);
        $statement->bindValue(':flow_up', $this->flow_up, \PDO::PARAM_INT);
        $statement->bindValue(':flow_down', $this->flow_down, \PDO::PARAM_INT);
        $statement->bindValue(':transfer', $this->transfer, \PDO::PARAM_INT);
        $statement->bindValue(':plan', $this->plan, \PDO::PARAM_STR);
        $statement->bindValue(':enable', $this->enable, \PDO::PARAM_INT);
        $statement->bindValue(':invite', $this->invite, \PDO::PARAM_INT);
        $statement->bindValue(':regDateLine', $this->regDateLine, \PDO::PARAM_INT);
        $statement->bindValue(':userId', $this->uid, \PDO::PARAM_INT);
        $statement->execute();
        Database::commit();
    }


    /**
     * Get password
     */
    public function getPassword() {
        return $this->password;
    }



}
