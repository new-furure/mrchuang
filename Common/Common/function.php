<?php
/**
 * Home前台和Admin后台自定义公用函数库
 * ++++++++++++++++++++++++++++++++++++++++++++
 * + 函数命名命名规则： 小写和下划线
 * ++++++++++++++++++++++++++++++++++++++++++++
 * ---------------函数总览表--------------------
 * 上传文件
 *  upload_file( $savePath, $saveName, $postName, $fileexts="img" )
 *
 * 安全验证邮箱格式是否正确
 *  validate_email( $email )
 *
 * 通过id生成对应页面的url连接 unfinished
 *  get_url_by_id( $id )
 *
 * 获取id类型
 *  get_id_type( $id )
 *
 * 随机验证,及其产生验证
 *  validate( $id, $key=null )
 *
 * 用户类型快速查询
 *  ac( $user_type, $class=3 )
 *
 * 通过id查询用户类型
 *  ac_by_id( $id=0, $class=3 )
 *
 * 设置 用户类型
 *  set_user_type( &$user_type, $is_active=null, $is_org=null, $org_type=null )
 *
 * 密码加密 unfinished
 *  encryption( $p, $i )
 *
 * 生成N位随机字符串
 *  random_string( $n=16)
 *
 * 发送邮件
 * send_mail($to, $type, $content = '')
 *
 * 邀请和验证邀请
 * invite( $key )
 *
 * 删除邀请码
 * delete_invitation($code)
 * ---------------------------------------------
 */

/**
 * 单个文件上传
 * 上传文件的根目录/Uploads/
 *
 * @author NewFuture
 *
 * @param string  $savePath 相对于/Uploads/保存路径,
 * @param string  $savename 保存文件名(不含后缀)，文件后缀不变
 * @param string  $postName 文件在表单中的名字
 * @param array/string arry 自定义类型 ，string指定类型
 * [参数值说明]
 * ---$fileexts="img" 图片
 * ---$fileexts="doc" 文档
 * ---$fileexts=null null表示都允许
 * ---$fileexts=arry('xls','xlsx') 限制为.xls和xlsx文件
 * 返回值
 * @return $url 文件的路径 null上传失败 ，
 * --- sample
 *  $url=upload_file("\Img\User\Photo\"，,$id,'photo','img');
 */
function upload_file( $savePath, $saveName, $postName, $fileexts="img" ) {

  $upload = new \Think\Upload();// 实例化上传类
  $upload->maxSize   =     5*1024*1024;//5M ;// 设置附件上传大小

  // 设置附件上传类型
  if ( is_array( $fileexts ) ) {
    $upload->exts =$fileexts;
  }  elseif ( strcasecmp( $fileexts, "img" )==0 ) {
    $upload->exts      =   array( 'jpg', 'jpeg', 'png', 'gif' );
  }elseif ( strcasecmp( $fileexts, "doc" )==0 ) {
    $upload->exts=array( 'doc', 'docx', 'pdf', 'wps', 'txt', 'htm', 'html' );
  }else {
    $upload->exts=null;
  }

  // 设置附件上传目录
  $upload->rootPath=C( 'UPLOAD_ROOT' );
  $upload->savePath=$savePath;

  $upload->saveName = $saveName;
  $upload->autoSub =false;//不创建子目录
  $upload->replace =ture;//同名覆盖
  // 上传单个文件
  $info   =   $upload->uploadOne( $_FILES[$postName] );
  if ( !$info ) {
    // 上传错误
    return null;
  }else {

    $relativeUrl=$info['savepath'].$info['savename'];//相对路径

    if ( C( "IS_SAE" ) ) {
      //sae 上传成功 获取上传文件信息
      $st = new SaeStorage();
      return $st->getUrl( 'uploads', $relativeUrl );

    }else {
      //本地上传
      return C( 'UPLOAD_ROOT' ).$relativeUrl;
    }
  }
}

/**
 * 不包含圈子帖子
 * 通过id获取url（相对于站点根目录的地址）
 * 根据具id自动判断类型便于垮模块使用
 * 对于帖子下的回复应该还有页内定位(#id)
 * 使用U方法
 *
 * @author NewFuture
 * @param int     $id 要访问目标的id
 * @return string $url 对应得链接地址，
 * 如果不存在返回null
 */
