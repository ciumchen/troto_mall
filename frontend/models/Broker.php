<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Broker
{

    /**
     * 列出参与提成的订单，条件：
     *      1、购买人有上级
     *      2、订单已支付
     *      END
     *
     * @return array
     */
    public function getOrderListDel()
    {
        $connection = Yii::$app->db;
        $sql = "SELECT id, uid, status, price FROM ims_shopping_order 
                WHERE uid IN ( SELECT uid FROM ims_users WHERE brokerid <> 0 AND brokerid IS NOT NULL) and status in (1, 2, 3, 4, 5)
                ORDER BY id DESC";
        $command = $connection->createCommand($sql);
        $orders = $command->queryAll();

        return $orders;
    }

    public function getLevelMember($uid)
    {

        $tree = $this->getUnderLine($uid);
        $members = [0 => []];
        foreach ($tree as $node) {
            $members[0][] = $node['uid'];

            if (isset($node['sub'])) {
                $this->getLevelMemberSub($node['sub'], $members);
            }
        }

        return $members;
    }

    public function getUnderLine($uid)
    {
        $underLine = Users::find()->asArray()->select(['uid', 'credits2', 'brokerid'])->where(['brokerid' => $uid])->all();

        foreach ($underLine as &$user) {
            $sub = $this->getUnderLine($user['uid']);

            if (!empty($sub)) {
                $user['sub'] = $sub;
            }
        }

        return $underLine;
    }

    private function getLevelMemberSub($root, &$members, &$level = 1)
    {
        foreach ($root as $node) {
            if (!isset($members[$level])) {
                $members[$level] = [];
            }

            $members[$level][] = $node['uid'];

            if (isset($node['sub'])) {
                $level++;
                $this->getLevelMemberSub($node['sub'], $members, $level);
            }
        }

        if (!isset($root['sub'])) {
            $level--;
        }
    }

    //把递归调用包装一下，上级使用时更友好些。
    public function getRelationList($uid)
    {
        $list = [];
        $this->getRelationListRecursive($uid, $list);

        //遍历上家，踢除掉中间多余的关系链条
        $data = [];
        if (isset($list[0])) { $data[0] = $list[0]; }
        if (isset($list[1])) { $data[1] = $list[1]; }
        if (isset($list[2])) { $data[2] = $list[2]; }
        if (isset($list[3])) { $data[3] = $list[3]; }
        if (count($list)>4) {
            $data[3] = end($list);
        }
        return $data;
    }

    //递归生成关系链
    private function getRelationListRecursive($uid, &$list)
    {
        array_push($list, (int)$uid);

        $user = Users::find()->asArray()->select(['uid', 'credits2', 'brokerid'])->where(['uid' => $uid])->one();

        // 根据不同的情形，返回不同值，便于排错。
        if ($user === false) {
            return false;
        }

        if ($user['brokerid'] === null) {
            return null;
        }

        if ($user['brokerid'] == 0) {
            return 0;
        }

        $this->getRelationListRecursive($user['brokerid'], $list);

        return true;
    }

    public function calReward($users, $reward_total)
    {
        $len = count($users);
        $bill = [];
        $ratio = [0, 0.5, 0.3, 0.2];

        foreach ($users as $key => $uid) {
            $user = Users::find()->asArray()->where(['uid' => $uid])->one();
            
            $reward = $ratio[$key] * $reward_total;
            //判断是否激活
            $real_reward = ($user['credits2'] > 0 ? $reward : 0);
        /*
            $reward = $this->levelRewardRatio($key, $len, $user) * $reward_total;

            //判断是否为代理商
            if ($user['brokerid'] === null) {
                $real_reward = $reward;
            } else {
                //判断是否激活
                $real_reward = ($user['credits2'] > 0 ? $reward : 0);
            }
        */
            $bill[] = [
                'uid' => $uid,
                'reward' => $real_reward
            ];
        }

        return $bill;
    }

    private function levelRewardRatio($level, $len, $user)
    {
        if ($level === 0) return 0; //自己不分

        if ($level === 1) {
            if ($user['brokerid'] === null) {
                return 0.6; //代理商
            } else {
                return 0.4; //分销商
            }
        }

        if ($level === 2) {
            if ($level === ($len - 1)) {
                return 0.45;
            } else {
                return 0.25;
            }
        }

        if ($level === 3) {
            if ($level === ($len - 1)) {
                return 0.35;
            } else {
                return 0.15;
            }
        }

        if ($level === ($len - 1)) return 0.2;

        return 0;  //三层级以上无分成
    }

    public function getTotalReward($uid)
    {
        $sql = "select sum(reward_amount) as total_reward from ims_brokers_order_reward where brokerid = $uid";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $result = $command->queryOne();
        $total = $result['total_reward'];

        return ($total === null ? 0 : $total);
    }
}

//end file