<?
/**
 +------------------------------------------------------------------------------
 * Spring框架	文件缓存组件(辅助工具)
 +------------------------------------------------------------------------------
 * @mobile  13183857698
 * @oicq    78252859
 * @author  VOID(空) <lkf5_303@163.com>
 * @version 3.0
 +------------------------------------------------------------------------------
 */
class FileCache implements ICache
{
	/**
	 * 缓存路径
	 */
	public $path   = null;
	
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
		 $this->expire = null;
	}

	/**
	 * 初始化检查
	 *
	 * @access	public
	 * @return	void
	 */
	private function init()
	{
		if ( file_exists($this->path) ) 
		{
			return true;
		}

		$dirs  = explode('/', $this->path);
		$total = count($dirs);
		$temp  = '';
		for ( $i=0; $i<$total; $i++ )
		{
			$temp .= $dirs[$i].'/';
			if ( !is_dir($temp) )
			{
				if( !@mkdir($temp) ) return false;
				@chmod($temp, 0777);
			}
		}
		return true;
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
	public function set($key, $value, $expire = 0)
	{
		if ( !$this->init() ) 
		{
			return false;
		}
		$key        = serialize($key);
		$expire     = $expire > 0 ? time() + $expire : time() + $this->expire;
		$file       = $this->path.'/'.md5($key).'.php';
		$fileExpire = $this->path.'/'.md5($key).'_expire.php';
		if ( empty($value) )
		{
			if ( file_exists($file) ) @unlink($file);
			if ( file_exists($fileExpire) ) @unlink($fileExpire);

			return true;
		}

		file_put_contents($fileExpire, $expire);
		$data = serialize($value);
		$bool = file_put_contents($file, $data);

		if ( $bool )
		{
			clearstatcache();
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
	 * @param	mixed	$key	键 
	 * @return	mixed
	 */
	public function get($key)
	{
		if( !$this->init() ) 
		{
			return false;
		}

		$key  = serialize($key);
		$file = $this->path.'/'.md5($key).'.php';
		if ( !file_exists($file) )
		{
			return null;
		}

		$fileExpire = $this->path.'/'.md5($key).'_expire.php';
		$life       = file_exists($fileExpire) ? file_get_contents($fileExpire) : 0;
		if ( time() > $life )
		{
			if ( file_exists($file) ) @unlink($file);
			if ( file_exists($fileExpire) ) @unlink($fileExpire);
			return null;
		}

		$data = file_get_contents($file);
		if ( empty($data) ) 
		{
			return null;
		}
		
		return unserialize($data);
	}

	/**
	 * 删除数据
	 *
	 * @access	public
	 * @param	mixed	$key	键
	 * @return	bool
	 */
	public function remove($key)
	{
		$key  = serialize($key);
		$file = $this->path.'/'.md5($key).'.php';
		if ( file_exists($file) )	
		{
			return @unlink($file);
		}
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
}
?>