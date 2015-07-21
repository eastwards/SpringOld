<?
/**
 * 控制台运行
 *
 * 查看资料
 *
 * @package	Action
 * @author	void
 * @since	2015-01-13
 */
class CommandAction extends ConsoleAction
{
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'user' => 'User',
		);
	
	/**
	 * 查看资料
	 * @author	void
	 * @since	2014-12-17
	 *
	 * @access	public
	 * @return	void
	 */
	public function profile()
	{
		$userId = $this->input('userId', 'int');
		$userId = $userId ? $userId : $this->userId;
		$data   = $this->import('user')->getDetail($userId);
		print_r($data);
	}
}
?>