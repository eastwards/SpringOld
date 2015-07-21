<?
/**
 * 404控制器
 *
 * 显示404错误
 *
 * @package	Action
 * @author	void
 * @since	2015-01-28
 */
class ErrorAction extends Action
{
	/**
	 * 显示404错误
	 * @author	void
	 * @since	2015-01-28
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
		//根据应用需要自行定义
		SpringException::throwException('发生404啦,请求的地址不存在!');
	}
}
?>