function get_url_by_id( $id ) {
  $url=null;

  switch ( get_id_type( $id ) ) {
    //类型判断
  case 'user':
    //用户
    $url=U( 'User/info', "id=$id" );

    break;

  case 'article':
    //文章.
    $Article=M( "artcile" );

    $type=$Article->getFieldByArticle_Id( $id, "article_type" );

    switch ( $type ) {
    case C( "PROJECT_TYPE" ):
      //项目
      $url=U( "Project/detail", "aid=$id" );
      break;

    case C( "POLICY_TYPE" ):
      //政策
      $url=U( "Policy/detail", "aid=$id" );
      break;

    case C( "QUESTION_TYPE" ):
      //问题
      $url=U( "Question/detail", "aid=$id" );
      break;

    case C( "POST_TYPE" ):
      //圈子post

      break;

    default:
      // code...
      break;
    }
    break;

  case 'comment':
  case 'second_comment':
    //回复评论 附加页内定位
    //二级评论
    $comment=M( 'comment' )->getByCommentId( $id );
    $artcile_id=$comment['artclie_id'] ;

    switch ( $comment['commnet_type'] ) {

    case C( 'PROJECT_IMPROVE' )://项目改善
    case C( 'PROJECT_COMMENT' )://项目评论
      $url=U( 'Project/detail', "aid=$id" )."#$artcile_id";
      break;

    case C( 'POLICY_COMMENT' )://政策评论
      $url=U( 'Policy/detail', "aid=$id" )."#$artcile_id";
      break;

    case C( 'QUESTION_COMMENT' )://问题评论
      $url=U( 'Question/detail', "aid=$id" )."#$artcile_id";
      break;

    case C( 'IDEA_COMMENT' )://创意评论
      //$url=;
      break;

    case C( 'VC_COMMENT' )://风投回复
      break;

    case C( 'INCUBATOR_COMMENT' )://孵化器评论
      break;

    case C( 'CIRCLE_POST_COMMENT' ):
      // code...
      break;

    default:
      // code...
      break;
    }


    break;

  case 'circle':
    //圈子
    $url=U( "Circle/detail", "cid=$id" );
    break;
  case 'notice':
    //通知

    break;

  }

  return $url;
}

/**
 * 一次性验证生成 和验证
 *
 * @author NewFuture
 * @param int     $id 验证ID值
 * @param string  /无 $key 随机验证字符, null生成或者更新随机验证
 * @return mixed 是否验证成功 请使用$result===true而不是==判断返回结果
 * sample
 * 生成$key=validate(1234)为1234生成验证信息 返回随机验证字符串$key
 * 验证$result= validate（12345，"ABCDSUNM")12345的随机密码是否匹配
 * true;通过验证，（false,"出错原因"）验证失败；
 */
function validate( $id, $key=null ) {

  $Validate=M( 'validate' );
  if ( $key==null ) {
    //生成验证信息
    $Validate->where( "validate_id=".$id )->delete();
    $v['validate_id']=$id;
    $key=random_string();
    $v['validate_key']=$key;
    $Validate->add( $v );
    return $key;
  }else {
    //进行验证
    $result=null;
    $data=$Validate->where( 'validate_id='. $id )->find();
    if ( $data ) {
      //验证信息存在，验证并删除数据库
      $createtime=$data['validate_time'];
      $days=round( ( time()-strtotime( $createtime ) )/3600/24 ) ;
      if ( $days>C( "VALIDATE_EFFECTIVE_TIME" ) ) {
        //过期
        $result=array( false, "验证已经超过".C( "VALIDATE_EFFECTIVE_TIME" )."天限制" );
      }elseif ( $key=== $data['validate_key'] ) {
        //通过验证
        $result=true;

      }else {
        $result=array( false, "验证不匹配" );
      }
      $Validate->where( "validate_id=".$id )->delete();

    } else {
      $result=array( false, "验证信息不存在" );
    }
    return $result;
  }
}

/**
 * 获取id类型
 * 只做数值判断，不查询数据库
 *
 * @author NewFuture
 * @param int     $id;
 * @return string 对应数据库表名
 *    结果"admin"  "article" "tag" "circle" "comment" "notice" "message" null
 */
