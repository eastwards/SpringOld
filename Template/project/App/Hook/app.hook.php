<?
/**
 * 登陆检查
 *
 * 登录检查、其它检查等
 *
 * @package	Hook
 * @author	void
 * @since	2014-12-09
 */
class Hook
{
	/**
	 * 当前被请求的控制器
	 */
	public $mod    = '';

	/**
	 * 当前被请求的操作
	 */
	public $action = '';


	/**
	 * 钩子方法拦截用户操作
	 * @author	void
	 * @since	2014-12-09
	 *
	 * @access	public
	 * @return	void
	 */
	public function work()
	{
		return '';
		writeLog($_SERVER['REQUEST_URI']);
		$this->check($this->mod, $this->action );
	}

	/**
	 * 拦截用户操作(验证登录)
	 * @author	void
	 * @since	2014-12-09
	 *
	 * @access	private
	 * @param	string  $mod	控制器
	 * @param	string  $action 操作
	 * @return	void
	 */
	private function check($mod, $action)
	{
		/**
		 * 排除不需要登录的控制器、操作
		 * key为控制器、值为控制器类方法(命名全小写)
		 * * 代表控制器中的所有操作都不需要登录
		 * 'passport' => array('login', 'register', 'exist'),
		 * 表示控制器passport中的login、register、exist不需要登录
		 */
		$mods = array(
			'index'		=> '*',
			'user'		=> '*',  //user控制器所有操作都无需登陆
			'passport'  => array('login', 'register', 'exist'),
			);
		$allow = false;
		
		if ( isset($mods[$mod]) ) {
			if ( is_array($mods[$mod]) ) {
				$allow = in_array($action, $mods[$mod]) ? true : false;
			} else {
				$allow = $mods[$mod] == '*' ? true : false;
			}
		}
		
		$login = LoginAuth::check();
		if ( !$allow && !$login) {
			//跳转到登录界面
			goUrl('您还没有登录!', '/passport/login/');
			exit;
		}
	}
}
?>