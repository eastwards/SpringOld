<?
/**
 * 工作日志
 *
 * 工作日志详情、获取工作日志分页列表、添加工作日志、编辑工作日志、删除工作日志
 * 
 * @package	Module
 * @author	void
 * @since	2015-06-11
 */
class EventModule extends AppModule
{
	/**
	 * 工作日志标签
	 */
	public    $tags  = array(
		'1'  => '熟悉需求',
		'2'  => '编写代码',
		'3'  => '工作会议',
		'4'  => '撰写文档',
		'5'  => '学习新技术',
		'6'  => '帮带、分享',
		'15' => '其他事务',
		);

	/**
	 * 引用业务模型
	 */
	public $models = array(
		'file'  => 'File',
		'user'  => 'User',
		'event' => 'Event',
		);


	/**
	 * 工作日志详情
	 * @author	void
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	int		$id		日志id
	 * @return	array
	 */
	public function getDetail($id)
	{
		$data = $this->import('event')->get($id);
		if ( empty($data) ) {
			return array();
		}
		
		$data['file'] = $this->import('file')->getList($data['attachment']);
		$data['name'] = $this->import('user')->get($data['userId'], 'name');
		$tagIds       = explode(",", $data['tagIds']);
		$tags         = "";
		foreach ( $tagIds as $tagId ) {
			$tagId && $tags .= $this->tags[$tagId]. " ";
		}
		$data['tags'] = $tags;

		return $data;
	}

	/**
	 * 获取工作日志分页列表
	 * @author	void
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	int		$page		页码
	 * @param	int		$num		返回条数
	 * @return	array
	 */
	public function getPageList($userId, $page = 1, $num = 20)
	{
		$data = $this->import('event')->getIdsPageList($userId, $page, $num);
		foreach ( $data['rows'] as &$row ) {
			$row = $this->getDetail($row['id']);
		}

		return $data;
	}

	/**
	 * 添加工作日志
	 * @author	void
	 * @since	2015-06-11
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	array	$data		日志数据
	 * @return	int
	 */
	public function add($userId, $data)
	{
		return $this->import('event')->add($userId, $data);
	}

	/**
	 * 编辑工作日志
	 * @author	void
	 * @since	2015-06-11
	 * 
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	array	$data		日志数据
	 * @return	bool
	 */
	public function edit($userId, $data)
	{
		return $this->import('event')->edit($userId, $data);
	}
	
	/**
	 * 删除工作日志(同时删除附件)
	 * @author	void
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	int		$userId		用户id
	 * @param	int		$id			工作日志id
	 * @return	bool
	 */
	public function delete($userId, $id)
	{
		$this->select('Event');
		$this->begin();
		$fileIds = $this->import('event')->get($id, 'attachment');
		$this->import('file')->delete($fileIds);
		$this->import('event')->delete($userId, $id);

		return $this->commit();
	}

	/**
	 * 删除用户全部工作日志(同时删除附件)
	 * @author	void
	 * @since	2015-06-12
	 *
	 * @access	public
	 * @param	int		$userId		用户id
	 * @return	bool
	 */
	public function deleteAll($userId)
	{
		$this->select('Event');
		$fileIds = $this->import('event')->get($id, 'attachment');
		$this->import('file')->delete($fileIds);
		$this->import('event')->delete($userId, $id);

		return $this->commit();
	}
}
?>