<?
/**
 * 图形验证码
 *
 * 验证码显示、校验验证码
 *
 * @package	Action
 * @author	void
 * @since	2014-12-09
 */
class AuthCodeAction extends Action
{
	/**
	 * 验证码显示
	 * @author	void
	 * @since	2014-12-09
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
		$img         = $this->com('vi');
		$img->width  = 80;
		$img->height = 30;
		$img->x      = 20;
		$img->y      = 8;
		$img->vName  = 'authCode'; //验证码名称
		$img->text   = $img->getRandNum();
		$img->create();
		$img->show();
	}

	/**
	 * 校验验证码
	 * @author	void
	 * @since	2014-12-09
	 *
	 * @access	public
	 * @return	void
	 */
	public function check()
	{
		$img        = $this->com('vi');
		$img->vName = 'authCode'; //验证码名称
		$code       = $this->input('code');
		$msg        = $img->verify($code) ? 'ok' : 'failure';
		
		//清除验证码
		$img->clear(); 
		print $msg;
	}
}
?>