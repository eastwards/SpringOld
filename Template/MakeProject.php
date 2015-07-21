<?
//生成项目模板
define('LibDir', 'Spring');								 //定义Spring框架路径
require(LibDir.'/Util/Tool/ProjectTemplate.php');	     //载入模板生成工具
$name = isset($_GET['name']) ? $_GET['name'] : 'System'; //指定项目名称						
ProjectTemplate::create($name);						     //开始生成
?>