<?
class ProjectTemplate
{
	/**
	 * 创建项目模板
	 *
	 * @access	public
	 * @param	string	$name	项目名称
	 * @return	void
	 */
	public static function create($name)
	{
		if ( file_exists($name) ) {
			header("Content-type: text/html; charset=utf-8");
			print '该项目已存在';
			return '';
		}

		require(LibDir.'/Util/Tool/IO.php');
		$project  = $name;
		$template = LibDir.'/Template/project';
		$dirs     = array(
			"App/Model",
			"App/Module",
			"App/Action",
			"App/View",
			"App/Entity",
			"App/Bi",
			"App/Console",
			"App/Form",
			"App/Util",
			"App/Hook",
			"Config",
			"Config/Db",
			"Config/Extension",
			"Config/Table",
			"Data",
			"Resource/Cache",
			"Resource/MessageBox",
			"Resource/Log",
			"Static/css",
			"Static/images",
			"Static/js/lib",
			"",	
			);
		
		foreach ( $dirs as $dir ) {
			IO::createDir("$project/$dir");
		}
		
		foreach ( $dirs as $dir ) {
			$list = $dir ? scandir("$template/$dir") : scandir("$template");
			foreach ( $list as $file ) {
				if ( $file != "." && $file != ".." && is_file("$template/$dir/$file") ) {
					copy("$template/$dir/$file", "$project/$dir/$file");
				}
			}
		}
	}

}
?>