function get_id_type( $id ) {
  if ( $id>C( "MIN_USER_ID" )&&$id<C( "MAX_USER_ID" ) ) {
    //用户
    return "user";
  }elseif ( $id>C( "MIN_ARTICLE_ID" )&&$id<C( "MAX_ARTICLE_ID" ) ) {
    //文章
    return "article";
  } elseif ( $id>C( "MIN_COMMONT_ID" )&&$id<C( "MAX_COMMONT_ID" ) ) {
    //评论
    return "comment";
  }elseif ( $id>C( "MIN_SECOMMENT_ID" )&&$id<C( "MAX_SECOMMENT_ID" ) ) {
    //二级评论
    return "second_comment";
  }  elseif ( $id>C( "MIN_TAG_ID" )&&$id<C( "MAX_TAG_ID" ) ) {
    //标签
    return "tag";
  } elseif ( $id>C( "MIN_CIRCLE_ID" )&&$id<C( "MAX_CIRCLE_ID" ) ) {
    //圈子
    return "circle";
  }  elseif ( $id>C( "MIN_NOTICE_ID" )&&$id<C( "MAX_NOTCIE_ID" ) ) {
    //通知
    return "notice";
  }elseif ( $id>C( "MIN_MESSAGE_ID" )&&$id<C( "MAX_MESSAGE_ID" ) ) {
    //消息
    return "message";
  }elseif ( $id>C( "MIN_ADMIN_ID" )&&$id<C( "MAX_ADMIN_ID" )  ) {
    //管理员
    return "admin";
  } else {
    return null;
  }

}

/**
 * 用户状态查询，需要先查表
 *
 * @author NewFuture
 * @param int     $user_type 【必须】 用户的usertype的字段值
 * @param int     $class     【可选】查询状态的类型[1,2,3]默认3
 * @return int与参数有关
 * 对照表
 * [$class=1,查询激活状态  ，返回0，1]
 * ---此项暂不使用///[$class=2,查询是否是组织，返回0，1]
 * [$class=3,查询组织（包括所属组织）内别，返回，0-6]参照配置
 * 其他null
 */
function ac( $user_type, $class=3 ) {
  switch ( $class ) {
  case 1:
    //激活状态
    return ( $user_type>>( C( "ACTIVE_BIT" )-1 ) )&1;
    break;

  case 2:
    //组织还是个人
    return ( $user_type>>( C( "ORG_BIT" )-1 ) )&1;
    break;

  case 3:
    //组织或者所属组织分类
    return $user_type>>2;
    break;
  }
}

/**
 * 用户状态查询，每次需要需要数据库
 *
 * @author NewFuture
 * @param int     $id                                                   【可选】0表示当前用户
 * @param int     $class【可选】查询状态的类型[1,2,3]默认3
 * @return int与参数有关
 * 对照表
 * [$class=1,查询激活状态  ，返回0，1]
 * [$class=2,查询是否是组织，返回0，1]
 * [$class=3,查询组织（包括所属组织）内别，返回，0-6]参照配置
 * 其他null
 * 【sample】
 * 如需要判断 当前 是否为用户是否是 政府部门或者为其中成员
 *  ac_by_id()==C("GOVERNMENT")
 *
 * 如果要判断$uid是否为 政府部门组织
 * 此时建议使用ac()效率更高
 *  ac_by_id($uid)==C("GOVERNMENT") && ac_by_id($id,2)
 *
 * @version 2.0 组织类型直接代表类型
 */
function ac_by_id( $id=0, $class=3 ) {
  if ( !$id ) {
    $id=get_id();
  }
  //获取用户类型
  $user_type=M( "User" )->getFieldByUserId( $id, 'user_type' );

  return ac( $user_type, $class );
}

/**
 * 设置用户类型
 * 不设置的参数必须设为null，不能设为0或者false
 *
 * @author Future
 * @param int     &$user_type 用户的类型
 *
 * @param unknown $is_active  是否激活
 *          null      不修改
 *          false/0   未激活
 *           true/1   激活
 *
 * @param unknown $is_org     是否为组织
 *          null      不修改
 *          false/0   个人
 *           true/1   组织
 * @param unknown $org_type   所属组织类型
 *          null      不修改
 *         C("...6个配置...") 修改为对应组织
 *
 * example
 *    设置一个 未激活的 政府 组织账号
 *  set_user_type($user_type,0,1,C("GOVERNMENT"));
 *
 *  加入到 某风投
 *  set_user_type($user_type,null,null,C("VC"));
 */
