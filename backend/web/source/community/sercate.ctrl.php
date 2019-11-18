<?php
/**
 * sercate.ctrl.php
 * 社区服务类型
 *
 */
defined('IN_IA') or exit('Access Denied');

$dos = array('display','add','delete','subclass');
$do = in_array($do, $dos) ? $do : 'display';
load()->model("community.service.sercate");
if($do == 'display'){	
	$cateInfo = sercate_getCateToInfoMation();
	$children = array();
	foreach ($cateInfo as $Index => $row) {
		if(!empty($row['parentid'])){
			$children[$row['parentid']][] = $row;
			unset($cateInfo[$Index]);
		}
	}
}else if($do == 'add'){
	$cateid = intval($_GPC['cateid']);
	if(!empty($cateid)){
		$title = "编辑分类";
		$ret = sercate_getAloneCateToInfo($cateid);
	}
	if($_W['ispost']){
		$data = array();
		if(isset($_GPC['addsubmit'])){
			if(!empty($_GPC['catename'])){
				$cateName = trim(htmlspecialchars(str_replace(" ","",$_GPC['catename'])));
				$data['title'] = $cateName;
				if(isset($_GPC['label']) && !empty($_GPC['label'])){
					$data['label'] = str_replace(" ","",$_GPC['label']);
				}
				$status = isset($_GPC['status']) ? intval($_GPC['status']) : false;
				$indexshow = isset($_GPC['indexshow']) ? intval($_GPC['indexshow']) : false;
				if($status === false && $indexshow === false){
					message("启动状态出错！","","error");
				}
				$data['status'] = $status;
				$data['indexshow'] = $indexshow;
				$res = pdo_insert("community_service_category",$data);
				($res > 0) ? message("添加分类已成功！", url('community/sercate/display'),"success") : message("添加分类失败！","","error");
			} else {
				message("分类标题不能为空！","","error");
			}
		} else if(isset($_GPC['editorsubmit'])){
			if(!empty($_GPC['catename'])){
				$cateName = trim(htmlspecialchars(str_replace(" ","",$_GPC['catename'])));
				$data['title'] = $cateName;
				if(isset($_GPC['label']) && !empty($_GPC['label'])){
					$data['label'] = str_replace(" ","",$_GPC['label']);
				}
				$status = isset($_GPC['status']) ? intval($_GPC['status']) : false;
				$indexshow = isset($_GPC['indexshow']) ? intval($_GPC['indexshow']) : false;
				if($status === false && $indexshow === false){
					message("启动状态出错！","","error");
				}
				$data['status'] = $status;
				$data['indexshow'] = $indexshow;
				$res = pdo_update("community_service_category",$data,array('sercateid'=>$cateid));
				($res > 0) ? message("编辑分类已成功！", url('community/sercate/display'),"success") : message("编辑分类失败！","","error");
			} else {
				message("分类标题不能为空！","","error");
			}
		}
	}
}else if($do == 'delete'){
	if(!empty($_GPC['cateid'])){
		$cateid = intval($_GPC['cateid']);
	}

	$res = pdo_delete("community_service_category",array("sercateid" => $cateid));
	if($res > 0){
		message("删除成功！",url('community/sercate/display'),"success");
	} else {
		message("删除失败！","","error");
	}
}else if($do == 'subclass'){
	$catedid = intval($_GPC['catedid']);
	$res = sercate_getAloneCateToInfo($catedid);
	if($_W['ispost']){
		$data = array();
		$classTitle = $_GPC['title'];
		$data['title'] = $classTitle;
		$data['label'] = $_GPC['label'];
		$data['parentid'] = $catedid;
		$data['status'] = intval($_GPC['status']);
		$data['indexshow'] = intval($_GPC['indexshow']);
		pdo_insert("community_service_category",$data);
		$res = pdo_insertid();
		if($res > 0){
			message("添加成功！",url('community/sercate'),"success");
		}
		message("添加失败！",referer(),"error");
	}
}

template('community/sercate');