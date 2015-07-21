<?
/**
 * 应用公用表单组件
 *
 * 表单数据收集
 *
 * @package	Form
 * @author	void
 * @since	2015-06-19
 */
abstract class AppForm extends Form
{
	/**
	 * 错误输出格式[0消息框提示、1json格式提示]
	 */
	protected $format = 0;

	/**
	 * 验证规则
	 */
	protected $rules = array(
		'require'  =>  '/.+/',
		'email'    =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
		'currency' =>  '/^\d+(\.\d+)?$/',
		'number'   =>  '/^\d+$/',
		'zip'      =>  '/^\d{6}$/',
		'int'	   =>  '/^[-\+]?\d+$/',
		'float'    =>  '/^[-\+]?\d+(\.\d+)$/',
		'english'  =>  '/^[A-Za-z]+$/',
		);
}
?>