<?
/**
 * 应用公用控制器
 *
 * 存放应用的公共方法
 *
 * @package	Action
 * @author	void
 * @since	2015-01-28
 */
abstract class AppAction extends Action
{
	/**
	 * 每页显示多少行
	 */
	public $rowNum  = 15;


	/**
	 * 前置操作(框架自动调用)
	 * @author	void
	 * @since	2015-01-28
	 *
	 * @access	public
	 * @return	void
	 */
	public function before()
	{
		//自定义业务逻辑
	}

	/**
	 * 后置操作(框架自动调用)
	 * @author	void
	 * @since	2015-01-28
	 *
	 * @access	public
	 * @return	void
	 */
	public function after()
	{
		//自定义业务逻辑
	}
}
?>