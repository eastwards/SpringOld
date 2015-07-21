<?
/**
 * 编辑工作日志表单验证模型
 *
 * @package	Form
 * @author	void
 * @since	2015-06-18
 */
class EventEditForm extends Form
{
	/**
	 * 字段映射(建立表单字段与程序字段或数据表字段的关联)
	 */
	protected $map = array(
		'id' => array(
			'field' => 'id',
			'match' => array('number', '', '日志id错误'), 
			),
		'content' => array(
			'field' => 'content',
			'match' => array('require', '', '工作内容不能为空'), 
			),
		'tag' => array(
			'field'  => 'tagIds',
			'method' => 'tag', 
			),
		'attachment' => array(
			'field'  => 'attachment',
			'method' => 'attachment', 
			),
		'doTime' => array(
			'field' => 'doTime',
			'method' => 'doTime', 
			),
		'userId' => array(
			'field' => 'userId',
			'match' => array('int', '', '用户id不能为空'),
			),
		);

	/**
	 * 标签处理
	 * @author	void
	 * @since	2015-01-20
	 * 
	 * @access	public
	 * @param	array	$value	标签id
	 * @return	string
	 */
	public function tag($value)
	{
		if ( empty($value) ) {
			$this->stop('标签不能为空');
		}

		return is_array($value) ? implode(',', $value) : '';
	}

	/**
	 * 附件处理
	 * @author	void
	 * @since	2014-12-18
	 * 
	 * @access	public
	 * @param	array	$value	附件
	 * @return	string
	 */
	public function attachment($value)
	{
		return !empty($value) && is_array($value) ? implode(',', $value) : '';
	}

	/**
	 * 验证日期、格式化处理
	 * @author	void
	 * @since	2014-12-22
	 * 
	 * @access	public
	 * @param	string	$value	工作日期
	 * @return	int
	 */
	public function doTime($value)
	{
		$value = trim($value);
		if ( empty($value) ) {
			$this->stop('工作日期不能为空');
		}

		return strtotime($value);
	}
}
?>