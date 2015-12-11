<?php
/**
 * SS-Panel
 * A simple Shadowsocks management system
 * Author: Sendya <18x@loacg.com>
 */
namespace Controller;


use Helper\Listener;
use Helper\Util;
use Model\User;

class Form extends Listener
{
    const PLAN_A = 'A', PLAN_B = 'B', PLAN_C = 'C', PLAN_D = 'D', PLAN_VIP = 'VIP';

    public function ChangeNickname()
    {
        global $user;
        $result = array('error' => 1, 'message' => '�޸�ʧ��');
        $nickname = trim($_POST['nickname']);

        if ('' != $nickname) {
            $user = User::GetUserByUserId($user->uid);
            $user->nickname = $nickname;
            $user->updateUser();
            $result = array('error' => 0, 'message' => '�޸ĳɹ�');
        }

        echo json_encode($result);
        exit();
    }

    public function ChangeSSPwd()
    {
        global $user;
        $result = array('error' => 1, 'message' => '�޸�ʧ��');
        $sspwd = trim(($_GET['sspwd']));
        if ('' == $sspwd || $sspwd == null)
            $sspwd = Util::GetRandomPwd();

        $user = User::GetUserByUserId($user->uid);
        $user->sspwd = $sspwd;
        $user->updateUser();
        $result = array('error' => 1, 'message' => '�޸�SS��������ɹ�');

        echo json_encode($result);
        exit();
    }

    public function UpdatePlan()
    {
        global $user;
        $result = array('error' => 1, 'message' => '�����˻�����ʧ��.');

        switch($user->plan)
        {
            case self::PLAN_A:

                if($user->money >= 15) {
                    $user->money = $user->money-15;//�۳�15 ������B�ײ�
                    $user->plan = self::PLAN_B;
                    $user->transfer = Util::GetGB() * 100;
                    $user->updateUser();
                    $result['error'] = 0;
                    $result['message'] = '�����ɹ������ĵ�ǰ�ȼ�Ϊ';
                } else {
                    $result['message'] = '����ʧ�ܣ���������';
                }
                break;
            case self::PLAN_B:
                if($user->money >= 25) {
                    $user->money = $user->money-25;//�۳�15 ������B�ײ�
                    $user->plan = self::PLAN_C;
                    $user->transfer = Util::GetGB() * 200;
                    $user->updateUser();
                    $result['error'] = 0;
                    $result['message'] = '�����ɹ������ĵ�ǰ�ȼ�Ϊ';
                } else {
                    $result['message'] = '����ʧ�ܣ���������';
                }
                break;
            case self::PLAN_C:
                if($user->money >= 40) {
                    $user->money = $user->money-40;//�۳�15 ������B�ײ�
                    $user->plan = self::PLAN_D;
                    $user->transfer = Util::GetGB() * 500;
                    $user->updateUser();
                    $result['error'] = 0;
                    $result['message'] = '�����ɹ������ĵ�ǰ�ȼ�Ϊ';
                } else {
                    $result['message'] = '����ʧ�ܣ���������';
                }
                break;
            case self::PLAN_VIP:
                $result['error'] = 0;
                $result['message'] = '�Բۣ��㲻����ƾʲô����VIP';
                break;
            default:
                $result['message'] = '��֪�����������������������.����ʧ��';
                break;
        }

        echo json_encode($result);
        exit();
    }

    public function CheckIn()
    {
        global $user;
        $result = array('error' => 1, 'message' => '');
        if($user->lastCheckinTime >= 3600*24) //һ��
        {
            $checkinTransfer = rand(5, 25) * Util::GetMB();
            $user->lastCheckinTime = time();
            $user->transfer = $user->transfer + $checkinTransfer;
            $result['message'] = 'ǩ���ɹ�, ���'.$checkinTransfer.'MB ����';
        } else {
            $result['message'] = '���Ѿ��� ' . date('Y-m-d H:i:s') . " ʱǩ����.";
        }
        echo json_encode($result);
        exit();
    }

}