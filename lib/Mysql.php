<?php
/**
 * 用于数据库操作的类
 * @author Heister
 * @version 1.0
 * qq 250661062
 */
class Mysql
{
	private $host;
	private $root;
	private $password;
	private $dbname;
	/**
	 * 资源
	 * @var resource
	 */
	public $link;

	/**
	 * 构造函数
	 * @param array $config
	 */
	function __construct($config)
	{
		$this->host   = $config['host'];
		$this->root   = $config['root'];
		$this->pass   = $config['password'];
		$this->dbname = $config['dbname'];
		
		$this->connect();
	}
	
	private function connect()
	{
		$this -> link =
			@mysql_connect($this->host,$this->root,$this->pass) 
			or die( exit("<h2>数据库连接失败：请检查配置文件</h2>".mysql_error() ));
		
		mysql_select_db($this->dbname,$this->link) or die(exit("<h2>数据库不存在</h2>"));
		
		mysql_query("set names 'UTF8'");
	}

	/**
	 * 执行命令
	 * @param string $sql
	 * @return mix 
	 */
	public function query($sql="")
	{
		return mysql_query( $sql , $this->link );
	}
	
	function __destruct(){
		mysql_close($this->link);
	}
}
