<?
/**
 * 获取访问者ip
 *
 * @return  string
 */
function getClientIp()
{
	if ( getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown') )
	{
		$onlineip = getenv('HTTP_CLIENT_IP');
	}
	elseif ( getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown') )
	{
		$onlineip = getenv('HTTP_X_FORWARDED_FOR');
	}
	elseif ( getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown') )
	{
		$onlineip = getenv('REMOTE_ADDR');
	}
	elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown') )
	{
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	return $onlineip;
}

/**
 * URL跳转
 *
 * @param	string  $desc		消息文本
 * @param	string  $url		跳转地址
 * @param	string  $scripts	待执行的多个JS文件地址
 * @param	int		$seconds	停留时间(秒)
 * @return  void
 */
function goUrl($desc, $url, $scripts = array(), $seconds = 1)
{
	$msgFile = ResourceDir.'/default/redirect.html';
	$path    = '/'.ResourceDir.'/';
	$js      = '';

	foreach ( $scripts as $script ) {
		$js .= $script;
	}
	
	$tipInfo  = "$desc <br>请稍后,系统正在自动跳转........";
	$gotoUrl  = "<meta http-equiv='Refresh' content='$seconds; url=$url'>";
	require($msgFile);
	exit();
}

/**
 * 日志记录
 *
 * @param	string	$content	日志内容
 * @param	string  $file		日志文件名
 * @param	string	$dir		日志存放目录
 * @return	void
 */
function writeLog($content, $file = 'log.log', $dir = '')
{
	if ( preg_match('/php/i',$file) ) {
		return ;
	}
	
	$logDir = $dir ? $dir : LogDir;
	
	if (!file_exists($logDir) ) {
		@mkdir($logDir);
		@chmod($logDir, 0777);
	}
	
	if ( is_array($content) ) {
		$content = var_export($content, true);
	} else {
		$content = "【".date("Y-m-d H:i:s", time())."】\t\t".$content."\r\n";
	}
	file_put_contents($logDir.'/'.$file, $content, FILE_APPEND);
}

/**
 * 获取分页条
 *
 * @param	array   $pager   组合要素
 * @param	bool    $script  是否带有下拉
 * @return	string
 */
function getPageBar($pager, $script = true)
{
	if ( empty($pager) || !is_array($pager) ) {
		return '';
	}

	$html = '共'.$pager['recordNum'].'条/'. '共' . $pager['pageNum'] . '页' . '&nbsp;';

	if ( $pager['pageNum'] > 10 ) {
		$html .= '<a href="' . $pager['first'] . '">首页</a>' . '&nbsp;' .
			'<a href="' . $pager['pre']   . '">上页</a>' . '&nbsp;' .
			 '<a href="' . $pager['next']  . '">下页</a>' . '&nbsp;' .$pager['point'].
			 '<a href="' . $pager['last']  . '">尾页</a>' . '&nbsp;' ;
	} else {
		$html .= '<a href="' . $pager['first'] . '">首页</a>' . '&nbsp;' .
			'<a href="' . $pager['pre']   . '">上页</a>' . '&nbsp;' .
			 '<a href="' . $pager['next']  . '">下页</a>' . '&nbsp;' .
			 '<a href="' . $pager['last']  . '">尾页</a>' . '&nbsp;' ;
	}


	$html .= $script ? $pager['jump'] : '';

	return $html;
}

/**
 * 中断并输出提示
 *
 * @param  string	$msg	提示信息
 * @return void
 */
function halt($msg)
{
	SpringException::throwException($msg);
}

/**
 * 数组格式转换[2维转化成1维]
 *
 * @param  array  $list  2维数组
 * @param  array  $cols  2维数组中的列名[array(id)、array(id,name)]
 * @return array
 */
function arrayOne($list, $cols = array())
{
	if ( empty($list) || (empty($cols) || !is_array($cols)) ) return $list;
	
	$temp   = array();
	$length = count($cols);
	foreach ($list as $data) {
		if ( $length == 1 ) {
			$temp[] = isset($data[$cols[0]]) ? $data[$cols[0]] : '';
		} else {
			$temp[$data[$cols[0]]] = isset($data[$cols[1]]) ? $data[$cols[1]] : '';
		}
	}
	return $temp;
}

/**
 * 字符串1是否为字符串2的子串
 *
 * @param  string	$str1	字符串1
 * @param  string	$str2	字符串2
 * @return bool
 */
function strExist($str1, $str2)
{
	return !(strpos($str2, $str1) === FALSE);
}
?>