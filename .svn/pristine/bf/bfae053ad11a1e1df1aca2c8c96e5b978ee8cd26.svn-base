<?php
/**
 * memcached客户端
 */
defined('IN_IA') or exit('Access Denied');


function memc() {
	static $mc;
	if(empty($mc)) {
		global $_W;
		$mc = new MemcacheClient($_W['config']['memcache']);
	}
	return $mc;
}


class MemcacheClient {
	public $memClient;
	public $serverList = array();
	private static $randCharArr = array(
			'a','b','c','d','e','f','g','h','i','j','k','l','m',
			'n','o','p','q','r','s','t','u','v','w','x','y','z',
			'0','1','2','3','4','5','6','7','8','9'
		);

	public function __construct($memcachedServer) {
		//$this->memClient = memcache_connect($memcachedServer[0]['host'],$memcachedServer[0]['port']);
		$this->memClient = new Memcached();
		foreach ($memcachedServer as $server) {
			$this->serverList[] = $server['host'].':'.$server['port'];
			$this->memClient->addServer($server['host'], $server['port']);
		}
		return $this->memClient;
	}

	function generateKey(){
		//理论值可以生成key的数量 2,176,782,336
		$key = self::$randCharArr[rand(0,35)].self::$randCharArr[rand(0,35)].self::$randCharArr[rand(0,35)];
		$key .= self::$randCharArr[rand(0,35)].self::$randCharArr[rand(0,35)].self::$randCharArr[rand(0,35)];
		if ($this->get($key)) {
			$key = $this->generateKey();
		}
		return $key;
	}

	public function add($key, $value, $zipLevel=0, $expireTime=0) {
		return $this->memClient->set($key, $value, $zipLevel, $expireTime);
	}
	
	/*
	 * 设置memcahce存储值
	 * @param $value mixed 设置值
	 * @param $zipLevel int 压缩级别（0--1）
	 * @prama $expireTime int 过期时间（默认一天）
	 */
	public function set($value, $zipLevel=0, $expireTime=0) {
		$key = $this->generateKey();
		$setKey = $this->memClient->set($key, $value, $zipLevel, $expireTime);
		if ($setKey) {
			return $key;
		}
		return false;
	}

	public function get($key) {
		return $this->memClient->get($key);
	}
	
	/*
	 * 修改memcahce存储值
	 * @param $key string 键位
	 * @param $value mixed 设置值
	 * @param $zipLevel int 压缩级别（0--1）
	 * @prama $expireTime int 过期时间（默认永不过期）
	 */
	public function update($key, $value, $zipLevel=0, $expireTime=0) {
		$this->memClient->delete($key);
		return $this->memClient->add($key, $value, $zipLevel, $expireTime);
		 $this->memClient->replace($key, $value, $zipLevel, $expireTime);
	}
	
	public function delete($key) {
		return $this->memClient->delete($key);
	}

	/*
	 * 清除所有数据
	 */
	public function flush() {
		return $this->memClient->flush();
	}
	
	/*
	 * 关闭连接
	 */
	public function close() {
		return $this->memClient->close();
	}
	
	
	public function status() {
		return $this->memClient->getStats();
	}
	
	public function serverList() {
		return $this->serverList;
	}
	
	public function getVersion() {
		return $this->memClient->getVersion();
	}
	
}