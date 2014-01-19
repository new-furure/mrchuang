<?
class LogregModel extends Model {
    // 定义自动验证
    protected $_validate    =   array(
        array('user_email','require','填写注册邮箱'),
  		array('user_nickname','require','填写昵称'),
  		array('user_password','require','填写昵称')
        );
    // 定义自动完成
    protected $_auto    =   array(
        );
 }
