<?
/**
 * 应用公用模型
 *
 * 应用相关的公共方法
 * 
 * @package	Model
 * @author	void
 * @since	2015-01-13
 */
abstract class AppModel extends Model
{
	/**
	 * sql调试开关
	 */
	public $debug     = true;

	/**
	 * 查询字段
	 */
	protected $fields = array(
		'1' => 'id',
		);

	/**
	 * 初始化(构造模型时执行)
	 *
	 * @access	public
	 * @return	void
	 */
	public function init()
	{
	}

	/**
	 * 通过主键id获取信息(1条数据)
	 * @author	void
	 * @since	2015-01-13
	 *
	 * @access	public
	 * @param	int		$id		主键id
	 * @param	string	$field	字段名(只支持一个)
	 * @return	mixed   array|string
	 */
	public function get($id, $field = '')
	{
		return $this->findOne($id, $field);
	}

	/**
	 * 通过字段名获取信息(1条数据)
	 * @author	void
	 * @since	2015-01-19
	 *
	 * @access	public
	 * @param	int		$key	字段key
	 * @param	string	$value	字段值
	 * @return	array
	 */
	public function getByField($key, $value)
	{
		$field   = $this->fields[$key];
		$r['eq'] = array($field => $value);

		return $this->find($r);
	}
}
?>