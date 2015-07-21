<?
/**
 +------------------------------------------------------------------------------
 * Spring框架 服务工厂(框架核心)
 +------------------------------------------------------------------------------
 * @mobile	13183857698
 * @qq		78252859
 * @author  void <lkf5_303@163.com>
 * @version 3.0
 +------------------------------------------------------------------------------
 */
class ServiceFactory
{
	public  static $isCached   = true;     //是否缓存组件配置信息
	public  static $cacheDir   = null;     //组件配置信息缓存目录
	public  static $configFile = null;     //级联配置文件
	private static $table      = array();  //对象登记表


	/**
	 * 负责资源的清理工作
	 *
	 * @access	public
	 * @return	void
	 */
	public static function dispose()
	{
		foreach ( ServiceFactory::$table as $k => $v ) 
		{
			ServiceFactory::$table[$k] = null;
		}
		ServiceFactory::$table      = null;
		ServiceFactory::$isCached   = null;
		ServiceFactory::$cacheDir   = null;
		ServiceFactory::$configFile = null;
	}

	/**
	 * 得到一个组件对象
	 *
	 * @access	public
	 * @param	string	$objId	组件标识id 
	 * @return	object
	 */
	public static function getObject($objId = null)
	{
		if ( empty($objId) || !is_string($objId) ) 
		{
			SpringException::throwException("objId参数错误!");
		}

		if ( isset(ServiceFactory::$table[$objId]) ) 
		{
			return ServiceFactory::$table[$objId];  
		}

		$config = ServiceFactory::getConfiguration($objId);
		$object = $config === false ? new stdClass() : ServiceFactory::create($config);
		if ( !is_object($object) )
		{
			SpringException::throwException("配置参数: $objId 构造对象失败!");
		}
		
		return $object;
	}

	/**
	 * 通过组件配置信息构造组件对象
	 *
	 * @access	private
	 * @param	array	$config	组件配置信息 
	 * @return	object
	 */
	private static function create($config)
	{
		if ( !file_exists($config['source']) )
		{
			SpringException::throwException($config['source']."文件不存在!");
		}
		
		//文件包含(一个或多个)
		if ( isset($config['import']) && is_array($config['import']) )
		{
			foreach ( $config['import'] as $file )
			{
				if ( !empty($file) && file_exists($file) ) 
				{
					require_once($file);
				}
			}
		}

		require_once($config['source']);
		if ( !class_exists($config['className']) ) 
		{
			SpringException::throwException("未定义的类 $config[className]");
		}

		$object = new $config['className']();
		
		//对属性赋值
		if ( isset($config['property']) && is_array($config['property']) )
		{
			foreach ( $config['property'] as $propertyName => $propertyVal )
			{
				if ( !empty($propertyName) && $propertyName != 'objRef' ) 
				{
					$object->$propertyName = $propertyVal;
				}
			}
		}
		
		//执行组件中指定的初始化方法
		if ( isset($config['initMethod']) && method_exists($object, $config['initMethod']) )
		{
			$object->$config['initMethod']();
		}
		
		//构造组件中的依赖对象
		if ( isset($config['property']['objRef']) && is_array($config['property']['objRef']) )
		{
			foreach ( $config['property']['objRef'] as $key => $val )
			{
				if ( $key && $val )
				{
					//递规构造对象
					$object->$key = ServiceFactory::getObject($val);
					ServiceFactory::registry($val, $object->$key);
				}
			}
		}

		//动态植入代码(钩子链)
		if ( isset($config['property']['hookList']) && is_array($config['property']['hookList']) )
		{
			foreach ( $config['property']['hookList'] as $key => $val )
			{
				if ( empty($key) || empty($val) ) 
				{
					continue;
				}
				$hook = ServiceFactory::getObject($val);
				ServiceFactory::registry($val, $hook);
				//执行钩子代码
				if ( is_object($hook) && method_exists($hook, 'work') )
				{
					$hook->object = $object;
					$hook->work(); 
				}
				$hook = null;
			}
		}
		ServiceFactory::registry($config['id'], $object);
		
		return $object; 
	}

	/**
	 * 组件对象登记
	 *
	 * @access	private
	 * @param	string	$objId	组件标识id
	 * @param	object	$object	组件对象
	 * @return	void 
	 */
	private static function registry($objId, $object)
	{
		ServiceFactory::$table[$objId] = $object;
	}

