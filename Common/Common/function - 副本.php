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
 * 密码加密 unfinished
 *  encryption( $p, $i )
 *
 * 生成N位随机字符串
 *  random_string( $n=8 )
 * ---------------------------------------------
 */

/**
 * 单个文件上传
 * 上传文件的根目录/Uploads/
 *
 * @author NewFuture
 * 
 * @param string  $savePath 相对于/Uploads/保存路径,
 * @param string  $savename 保存文件名，文件后缀不变
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
  $upload->rootPath  = '/Uploads/';
  $upload->savePath  = $savePath; // 设置附件上传目录
  $upload->saveName = $saveName;
  $upload->autoSub =false;//不创建子目录
  $upload->replace =ture;//同名覆盖
  // 上传单个文件
  $info   =   $upload->uploadOne( $_FILES[$postName] );
  if ( !$info ) {
    // 上传错误
    return null;
  }else {
    // 上传成功 获取上传文件信息
    return "/Uploads/".$info['savepath'].$info['savename'];
  }
}

/**
 * unfinished
 * 通过id获取url（相对于站点根目录的地址）
 * 根据具id自动判断类型便于垮模块使用
 * 对于帖子下的回复应该还有页内定位(#id)
 *
 * @author NewFuture
 * @param int     $id 要访问目标的id
 * @return string $url 对应得链接地址，
 * 如果不存在返回null
 */
function get_url_by_id( $id ) {
  $url=null;
  $home_url="/index.php/Home/";

  switch ( get_id_type( $id ) ) {
    //类型判断
  case 'user':
    //用户
    $url=$home_url."User/info/id/".$id;
    break;
  case 'article':
    //文章.
    $Article=M( "artcile" );

    $type=$Article->getFieldByArticle_Id( $id, "article_type" );

    switch ( $type ) {
    case C( "PROJECT_TYPE" ):
      //项目
      $url=$home_url."Project/Detail/aid/".$id;
      break;
    case C( "POLICY_TYPE" ):
      //政策
      $url=$home_url."Policy/Detail/aid/".$id;
      break;
    case C( "QUESTION_TYPE" ):
      //问题
      $url=$home_url."Question/Detail/aid/".$id;
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
    //回复评论 附加页内定位

    break;
  case 'circle':
    //圈子
    $url=$home_url."Circle/".$id;
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
 * @return mixed 是否验证成功fa 请使用$result===true而不是==判断返回结果
 * sample
 * $key=validate(1234)为1234生成验证信息 返回随机验证字符串$key
 * $result= validate（12345，"ABCDSUNM")12345的随机密码是否匹配
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
      }
      elseif ( $key=== $data['validate_key'] ) {
        //通过验证
        $result=true;
      }
      else {
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
    return "user";
  }elseif ( $id>C( "MIN_ARTICLE_ID" )&&$id<C( "MAX_ARTICLE_ID" ) ) {
    return "article";
  } elseif ( $id>C( "MIN_COMMONT_ID" )&&$id<C( "MAX_COMMONT_ID" ) ) {
    return "comment";
  }  elseif ( $id>C( "MIN_TAG_ID" )&&$id<C( "MAX_TAG_ID" ) ) {
    return "tag";
  } elseif ( $id>C( "MIN_CIRCLE_ID" )&&$id<C( "MAX_NOTCIE_ID" ) ) {
    return "circle";
  }  elseif ( $id>C( "MIN_NOTICE_ID" )&&$id<C( "MAX_NOTCIE_ID" ) ) {
    return "notice";
  }elseif ( $id>C( "MIN_MESSAGE_ID" )&&$id<C( "MAX_MESSAGE_ID" ) ) {
    return "message";
  }elseif ( $id>C( "MIN_ADMIN_ID" )&&$id<C( "MAX_ADMIN_ID" )  ) {
    return "admin";
  } else {
    return null;
  }

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
 * 暂不写
 *
 * @author NewFuture
 */
function encryption( $p, $i ) {
  return $p;
}


/**
 * 生成n位随机字符串
 *
 * @author NewFuture Copy form http://www.oschina.net/code/snippet_4873_4493回复mark35
 */
function random_string( $n=16 ) {
  return substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, $n );
}
