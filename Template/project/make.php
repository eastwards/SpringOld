<?
define('WebDir', '.');						//定义项目路径
require('../Spring/Spring.php');		    //载入框架入口文件
require(LibDir.'/Util/Tool/MakeCode.php');	//载入代码生成工具
Spring::init();

//指定数据库名、表前缀
$configs = array(
	array(
		'name'	  => 'demo',
		'prefix'  => 't_',
		'contain' => '*',		//*为所有数据表
		),
	array(
		'name'	  => 'training',
		'prefix'  => 'tt_',
		'contain' => array('tt_ft'),  //array为指定某些表
		),
	);

//指定数据库配置文件存放路径
MakeCode::$configFileDir = WebDir.'/Config/Db';
MakeCode::create($configs);
?>