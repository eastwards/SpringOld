<?
/**
 * 应用公用业务组件
 *
 * 应用相关的业务方法
 * 
 * @package	Model
 * @author	void
 * @since	2015-06-19
 */
abstract class AppModule extends Module
{
	/**
	 * 获取业务对象(系统对接时使用)
	 * @author	void
	 * @since	2015-06-19
	 *
	 * @access	public
	 * @param	string	$name	业务代理类名
	 * @return	object  返回业务对象
	 */
	public function importBi($name)
	{
		static $config = array();
		if ( empty($config) ) {
			require(ConfigDir.'/Extension/service.config.php');
		}
		
		static $objList = array();
		if ( isset($objList[$name]) && $objList[$name] ) {
			return $objList[$name];
		}

		$file = BiDir.'/'.strtolower($name).'.bi.php';
		require($file);
		$className      = $name.'Bi';
		$bi             = new $className();
		$bi->url        = $config[$bi->apiId]['url'];
		$objList[$name] = $bi;
		
		return $bi;
	}

	/**
	 * 获取用户的姓名
	 * @author	void
	 * @since	2015-06-12
	 *
	 * @access	public
	 * @param	int		$userId		用户id
	 * @return	string
	 */
	public function getName($userId)
	{
		return $this->import('user', 2)->get($userId, 'name');
	}
}
?>