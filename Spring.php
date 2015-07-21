<?
/**
 +------------------------------------------------------------------------------
 * Spring框架入口
 +------------------------------------------------------------------------------
 * @mobile	13183857698
 * @qq		78252859
 * @author  void <lkf5_303@163.com>
 * @version 3.0
 +------------------------------------------------------------------------------
 */

//Spring框架目录
defined('LibDir')      or define('LibDir', dirname(__FILE__));

//当前应用程序运行的根路径
defined('Root')        or define('Root', '/');

//网站目录
defined('WebDir')      or define('WebDir', '.');

//资源目录
defined('ResourceDir') or define('ResourceDir', WebDir.'/Resource');

//应用代码存放路径
defined('AppDir')      or define('AppDir', WebDir.'/App');

//业务组件(业务组件存放路径)
defined('ModuleDir')    or define('ModuleDir', AppDir.'/Module'); 

//业务逻辑(模型组件存放路径)
defined('ModelDir')    or define('ModelDir', AppDir.'/Model'); 

//视图层存放路径
defined('ViewDir')     or define('ViewDir', AppDir.'/View');    

//控制器存放路径
defined('ActionDir')   or define('ActionDir', AppDir.'/Action');

//表单组件存放路径
defined('FormDir')     or define('FormDir', AppDir.'/Form');

//实体层存放目录
defined('EntityDir')   or define('EntityDir', AppDir.'/Entity');	

//api代理存放路径
defined('BiDir')       or define('BiDir', AppDir.'/Bi');

//公用组件库路径
defined('UtilDir')	   or define('UtilDir', AppDir.'/Util');

//定义项目动态资源路径
defined('DataDir')     or define('DataDir', WebDir.'Data');

//定义项目静态资源路径
defined('StaticDir')   or define('StaticDir', Root.'Static/');

//指定默认控制器 
defined('DefaultMod')  or define('DefaultMod', 'Index');

//指定发生404时的控制器 
defined('ErrorMod')    or define('ErrorMod', 'Error');

//配置文件存放目录
defined('ConfigDir')   or define('ConfigDir', WebDir.'/Config');

//日志存放目录
defined('LogDir')	   or define('LogDir', ResourceDir.'/Log');			

//缓存组件配置信息目录
defined('CacheDir')	   or define('CacheDir', ResourceDir.'/Cache');

//是否缓存对象资源
defined('IsCached')	   or define('IsCached', false);

//指定编码
header("Content-type: text/html; charset=utf-8");

//设置时间区域
ini_set("date.timezone", "Asia/Shanghai");

class Spring
{
	/**
	 * 是否开启警告
	 */
	public static $isNotice  = true;

	/**
	 * 运行模式(1为web、2为控制台)
	 */
	public static $mode      = 1;

	/**
	 * 类地图(键为类名、值为类文件路径)
	 */
	private static $classMap = array();
	
	
	/**
	 * Spring框架入口
	 *
	 * @access	public
	 * @param	string	$mode	运行模式(1为web、2为控制台)
	 * @return	void
	 */
	public static function run($mode = 1)
	{
		self::init();
		self::$mode = $mode;
		$appName    = $mode == 1 ? 'WebApplication' : 'ConsoleApplication';
		$app = new $appName();
		$app->process();
		$app = null;
		ServiceFactory::dispose();
	}

	/**
	 * 框架初始化
	 *
	 * @access	private
	 * @return	void
	 */
	private static function init()
	{
		set_error_handler(array("Spring", "appError"));
		set_exception_handler(array("Spring", "appException"));
		ServiceFactory::$cacheDir   = CacheDir;
		ServiceFactory::$isCached   = IsCached;
		ServiceFactory::$configFile = LibDir.'/Config/map.config.php';
		Orm::$entityDir             = EntityDir;
		
		//载入应用所需类库
		if ( file_exists(UtilDir.'/import.php') )
		{
			require(UtilDir.'/import.php');
		}
	}
	
	/**
	 * 获取组件
	 *
	 * @access	public
	 * @param	string	$name	组件标签
	 * @return	object
	 */
	public static function getComponent($name)
	{
		return ServiceFactory::getObject($name);
	}

	/**
	 * 类文件自动加载
	 *
	 * @access	public
	 * @param	string	$className	类名(区分大小写)
	 * @return	bool
	 */
	public static function loader($className)
	{
		if ( empty(self::$classMap) )
		{
			$map = array();
			require(LibDir.'/Config/classmap.config.php');
			if ( file_exists(ConfigDir.'/Extension/classmap.config.php') )
			{
				require(ConfigDir.'/Extension/classmap.config.php');
				$map = isset($map) && is_array($map) ? $map : array();
			}

			self::$classMap = array_merge($classMap, $map);
		}
		
		if ( isset(self::$classMap[$className]) )
		{
			require(self::$classMap[$className]);
		}
		
		return class_exists($className, false) || interface_exists($className, false);
	}

	/**
	 * 记录跟踪信息
	 *
	 * @access	public
	 * @param	string	$msg		消息
	 * @param	string	$category	消息分类
	 * @return	void
	 */
	public static function trace($msg, $category)
	{
	}

	/**
	 * 警告、错误信息
	 *
	 * @access	public
	 * @return	void
	 */
	public static function appError($errno, $errstr, $errfile, $errline)
	{
		if ( !self::$isNotice )
		{
			return '';
		}

		if ( self::$mode == 2 )
		{
			$error  ="errno: $errno \n";
			$error .="desc: $errstr \n";
			$error .="file: ".basename($errfile)."\n";
			$error .="line: $errline \n";
			SpringException::throwException($error);
		} 
		else
		{
			$error = "错误号: $errno <br>";
			$error .="描  述: $errstr <br>";
			$error .= "所在文件: ".basename($errfile)."<br>";
			$error .= "所在行数: 第 $errline 行<br>";
			SpringException::throwException($error);
		}
	}

	/**
	 * 异常信息
	 *
	 * @access	public
	 * @return	void
	 */
	public static function appException($e)
	{
		SpringException::throwException($e->__toString());
	}
}
spl_autoload_register(array('Spring','loader'));
?>