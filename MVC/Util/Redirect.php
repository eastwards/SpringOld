<?
/**
 +------------------------------------------------------------------------------
 * Spring框架 页面跳转组件(MVC辅助工具)
 +------------------------------------------------------------------------------
 * @mobile	13183857698
 * @qq		78252859
 * @author  void <lkf5_303@163.com>
 * @version 3.0
 +------------------------------------------------------------------------------
 */
class Redirect
{
	/**
	 * 显示消息提示框(带提示信息+跳转)
	 *
	 * @access	public
	 * @param	string  $desc		消息文本
	 * @param	string  $url		跳转地址
	 * @param	string  $scripts	待执行的多个JS文件地址
	 * @param	int		$seconds	停留时间(秒)
	 * @return  void
	 */
	public function show($desc, $url, $scripts = array(), $seconds=2)
	{
		if ( empty($desc) ) 
		{
			if ( is_array($scripts) && !empty($scripts) ) 
			{
				print implode('', $scripts);
			}
			print "<script language='javascript'>";
			print "location.href='$url';";
			print "</script>";
			exit();
		}

		if ( file_exists(ResourceDir.'/default/redirect.html') )
		{
			$msgFile = ResourceDir.'/default/redirect.html';
			$path    = '/'.ResourceDir.'/';
		}
		else
		{
			$msgFile = LibDir.'/Template/UI/redirect.html';
			$path    = '';
		}

		$js = '';
		foreach ( $scripts as $script ) 
		{
			$js .= $script;
		}

		$tipInfo  = "$desc";
		$gotoUrl  = "<meta http-equiv='Refresh' content='$seconds; url=$url'>";
		require($msgFile);
		exit();
	}
}
?>