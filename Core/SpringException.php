<?
/**
 +------------------------------------------------------------------------------
 * Spring框架 异常处理(框架核心)
 +------------------------------------------------------------------------------
 * @mobile	13183857698
 * @qq		78252859
 * @author  void <lkf5_303@163.com>
 * @version 3.0
 +------------------------------------------------------------------------------
 */
class SpringException
{
	/**
	 * 抛出异常信息
	 *
	 * @access	public
	 * @param	string	$msg	异常信息
	 * @return	void
	 */
	public static function throwException($msg)
	{
		if ( Spring::$mode == 2 ) {
			SpringException::writeLog($msg);
			exit($msg);
		}
		
		SpringException::writeLog($msg);
		if ( file_exists(ResourceDir.'/MessageBox/error.html') )
		{
			$msgFile = ResourceDir.'/MessageBox/error.html';
			$path    = '/'.ResourceDir.'/';
		}
		else
		{
			$msgFile = LibDir.'/Template/UI/error.html';
			$path    = '';
		}
		require($msgFile);
		exit();
	}

	/**
	 * 记录日志
	 *
	 * @access	public
	 * @param	string	$content	日志内容
	 * @param	string	$file		日志文件名
	 * @return	void
	 */
	public static function writeLog($content, $file = 'spring.log')
	{
		if ( preg_match('/php/i',$file) || is_array($content) ) 
		{
			return '';
		}

		$logDir = defined('LogDir') ? LogDir : "log";
		if ( !file_exists($logDir) ) 
		{
			@mkdir($logDir);
			@chmod($logDir, 0777);
		}
		$host    = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']     : '';
		$uri     = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$reqUrl  = $host & $uri ? 'url: http://'.$host.$uri : '';
		$content = "【".date("Y-m-d H:i:s", time())."】\t\t".$content."\t\t".$reqUrl."\r\n";
		file_put_contents($logDir.'/'.$file, $content, FILE_APPEND);
	}
}
?>