<?php
/**
 * WeEngine的pdo事务有bug，重新扩展（只针对事务）
 * 用法参考：
 * 		load()->func('pdoT');
 *		pdoT_begin();
 *		pdoT_execute($sql1);
 *		pdoT_execute($sql2);
 *		$result = pdoT_confirm();
 */
defined('IN_IA') or exit('Access Denied');

/*
 * 创建pdo_mysql链接
 */
function pdoT() {
	global $_W;
	static $objPdo;
	$dbCfgArr = $_W['config']['db'];
	if (!$objPdo) {
		try{
			$objPdo=new pdo("mysql:host=".$dbCfgArr['host'].";dbname=".$dbCfgArr['database'], $dbCfgArr['username'], $dbCfgArr['password']);//最后是关闭自动提交 
	        $objPdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);  //通过设置属性方法进行关闭自动提交
	        $objPdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);  //开启异常处理
	    }catch(PDOException $e){
	        echo "数据库连接失败：".$e->getMessage(); 
	        exit;
	    }
	    $objPdo->exec('set names '.$dbCfgArr['charset']);
	}
    return $objPdo;
}

function PDOTransactionSqlList($newSql=''){
	static $sqlList=array();
	if ($newSql!='') {
		$sqlList[] = $newSql;
	}
	return $sqlList;
}

function pdoT_begin() {
	return pdoT()->beginTransaction();
}


function pdoT_execute($sql){
	return PDOTransactionSqlList($sql);
}

function pdoT_confirm() {
	$sqlList = PDOTransactionSqlList();
	if (!empty($sqlList)) {
		try {
			foreach ($sqlList as $sql) {
				pdoT()->exec($sql);
			}
			pdoT()->commit();
		} catch (PDOException $ex) {
			//PDOExceptionList($ex->getMessage());
			pdoT()->rollBack();
			return false;
		}
	}
	return true;
}
