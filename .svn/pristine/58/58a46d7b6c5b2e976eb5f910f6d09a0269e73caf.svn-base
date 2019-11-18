<?php
defined('IN_IA') or exit('Access Denied');
/**
 *
 * 地区业务处理
 */
class Regions extends WeModule
{
    function getRegions()
    {
        global $_W, $_GPC;
        $parentid = isset($_GPC['parentid']) ?$_GPC['parentid']: 0;
        $parentid = intval($parentid);

        $sql = "SELECT * FROM ims_regions WHERE parentid=:parentid";
        $rs = pdo_fetchall($sql, array(':parentid'=>$parentid));
        return $rs;
    }

}