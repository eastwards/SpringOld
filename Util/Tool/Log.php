<?
/**
 +------------------------------------------------------------------------------
 * Spring框架 日志记录
 +------------------------------------------------------------------------------
 * @mobile	13183857698
 * @qq		78252859
 * @author  void <lkf5_303@163.com>
 * @version 3.0
 +------------------------------------------------------------------------------
 */
class Log
{
	/**
	 * 日志记录
	 *
	 * @param	string	$content	日志内容
	 * @param	string  $file		日志文件名
	 * @param	string	$dir		日志存放目录
	 * @return	void
	 */
	public static function write($content, $file = 'log.log', $dir = '')
	{
		if ( preg_match('/php/i',$file) )
		{
			return ;
		}
		
		$logDir = $dir ? $dir : LogDir;
		if ( !file_exists($logDir) )
		{
			@mkdir($logDir);
			@chmod($logDir, 0777);
		}

		if ( is_array($content) )
		{
			$content = var_export($content, true);
		} 
		else
		{
			$content = "【".date("Y-m-d H:i:s", time())."】\t\t".$content."\r\n";
		}
		file_put_contents($logDir.'/'.$file, $content, FILE_APPEND);
	}
}
?>