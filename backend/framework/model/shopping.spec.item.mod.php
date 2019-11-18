
<?php
/**
 * 产品规格
 * shopping.spec.item.mod.php
 *
 */

defined('IN_IA') or exit('Access Denied');

/**
 * 获取规格值
 * 
 */
function Spec_getSpecItemBySpecId($SpecId = 0){
	return pdo_fetchall("select tba.*,tbb.stock from " . tablename('shopping_spec_item') . " as tba left join ".tablename("shopping_goods_option")." as tbb 
			on tba.id = tbb.specs where  tba.show=1 and tba.specid=:specid order by tba.displayorder asc", array(":specid" => $SpecId));
}