function set_user_type( &$user_type, $is_active=null, $is_org=null, $org_type=null ) {

  if ( !$user_type ) {
    $user_type=0;
  }
  //激活位设置
  if ( $is_active===1||$is_active===true ) {
    //激活
    $user_type |=( 1<<( C( "ACTIVE_BIT" )-1 ) );//激活位 置1

  }elseif ( $is_active===0||$is_active===false ) {
    //未激活
    $user_type&=~( 1<<( C( "ACTIVE_BIT" )-1 ) );//激活位 置0
  }

  //组织位设置
  if ( $is_org===1||$is_org===true ) {
    //是组织
    $user_type |=( 1<<( C( "ORG_BIT" )-1 ) );//组织位 置1
  }elseif ( $is_org===0||$is_org===false ) {
    //个人
    $user_type&=~( 1<<( C( "ORG_BIT" )-1 ) );//组织位 置0
  }


  //组织类型设置
  if ( $org_type===null ) {
    return $user_type;
  }elseif ( $org_type>=0 && $org_type<=C( "GOVERNMENT" ) ) {
    $user_type=( $user_type&3 )|( $org_type << 2 );
  }
  return $user_type;
}

/**
 * 严格验证email 格式合法性
 *
 * Validate an email address.
 * Provide email address (raw input)
 * Returns true if the email address has the email
 * address format and the domain exists.
 *
 * @author NewFuture copy from http://developer.51cto.com/art/200810/92652_all.htm
 * @param string  $email 邮箱字符串
 * @return bool 合法 true
 */
function validate_email( $email ) {
  $isValid = true;
  $atIndex = strrpos( $email, "@" );
  if ( is_bool( $atIndex ) && !$atIndex ) {
    $isValid = false;
  }
  else {
    $domain = substr( $email, $atIndex+1 );
    $local = substr( $email, 0, $atIndex );
    $localLen = strlen( $local );
    $domainLen = strlen( $domain );
    if ( $localLen < 1 || $localLen > 64 ) {
      // local part length exceeded
      $isValid = false;
    }
    elseif ( $domainLen < 1 || $domainLen > 255 ) {
      // domain part length exceeded
      $isValid = false;
    }
    elseif ( $local[0] == '.' || $local[$localLen-1] == '.' ) {
      // local part starts or ends with '.'
      $isValid = false;
    }
    elseif ( preg_match( '/\\.\\./', $local ) ) {
      // local part has two consecutive dots
      $isValid = false;
    }
    elseif ( !preg_match( '/^[A-Za-z0-9\\-\\.]+$/', $domain ) ) {
      // character not valid in domain part
      $isValid = false;
    }
    elseif ( preg_match( '/\\.\\./', $domain ) ) {
      // domain part has two consecutive dots
      $isValid = false;
    }
    elseif
    ( !preg_match( '/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
        str_replace( "\\\\", "", $local ) ) ) {
      // character not valid in local part unless
      // local part is quoted
      if ( !preg_match( '/^"(\\\\"|[^"])+"$/',
          str_replace( "\\\\", "", $local ) ) ) {
        $isValid = false;
      }
    }
    if ( $isValid && !( checkdnsrr( $domain, "MX" ) ||checkdnsrr( $domain, "A" ) ) ) {
      // domain not found in DNS
      $isValid = false;
    }
  }
  return $isValid;
}



/**
 * 加密密码
 *
 * @author NewFuture
 */
function encryption( $p, $i ) {
  return crypt($p,$i);
}


/**
 * 生成n位随机字符串
 *
 * @author NewFuture Copy form http://www.oschina.net/code/snippet_4873_4493回复mark35
 * @param int     n=16 位数
 */
function random_string( $n=16 ) {
  return substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, $n );
}


/**
 * 发送邮件
 *
 * @author 牛亮
 * @param to->收件人地址 subject->邮件主题 content->邮件主要内容
 * @return -1->type错误 array(...)->发送相关信息
 */