	/**
	 * 通过组件标识ID得到组件配置信息
	 *
	 * @access	private
	 * @param	string	$objId	组件标识id
	 * @return	mixed 
	 */
	private static function getConfiguration($objId)
	{
		$bool   = ServiceFactory::$isCached && ServiceFactory::$cacheDir && file_exists(ServiceFactory::$cacheDir);
		$config = $bool ? ServiceFactory::getCache($objId) : ServiceFactory::find($objId);

		if ( isset($config['ignore']) && $config['ignore'] && !file_exists($config['source']) ) 
		{
			return false;
		}

		if ( !file_exists($config['source']) ) 
		{
			SpringException::throwException("$config[className] 对象所在类文件 $config[source] 不存在!");
		}

		if ( isset($config['import']) && is_array($config['import']) )
		{
			foreach ( $config['import'] as $file)
			{
				if ( $file && !file_exists($file) )
				{
					SpringException::throwException("$config[className] 对象所依赖的文件 $file 不存在!");
				}
			}
		}
		return $config;
	}
	
	/**
	 * 从缓存中获取组件标识ID的组件配置信息
	 *
	 * @access	private
	 * @param	string	$objId	组件标识id
	 * @return	array 
	 */
	private static function getCache($objId)
	{
		if ( !file_exists(ServiceFactory::$cacheDir.'/'.$objId.'.php') )
		{
			$config = ServiceFactory::find($objId);
			ServiceFactory::cache($config);
		}
		else
		{
			require(ServiceFactory::$cacheDir.'/'.$objId.'.php');
		}
		return $config;
	}

	/**
	 * 通过组件标识ID扫描所有级联配置文件获取组件配置信息
	 *
	 * @access	private
	 * @param	string	$objId	组件标识id
	 * @return	array 
	 */
	private static function find($objId)
	{
		if ( !file_exists(ServiceFactory::$configFile) ) 
		{
			SpringException::throwException("级联配置文件: ServiceFactory::configFile 不存在!");
		}

		require(ServiceFactory::$configFile);
		$scanedFile = null;
		foreach ( $source as $objFile )
		{
			if ( empty($objFile['source']) || !file_exists($objFile['source']) )
			{
				continue;
			}
			
			require($objFile['source']);
			foreach ( $configs as $config )
			{
				if ( isset($config['enable']) && !$config['enable'] )
				{
					continue;
				}

				if ( $config['id'] == $objId )
				{
					return $config;
				}
			}
			$scanedFile = $scanedFile."\t".$objFile['source'];
		}
		ServiceFactory::remove();
		SpringException::throwException("对象标识ID: $objId 在配置文件 $scanedFile 中没有找到!");
	}

	/**
	 * 缓存组件标识ID的组件配置信息
	 *
	 * @access	private
	 * @param	array	$config	组件配置信息 
	 */
	private static function cache($config)
	{
		$str = "<?\r\n";
		foreach ( $config as $key => $val )
		{
			if ( is_array($config[$key]) )
			{
				if ( $key == 'import' )
				{
					foreach ( $config[$key] as $iKey=>$iVal )
					{
						$str .= "\$config['import']['$iKey'] = \"$iVal\";\r\n";
					}
				}
				
				if ( $key == 'property' )
				{
					foreach ( $config[$key] as $pKey => $pVal )
					{
						if ( $pKey == 'objRef' || $pKey == 'hookList' )
						{
							foreach ( $pVal as $k => $v ) 
							{
								$str .= "\$config['property']['$pKey']['$k'] = \"$v\";\r\n";
							}
							continue;
						}

						if ( is_array($pVal) )
						{
							foreach ( $pVal as $k => $v ) 
							{
								$str .= "\$config['property']['$pKey']['$k'] = \"$v\";\r\n";
							}
						}
						else
						{
							$str .= "\$config['property']['$pKey'] = \"$pVal\";\r\n";
						}
					}
				}				
				$str .= "\r\n";
			}
			else
			{
				$str .= "\$config['$key'] = \"$val\";\r\n";
			}
		}
		$str  = str_replace("\\","/", $str);
		$str .= "?>\r\n";
		$file = ServiceFactory::$cacheDir.'/'.$config['id'].'.php';
		file_put_contents($file, $str);
	}

	/**
	 * 清空组件配置信息缓存
	 *
	 * @access	private
	 */
	private static function remove()
	{
		if ( file_exists(ServiceFactory::$cacheDir) )
		{
			$handle = opendir(ServiceFactory::$cacheDir);
			while ( $file = readdir($handle) )
			{
				$file = ServiceFactory::$cacheDir . DIRECTORY_SEPARATOR . $file;
				if ( !is_dir($file) && $file != '.' && $file != '..' ) 
				{
					@unlink($file);
				}
			}
			closedir($handle);
		}
	}
}
?>