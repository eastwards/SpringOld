<?
/**
 +------------------------------------------------------------------------------
 * Spring框架 数据实体层sphinx接口
 +------------------------------------------------------------------------------
 * @mobile	13183857698
 * @qq		78252859
 * @author  void <lkf5_303@163.com>
 * @version 3.0
 +------------------------------------------------------------------------------
 */
class SphinxApi extends Entity
{
	/**
	 * 索引名称,其值格式为: product 表示数据源只有一个索引
	 * 'productMain,productDelta' 表示数据源为主索引+增量索引
	 */
	protected $index   = '';

	/**
	 * 属性字段(参与索引的表字段)
	 */
	protected $field  = array();

	/**
	 * 是否开启sphinx查询缓存
	 */
	protected $qc     = false;

	/**
	 * 获取多条数据
	 *
	 * @access	public
	 * @param	array	$rule  数据查询规则
	 * @return	array
	 */
	public function find($rule)
	{
		if ( $this->qc )
		{
			$key  = md5(serialize($rule));
			$data = $this->com('mem')->get($key);
			if ( !empty($data) )
			{
				return $data;
			}
		}

		$rule['pk']     = $this->pk;
		$rule['isPage'] = 0;
		$list           = array();
		$result         = $this->com('sphinx')->find($this->index, $rule);
		$rule['isFind'] = isset($rule['isFind']) ? $rule['isFind'] : false;

		if ( $rule['isFind'] ) 
		{
			foreach ( $result['rows'] as $row )
			{
				$item = $this->findOne($row['id']);
				if ( !empty($item) )
				{
					$list[] = $item;
				}
			}
		}

		!$rule['isFind'] && $list = $result['rows'];
		
		$this->qc && $this->com('mem')->set($key, $list, 60);
		$rule = $result = null;
		
		return $list;
	}

	/**
	 * 获取多条数据(数据分页时用)
	 *
	 * @access	public
	 * @param	array	$rule	数据查询规则
	 * @return	array
	 */
	public function findAll($rule)
	{
		if ( $this->qc )
		{
			$key  = md5(serialize($rule).$_SERVER['REQUEST_URI']);
			$data = $this->com('mem')->get($key);
			if ( !empty($data) )
			{
				return $data;
			}
		}
		
		$rule['pk']     = $this->pk;
		$rule['isPage'] = 1;
		$list           = array();
		$result         = $this->com('sphinx')->find($this->index, $rule);
		
		if ( $rule['isFind'] ) 
		{
			foreach ( $result['rows'] as $row )
			{
				$item = $this->findOne($row['id']);
				if ( !empty($item) )
				{
					$list[] = $item;
				}
			}
		}

		!$rule['isFind'] && $list = $result['rows'];

		$data = array(
			'rows'  => $list,
			'total' => $result['total'],
			);
		$this->qc && $this->com('mem')->set($key, $data, 60);
		$rule = $result = $list = null;
		
		return $data;
	}

	/**
	 * 创建一条数据
	 *
	 * @access	public
	 * @param	array	$data	数据信息[键值对]
	 * @return	int				0失败、大于0成功
	 */
	public function create($data)
	{
		$id = parent::create($data);
		if ( $id ) 
		{
			//写数据入增量表
		}
		
		return $id;
	}

	/**
	 * 修改数据
	 *
	 * @access	public
	 * @param	array	$data	被修改的数据[键值对]
	 * @param	array	$rule	数据修改规则
	 * @return	bool
	 */
	public function modify($data, $rule)
	{
		return parent::modify($data, $rule);
		$bool            = parent::modify($data, $rule);
		$rule            = array();
		$rule['scope']   = array('updated' => array(time()-4, time()+1));
		$rule['order']   = array('updated' => 'desc');
		$rule['limit']   = 10;
		$list            = parent::find($rule);
		$this->updateAttributes($list);
		$list = $rule    = null;
		
		return $bool;
	}

	/**
	 * 删除数据 
	 *
	 * @access	public
	 * @param	array	$rule	数据删除规则
	 * @return	bool
	 */
	public function remove($rule)
	{
		return parent::remove($rule);
		$rule['col']   = array($this->pk);
		$rule['limit'] = 20;
		$list          = parent::find($rule);
		$field         = array('removed');

		foreach ( $list as $data )
		{
			$value = array($data[$this->pk] => array(1));
			$this->UpdateAttributes($this->index, $field, $value);
		}
		$list = $field = null;
		
		return parent::remove($rule);
	}

	/**
	 * 更新文档属性值
	 *
	 * @access private
	 * @param  array	$list	列表数据
	 * @return void
	 */
	private function updateAttributes($list)
	{
		foreach ( $list as $data )
		{
			$value = array();
			foreach ( $this->field as $key ) 
			{
				$data[$key] = isset($data[$key]) ? $data[$key] : 0;
				$value[]    = preg_match("/\d+\.\d+/", $data[$key]) 
							  ? floatval($data[$key])
							  : intval($data[$key]);
			}
			$value = array($data[$this->pk] => $value);
			$this->com('sphinx')->UpdateAttributes($this->index, $this->field, $value);
		}	
	}
}
?>