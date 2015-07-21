<?
/**
 * 通行证Api代理接口
 *
 * 获取账户信息、检查账户是否存在、账户注册、账户登录、修改密码、
 * 修改登陆账户、激活邮箱或手机、重置密码、注销账户
 *
 * @package	Bi
 * @author	void
 * @since	2014-11-17
 */
class PassportBi extends Bi
{
	/**
	 * 接口标识
	 */
	public $apiId = 2;


	/**
	 * 获取账户信息
	 * @author	void
	 * @since	2014-11-17
	 * 
	 * @access	public
	 * @param	string	$account	登录账户
	 * @param	int		$cateId		账户标识(1邮件、2手机、3用户名、4用户id)
	 * @return	array   返回账户所有信息(空为账户不存在)
	 */
	public function get($account, $cateId)
	{
		$param = array(
			'account' => $account,
			'cateId'  => $cateId,
			);
		$data = $this->request("passport/get/", $param);

		return empty($data) ? array() : $data;
	}

	/**
	 * 检查账户是否存在
	 * @author	void
	 * @since	2014-11-17
	 * 
	 * @access	public
	 * @param	string	$account	登录账户
	 * @param	int		$cateId		账户标识(1邮件、2手机、3用户名)
	 * @return	int     返回userId(0不存在、大于0存在)
	 */
	public function exist($account, $cateId)
	{
		$param = array(
			'account' => $account,
			'cateId'  => $cateId,
			);
		$data = $this->request("passport/exist/", $param);

		return isset($data['userId']) ? $data['userId'] : 0;
	}

	/**
	 * 账户注册
	 * @author	void
	 * @since	2014-11-17
	 *
	 * @access	public
	 * @param	string	$account	登录账户
	 * @param	int		$cateId		账户标识(1邮件、2手机、3用户名)
	 * @param	string	$password	登录密码
	 * @param	string	$ip			用户ip
	 * @return	int     返回userId(-1账户已存在、0失败或异常、大于0成功)
	 */
	public function register($account, $cateId, $password, $ip)
	{
		$param = array(
			'ip'       => $ip,
			'account'  => $account,
			'cateId'   => $cateId,
			'password' => $password,
			);
		$data = $this->request("passport/register/", $param);

		return isset($data['userId']) ? $data['userId'] : 0;
	}

	/**
	 * 账户登录
	 * @author	void
	 * @since	2014-11-17
	 * 
	 * @access	public
	 * @param	string	$account	登录账户
	 * @param	int		$cateId		账户标识(1邮件、2手机、3用户名)
	 * @param	string	$password	登录密码
	 * @param	string	$ip			登录ip
	 * @return	array   返回账户信息(为空账户或密码错误、异常)
	 */
	public function login($account, $cateId, $password, $ip)
	{
		$param = array(
			'ip'       => $ip,
			'account'  => $account,
			'cateId'   => $cateId,
			'password' => $password,
			);
		$data = $this->request("passport/login/", $param);
		
		return empty($data) ? array() : $data;
	}
	
	/**
	 * 修改密码
	 * @author	void
	 * @since	2014-11-17
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	string	$password	登陆密码
	 * @param	string	$newPwd		新密码
	 * @return	int     (-1账户不存在或密码错误、0失败或异常、1成功)
	 */
	public function changePwd($userId, $password, $newPwd)
	{
		$param = array(
			'userId'   => $userId,
			'password' => $password,
			'newPwd'   => $newPwd,
			);
		$data = $this->request("passport/changePwd/", $param);

		return isset($data['result']) ? $data['result'] : 0;
	}

	/**
	 * 修改登陆账户
	 * @author	void
	 * @since	2014-11-17
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	string	$account	新登录账户
	 * @param	int		$cateId		账户标识(1邮件、2手机)
	 * @return	int     -1账户只能为邮件或手机、0失败或异常、1成功
	 */
	public function changeAccount($userId, $account, $cateId)
	{
		$param = array(
			'userId'  => $userId,
			'cateId'  => $cateId,
			'account' => $account,
			);
		$data = $this->request("passport/changeAccount/", $param);

		return isset($data['result']) ? $data['result'] : 0;
	}

	/**
	 * 激活邮箱、手机
	 * @author	void
	 * @since	2014-11-17
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	int		$cateId		1邮件、2手机
	 * @return	int     (-1账户不存在、0失败或异常、1成功)
	 */
	public function active($userId, $cateId)
	{
		$param = array(
			'userId'  => $userId,
			'cateId'  => $cateId,
			);
		$data = $this->request("passport/active/", $param);

		return isset($data['result']) ? $data['result'] : 0;
	}

	/**
	 * 重置密码
	 * @author	void
	 * @since	2014-11-17
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	string	$newPwd		新密码
	 * @return	int     -1账户不存在、0失败或异常、1成功
	 */
	public function resetPwd($userId, $newPwd)
	{
		$param = array(
			'userId' => $userId,
			'newPwd' => $newPwd,
			);
		$data = $this->request("passport/resetPwd/", $param);

		return isset($data['result']) ? $data['result'] : 0;
	}

	/**
	 * 注销账户
	 * @author	void
	 * @since	2014-11-17
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @return	int     0失败、1成功
	 */
	public function remove($userId)
	{
		$param = array(
			'userId'  => $userId,
			);
		$data = $this->request("passport/remove/", $param);

		return isset($data['result']) ? $data['result'] : 0;
	}
}
?>