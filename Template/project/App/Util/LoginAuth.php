<?
/**
 * 登陆认证
 *
 * 获取、设置、更新、验证、注销认证数据
 *
 * @package	Util
 * @author	void
 * @since	2014-12-09
 */
class LoginAuth
{
	/**
	 * 获取认证数据
	 * @author	void
	 * @since	2014-12-09
	 * 
	 * @access	public
	 * @param	string	$key	键
	 * @return	mixed(string|array)
	 */
	public static function get($key)
	{
		static $user = array();
		if ( $key == 'all' && !empty($user) ) {
			return $user;
		}
		
		if ( !empty($user) && isset($user[$key]) ) {
			return $user[$key];
		}
		
		$user = Session::get('userAuth');
		$user = empty($user) ? array() : unserialize($user);
		
		if ( $key == 'all' && !empty($user) ) {
			return $user;
		}
		
		if ( !empty($user) && isset($user[$key]) ) {
			return $user[$key];
		}
		return array();
	}

	/**
	 * 设置认证数据
	 * @author	void
	 * @since	2014-12-09
	 * 
	 * @access	public
	 * @param	array	$user	认证数据(键值对)
	 * @param	int		$expire	过期时间(秒)
	 * @return	void
	 */
	public static function set($user, $expire = 3600)
	{
		if ( !empty($user) && is_array($user) ) {
			$key = 'userAuth';
			Session::set($key, serialize($user), $expire);
		}
	}

	/**
	 * 更新认证数据
	 * @author	void
	 * @since	2014-12-09
	 * 
	 * @access	public
	 * @param	array	$user	认证数据(键值对)
	 * @param	int		$expire	过期时间(秒)
	 * @return	void
	 */
	public static function update($user, $expire = 3600)
	{
		$data = Session::get('all');
		$user = array_merge($data, $user);
		LoginAuth::set($user, $expire);
		$data = $user = null;
	}

	/**
	 * 验证登录
	 * @author	void
	 * @since	2014-12-09
	 * 
	 * @access	public
	 * @return	bool	
	 */
	public static function check()
	{
		$user = LoginAuth::get('all');
		$bool = empty($user) || !is_array($user) ? false : true;
		
		return $bool;
	}

	/**
	 * 注销登录
	 * @author	void
	 * @since	2014-12-09
	 * 
	 * @access	public
	 * @return	void
	 */
	public static function logout()
	{
		Session::clear();
	}
}
?>