function send_mail( $to, $type, $content ) {
  switch ( $type ) {
  case C( 'ACTIVE_MAIL' ):
    //激活
    $prefix = '您好,很高兴您注册了闯先生网,请点击以下链接进行账号的激活<br>';
    $suffix = '<br>该邮件来自闯先生网';
    $option['subject'] = '闯先生网用户注册激活';
    break;

  case C( 'PASSWORD_MAIL' ):
    //修改密码
    $prefix = '亲爱的用户,请点击以下链接进行账号密码的修改,若您没有申请找回密码<br>';
    $suffix = '<br>该邮件来自闯先生网';
    $option['subject'] = '闯先生网用户找回密码';
    break;

  case C( 'INVITE_ORG_MAIL' ):
    //组织邀请邮件
    $prefix = '您好,很高兴您注册了闯先生网的组织或企业账号,请点击以下链接进行账号的激活<br>';
    $suffix = '<br>来自闯先生网';
    $option['subject'] = '闯先生网用户组织邀请邮件';
    break;

  case C( 'CREATE_ORG_MAIL' ):
    //创建组织通知
    $prefix = '您好,您的组织已经创建成功！';
    $suffix = '<br>来自闯先生网';
    $option['subject'] = '闯先生网组织创建通知';
    break;

  case C( 'SEND_INVITE_CODE' ):
    //发送邀请码
    //创建组织通知
    $prefix = '您好,系统已经成功为您生成下面的邀请码！';
    $suffix = '<br>来自闯先生网';
    $option['subject'] = '闯先生邀请码';
    break;

  default:
    return -1;
    break;
  }
  if ( C( "IS_SAE" )&&C( 'OPEN_SAE_MAIL' ) ) {
    $mail = new SaeMail();
    $option['from'] = C( 'MAIL_ADDRESS' );
    $option['to'] = $to;
    $option['smtp_username'] = C( 'MAIL_LOGINNAME' );
    $option['smtp_password'] = C( 'MAIL_PASSWORD' );
    $option['smtp_host'] = C( 'MAIL_SMTP' );
    $option['smtp_port'] = 25;
    $option['content_type'] = C( 'MAIL_HTML' )?'HTML' : 'TXT';
    $option['content'] = $prefix.$content.$suffix;
    $mail->setOpt( $option );
    $mail->send();
    return array( 'errno' => $mail->errno(), 'errmsg' => $mail->errmsg() );
  } else {
    import( 'Org.Util.Mail' );
    $ret = SendMail( $to, $option['subject'], $prefix.$content.$suffix, C( 'MAIL_ADDRESS' ) );
    return $ret;
  }
}

/**
 * 邀请
 * 生成邀请码
 * 验证邀请码（不会更改数据库）
 *
 * @author NewFuture
 * @param string  $key
 *         $key为空null时生成邀请码
 *         为string进行验证
 * @return
 *            $string 生成验证码
 *            true 验证成功
 *             null 生成失败
 *             array（false,错误信息） 验证失败
 */
function invite( $key=null ) {

  $Invitation=M( 'Invitation' );
  if ( $key==null ) {

    //生成邀请码
    //最多尝试10次
    for ( $i=0; $i < 10 ; $i++ ) {

      $key=random_string( 8 );
      $v['invitation_code']=$key;
      if ( $Invitation->add( $v ) ) {
        return $key;
      }
    }
    return null;

  }elseif ( strlen( $key )!=8 ) {
    return false;
  }else {
    //进行验证
    $result=null;
    $data=$Invitation->getByInvitationCode( $key );

    if ( !$data ) {
      $result=array( false, "验证信息不存在" );
    }else {
      return   (bool)$data['invitation_effective'];
    }
    // if ( $data['invitation_effective'] ) {
    //   //验证信息存在，更新数据库
    //   $data['invitation_effective']=0;
    //   return $Invitation->save( $data );

    // }
  }
}

/**
 * 删除邀请码
 *
 * @author NewFuture
 * @param string  邀请码
 * @return 返回操作结果
 */
function delete_invitation( $code ) {
  if ( strlen( trim( $code ) )==8 ) {
    return M( "Invitation" )->where( 'invitation_code="'.$code.'"' )
    ->setField( 'invitation_effective', 0 );
  }
}
