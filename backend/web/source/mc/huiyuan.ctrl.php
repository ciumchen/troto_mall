<?php
defined('IN_IA') or exit('Access Denied');

load()->model('mc');
$dos = array('display', 'view', 'initsync', 'updategroup', 'updateParentBroker', 'sms', 'cancelParent', 'addparent');
$do = in_array($do, $dos) ? $do : 'display';

if ($do == 'display') {
    global $_W,$_GPC;
    $pars = array();

    load()->func('tpl');
    $_W['page']['title'] = '会员运营- 微信粉丝 - 粉丝营销';

    $pindex   = max(1, intval($_GPC['page']));
    $psize    = max(30, intval($_GPC['psize']));
    $gender   = $_GPC['gender'];
    $saleType = intval($_GPC['saleType']);

    $time = $_GPC['time'];
    if (isset($time['start'])){
        $starttime = $ret['param']['starttime'] = strtotime($time['start']);
    } else {
        $starttime = $ret['param']['starttime'] = strtotime('-6 month');
    }
    if (isset($time['end'])){
        $endtime = $ret['param']['endtime'] = strtotime($time['end'].' 23:59:59');
    } else {
        $endtime = $ret['param']['endtime'] = strtotime(date('Y-m-d 23:59:59', time()));
    }

    $condition = ' WHERE b.joindate  BETWEEN '.$starttime.' AND '.$endtime;

    if (!empty($gender)) {
        $condition .= " and b.gender = $gender ";
        $pars[':gender'] = $gender;
    }

    $nickname = trim($_GPC['nickname']);
    if (!empty($nickname)) {
        $condition .= " AND b.nickname LIKE '%{$nickname}%' ";
    }
    $uid = trim($_GPC['uid']);
    if (!empty($uid)) {
        $condition .= " and b.uid = $uid ";
        $pars[':uid'] = $uid;
    }
    $master  = array();
    $level   = intval($_GPC['level']);
    $fansuid = intval($_GPC['fansuid']);
    $bool = true;
    if (!empty($fansuid)) {
        $master = pdo_fetch('SELECT * FROM '.tablename('members').' WHERE uid = :uid', array(':uid' => $fansuid));
        $con = ' and a.uid = b.uid';

        if ($_GPC['pay'] == 'yes') {
            $con .= ' AND b.`status`>0';
            $type = 'yes';
        }
        if ($_GPC['pay'] == 'no') {
            $con .= ' AND b.`status`=0';
            $type = 'no';
        }

        //取得第一级
        if ($level==1) {
            $down = array($fansuid);
        }
        //取得第二级
        if ($level==2) {
            $down = array();
            $condition2 = ' AND b.brokerid in ('.implode(',', array($fansuid)).')';
            $list2 = pdo_fetchall("SELECT b.uid FROM " .tablename('members') . ' b '.$condition.$condition2 , $pars
            );
            foreach ($list2 as $memberOne) {
                $down[] = $memberOne['uid'];
            }
            // var_dump($down);exit();
        }
        //取得第三级
        if ($level==3) {
            $down = array();
            $condition2 = ' AND b.brokerid in ('.implode(',', array($fansuid)).')';
            $list2 = pdo_fetchall("SELECT b.uid FROM " .tablename('members') . ' b '.$condition.$condition2 , $pars
            );
            foreach ($list2 as $memberOne) {
                $down[] = $memberOne['uid'];
            }
            if (!empty($down)) {
                $condition3 = ' AND b.brokerid in ('.implode(',', $down).')';
                $list2 = pdo_fetchall("SELECT b.uid FROM " .tablename('members') . ' b '.$condition.$condition3 , $pars
                );
                foreach ($list2 as $memberOne) {
                    $down[] = $memberOne['uid'];
                }
            }
        }

        $down = implode(',', $down);
        if (!empty($down)) {
            $condition .= ' AND b.brokerid in (' . $down . ')';
        } else {
            $bool = false;
        }
    }

    if ($bool) {
        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('members') . ' b ' . $condition, $pars);

        $list = pdo_fetchall(
            "SELECT b.uid, b.nickname, b.brokerid, b.avatar, b.gender, b.joindate,b.lastvisit,b.follow,b.followtime,b.unfollowtime FROM " . tablename('members') . ' b '
            . $condition . " ORDER BY b.`joindate` DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $pars
        );

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                if (!empty($v['tag']) && is_string($v['tag'])) {
                    if (is_base64($v['tag'])) {
                        $list[$k]['tag'] = base64_decode($v['tag']);
                    }
                    if (is_serialized($list[$k]['tag'])) {
                        $list[$k]['tag'] = @iunserializer($list[$k]['tag']);
                    }
                    if (empty($list[$k]['avatar'])) {
                        $list[$k]['tag']['avatar'] = tomedia($list[$k]['tag']['headimgurl']);
                        unset($list[$k]['tag']['headimgurl']);
                    }
                } else if (empty($v['tag'])) {
                    $list[$k]['tag'] = array();
                }
                $list[$k]['user'] = ($v['nickname']) ? $v['nickname'] : $v['tag']['nickname'];
                $list[$k]['pid'] = mc_getNickname($v['pid']);
                $list[$k]['account'] = $accounts[$v['acid']]['name'];
            }
        }

        $pager = pagination($total, $pindex, $psize);
    }
}

if ($do == 'cancelParent') {
    //删除上家
    $downid = intval($_GPC['uid']);

    pdo_update('mc_members', array('pid' => 0), array('uid' => $downid));

    pdo_update('mc_relation', array('uid1' => '0', 'uid2' => '0', 'uid3' => '0'), array('uid' => $downid));    #我的所有上家
    pdo_update('mc_relation', array('uid2' => '0', 'uid3' => '0'), array('uid1' => $downid));    #我的两个上家
    pdo_update('mc_relation', array('uid3' => '0'), array('uid2' => $downid));                    #我的顶级上家

    message('修改成功', referer(), 'success');
}

if ($do == 'updateParentBroker') {
    $uid = intval($_GPC['uid']);
    $brokerid = $_GPC['brokerid'];
    pdo_query("update " . tablename('members') . " set brokerid = $brokerid where uid= $uid");
    message('修改成功', referer(), 'success');
    exit;
}

if ($do == 'addparent') {
    $uid = intval($_GPC['userid']);
    $pid = intval($_GPC['parentid']);
    if ($pid == 0) {
        message('上家不能为空！', referer(), 'error');
    }

    # 查看该用户是否有上家
    # 没有上家直接添加
    load()->model('mc.relation');

    # 关系链处理
    if (relation_handle($uid, $pid)) {
        # 生成关系链，调用余额日志并添加，添加领取
        $res = pdo_update('mc_members', array('pid' => $pid), array('uid' => $uid));

        message('操作成功！' . $res, referer(), 'success');
    } else {

        message('无法添加上家！', referer(), 'error');
    }
}

template('mc/huiyuan');