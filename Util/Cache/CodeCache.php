<?
/**
 +------------------------------------------------------------------------------
 * Spring框架	代码缓存组件(辅助工具)
 +------------------------------------------------------------------------------
 * @mobile  13183857698
 * @oicq    78252859
 * @author  VOID(空) <lkf5_303@163.com>
 * @version 3.0
 +------------------------------------------------------------------------------
 */
class CodeCache implements ICache
{
	/**
	 * 缓存路径
	 */
	public $path   = null;

	/**
	 * 生命周期
	 */
	public $life   = array();
	
	/**
	 * 缓存过期时间(默认为5分钟) 
	 */
	public $expire = 300;


	/**
	 * 类的构造子
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
	}

	/**
	 * 释放资源
	 *
	 * @access	public
	 * @return	void
	 */
	public function __destruct()
	{
		 $this->path   = null;
		 $this->life   = null;
		 $this->expire = null;
	}

	/**
	 * 初始化
	 *
	 * @access	public
	 * @return	void
	 */
	public function init()
	{
		$file = $this->path.'/expire.php';
		if ( file_exists($file) )	
		{
			require($file);
			$this->life = $life;
		}
	}

	/**
	 * 写入数据
	 *
	 * @access	public
	 * @param	mixed	$key	键 
	 * @param	mixed   $value  值
	 * @param	int		$expire 缓存时间(0持久存储)
	 * @return	bool
	 */
	public function set($key, $value, $expire = 0, $encoding = 0)
	{
		$key    = md5(serialize($key));
		$expire = $expire > 0 ? time() + $expire : time() + $this->expire;
		$file   = $this->path.'/'.$key.'.php';
		if ( empty($value) )
		{
			if ( file_exists($file) ) 
			{
				@unlink($file);
			}
			return true;
		}

		$this->life[$key] = $expire;
		$bool             = $this->createFile('var', $value, $file);
		if ( $bool )
		{
			$this->createFile('life', $this->life, $this->path.'/expire.php');
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取数据
	 *
	 * @access	public
	 * @param	mixed	$key		键 
	 * @param	int		$encoding	编码方式(0-3)
	 * @return	mixed
	 */
	public function get($key, $encoding = 0)
	{
		$key  = md5(serialize($key));
		$file = $this->path.'/'.$key.'.php';
		
		if ( !file_exists($file) )
		{
			return null;
		}
		
		if ( time() > $this->life[$key] )
		{
			if ( file_exists($file) ) 
			{
				@unlink($file);
			}
			$this->createFile('life', $this->life, $this->path.'/expire.php');
			return null;
		}
		
		require($file);

		return $var;
	}

	/**
	 * 删除数据
	 *
	 * @access	public
	 * @param	mixed	$key		键
	 * @param	int		$encoding	编码方式(0-3)
	 * @return	mixed
	 */
	public function remove($key, $encoding = 0)
	{
		$key  = md5(serialize($key));
		$file = $this->path.'/'.$key.'.php';
		if ( file_exists($file) )	
		{
			@unlink($file);
		}
		unset($this->life[$key]);
		$this->createFile('life', $this->life, $this->path.'/expire.php');
		return false;
	}

	/**
	 * 清空数据
	 *
	 * @access	public
	 * @return	bool
	 */
	public function clear()
	{
		if ( file_exists($this->path) )
		{
			$handle = opendir($this->path);
			while ( $file = readdir($handle) )
			{
				$file = $this->path . DIRECTORY_SEPARATOR . $file;
				if( !is_dir($file) && $file != '.' && $file != '..' ) 
				{
					if ( !@unlink($file) ) return false;
				}
			}
		}
		return true;
	}

	private function createFile($name, $value, $file)
	{
		return @file_put_contents($file, '<?$'.$name.'='.var_export($value, true).';?>');
	}
}
?>