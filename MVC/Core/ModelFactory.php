<?
/**
 +------------------------------------------------------------------------------
 * Spring框架 模型工厂(MVC核心)
 +------------------------------------------------------------------------------
 * @mobile	13183857698
 * @qq		78252859
 * @author  void <lkf5_303@163.com>
 * @version 3.0
 +------------------------------------------------------------------------------
 */
class ModelFactory
{
	public  $path      = null;     //模型类存放根路径
	public  $models    = array();  //指定模型的引用关系
	public  $suffix    = 'model';  //文件后缀
	public  $name      = null;     //当前构造模型的名称
	private $oldPath   = null;     //模型类初始存放根路径
	private $objTable  = array();  //模型对象缓存


	/**
	 * 初始化工作
	 *
	 * @access	public   
	 * @return	void
	 */
	public function __construct()
	{
	}

	/**
	 * 负责资源的清理工作
	 *
	 * @access	public
	 * @return	void
	 */
	public function __destruct()
	{
		foreach ( $this->objTable as $k=>$v )  
		{
			 $this->objTable[$k] = null;
		}

		$this->objTable  = null;
		$this->models    = null;
		$this->path      = null;
		$this->oldPath   = null;
		$this->suffix    = null;
	}

	/**
	 * 通过标签获取对象
	 *
	 * @access	public
	 * @param	string	$name	类标签
	 * @return	object
	 */
	public function getObject($name)
	{
		if ( isset($this->objTable[$name]) && is_object($this->objTable[$name]) )
		{
			return $this->objTable[$name];
		}

		//记录模型类初始存放根路径
		if ( !strpos($this->path, '/') === FALSE )
		{
			$this->oldPath = $this->path;
		}

		if ( !(strpos($this->models[$this->suffix][$name], '/') === FALSE) )
		{
			$bool       = ltrim($this->models[$this->suffix][$name], '/') == $this->models[$this->suffix][$name] ? true : false;
			$temp       = explode('/', $this->models[$this->suffix][$name]);
			$model      = $temp[count($temp)-1].ucfirst($this->suffix);
			$this->name = $temp[count($temp)-1];
			unset($temp[count($temp)-1]);
			$this->path = implode('/', $temp);
			$object     = $this->create($model);
		}
		else
		{
			$this->path = $this->oldPath ? $this->oldPath : $this->path;
			$model      = $this->models[$this->suffix][$name].ucfirst($this->suffix);
			$this->name = $this->models[$this->suffix][$name];
			$object     = $this->create($model);
		}

		$object->entity        = $this->name;
		$this->objTable[$name] = $object;
		
		return $this->objTable[$name];
	}

	/**
	 * 构造模型对象并初始化
	 *
	 * @access	private
	 * @param	string	$model	模型类名
	 * @return	object
	 */
	private function create($model)
	{
		$length    = strlen($this->suffix);
		$modelFile = substr(strtolower($model), 0, strlen($model)-$length).'.'.strtolower($this->suffix);
		$reqFile   = $this->findFile($modelFile);

		if ( $reqFile == null ) 
		{
			throw new SpringException("找不到文件$this->path/$modelFile.php");
		}

		require_once($reqFile);
		if ( !class_exists($model) ) 
		{
			throw new SpringException("文件$this->path/$modelFile.php中找不到类 $model");
		}
		
		return new $model();
	}

	/**
	 * 遍历模型层目录,查找请求的目标文件
	 *
	 * @access	private
	 * @param	string	$model	模型类名
	 * @return	string
	 */
	private function findFile($model)
	{
		$reqFile = $this->path.'/'.$model.'.php';

		//在模型层根目录查找请求的目标文件
		if ( file_exists($reqFile) )
		{
			$this->path = $this->oldPath ? $this->oldPath : $this->path;

			return $reqFile;
		}

		if ( !file_exists($this->path) ) 
		{
			throw new SpringException('找不到目录 '.$this->path);
		}

		//遍历模型层目录下的所有子目录,查找请求的目标文件
		if ( false !== ($dh = opendir($this->path)) )
		{
			while ( false !== ($file = readdir($dh)) ) 
			{
				$path = $this->path.'/'.$file;
				if ( is_dir($path) )
				{
					$reqFile = $path.'/'.$model.'.php';
					if ( file_exists($reqFile) ) return $reqFile;
				} 
			}
		}
		return null;
	}
}
?>