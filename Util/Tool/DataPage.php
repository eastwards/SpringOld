<?
/**
 +------------------------------------------------------------------------------
 * Spring框架 数组分页
 +------------------------------------------------------------------------------
 * @mobile	13183857698
 * @qq		78252859
 * @author  void <lkf5_303@163.com>
 * @version 3.1.4
 +------------------------------------------------------------------------------
 */
class DataPage
{
	/**
	 * 分页对象
	 */
	public  $pager = null;

	/**
	 * 地址栏参数
	 */
	public $input  = array();


	/**
	 * 二维数组排序、分页
	 *
	 * @param  array	$rule	规则
	 * @access public
	 * @return array
	 */
	public function get($rule)
	{
		if ( !isset($rule['data']) || !is_array($rule['data']) || empty($rule['data']) ) 
		{
			return array();
		}

		if ( !is_object($this->pager) ) 
		{
			return array();
		}

		if ( isset($rule['order']) && is_array($rule['order']) && !empty($rule['order']) )
		{
			$field = key($rule['order']);
			$rank  = $rule['order'][$field] == 'asc' ? SORT_ASC : SORT_DESC;

			foreach ( $rule['data'] as $key => $row )
			{
				$order[$key]  = $row[$field];
			}	
			array_multisort($order, $rank,  $rule['data']);
		}

		$urlPage            = isset($this->input['page']) ? $this->input['page']  : 1;
		$page               = isset($rule['page']) ? $rule['page']  : $urlPage;
		$this->pager->page  = $page;
		$this->pager->input = $this->input;
		$rows               = isset($rule['limit']) ? $rule['limit'] : 20;
		$total              = count($rule['data']);
		$data['record']	    = array_slice($rule['data'], ($page - 1) * $rows, $rows);
		$data['pageBar']    = $this->pager->get($total, $rows);		

		return $data;
	}
}
?>