<?php
/**
 * Home前台公用函数库
 * ++++++++++++++++++++++++++++++++++++++++++++
 * + 函数命名命名规则： 小写和下划线
 * ++++++++++++++++++++++++++++++++++++++++++++
 * ---------------前台函数总览表--------------------
 * 获取用户id
 *  get_id( $mustLogin=true )
 *
 * 查询是否关注
 * is_focus_on($item_id)
 *
 * 关注 / 取消关注
 *  focus( $item_id ,$is_on=null)
 *
 * 用户登陆验证
 * validate_user( $user_id, $password )
 *
 * 添加/删除标签
 * link_tags( $id, $tags=null, $add=true )
 *
 * -***
 * in_list($user_id,$article_id,$type)
 *
 * -***
 * edit_verify( $article_id )
 * -------------------------------------------------------
 */


/**
 * 获取用户id
 * 默认情况在未登陆情况下会跳转至登陆界面，
 * 以后如果使用弹窗，也在此设定参数，修改一处即可
 *
 * @author NewFuture
 * @param bool    $mustLogin=true ,是否必须登录，true时，在未登陆情况下回重定向到登录页面
 * @return $id 用户id ，0表示未登录
 */
function get_id( $mustLogin=true ) {
	$id=session( "user_id" );
	if (  $id>0 ) {
		return $id;
	}
	$error='会话已过期，请重新登录...';
	$id=cookie( "uid" );
	$password=cookie( "upasswd" );
	$result=validate_user( $id, $password );
	if ( $result===true ) {
		//cookie登陆
		//更新登录时间
		$User=M( "user" );
		$User->where( "user_id=".$id )->setField( "user_last_time", date( "Y-m-d H:i:s" ) );
		//session
		session( 'user_id',  $id );
		return $id;
	}
	else {$error=$result;}

	if ( $mustLogin )//异步不跳转
		redirect(U('User/Log'), 1, $error );
	return 0;
}

/**
 * 用户登录验证
 *
 * @author NewFuture
 * @param int     $user_id;用户名
 * @param string  $password          密码
 * @return mixed[bool/string]
 * -true 验证成功
 * -$error 验证错误信息
 */
function validate_user( $user_id, $password ) {

	$error=null;
	if ( $user_id>C( "MIN_USER_ID" ) ) {
		$User=M( "User" );
		$userinfo=$User->where( "user_id=".$user_id )->find();
		$passwd=encryption( $password , $user_id );
		if ( $userinfo["user_passwd"]===$passwd ) {
			//密码一致
			if ( $userinfo["user_effective"] ) {
				//有效
				if ( ac( $userinfo["user_type"], 1 ) ) {
					//类型处于非待验证阶段
					//session( 'user_id', $user_id );
					return true;
				}else {
					$error="尚处于验证期，请查看邮箱！";
				}
			}else {
				$error="您的账号已封！";
			}

		}else {
			$error="密码不符合！";
		}
	}else {
		$error="无效id";
	}
	return $error;
}

/**
 * 关注或取消关注
 * 根据目标id自动判断,user，标签，文章，
 *
 * @author NewFuture
 *
 * @param int     $item_id 关注对象id 限用户，文章，标签
 * @param null/bool $is_on   关注/取消
 *  null 自动切换
 *  true 强制关注
 *  false 取消关注
 *
 * @return array (bool,$result) ,$info;是否成功和对应操作
 *
 * 成功返回
 *   （true，1,最新关注数目），关注成功
 *   （true,-1，最新关注数目) ,取消关注成功
 * 失败返回
 *    (false ,0)未登录
 *   （false，错误原因)
 *
 * $info[0] 是否成功
 * $info[1] 消息，判断是否登陆$info[1]==0;
 *
 *@version 2.0 文章类型不做判断
 */
function focus( $item_id, $is_on=null ) {

	$user_id=get_id( false );
	$error=null;
	if ( $user_id<C( "MIN_USER_ID" ) ) {
		$error=0;
		return array(false ,$error);
	}else {
		$table_name=null;//关注表名
		$id_name=null;//关注对象字段名

		//关注数目统计
		$item_table=null;//对象所在的主表
		$item_name=null;
		$focus_num_name=null;//关注字段记录的关注数目名
		switch ( get_id_type( $item_id ) ) {

		case 'user':
			//用户
			$item_table="User";
			$item_name="user_id";
			$focus_num_name="user_focus_number";

			if ( $user_id==$item_id ) {
				$error="自己不能关注自己";
			}else {

				$table_name= 'focus_on_user';
				$id_name="user_id_focused";
			}
			break;

		case 'article':
			//文章
			$item_table="Article";
			$focus_num_name="article_focus_number";
			$item_name="article_id";

			if ( $is_on ) {
				$article=M( "Article" )->where( "article_id=".$item_id )
				->Field( "user_id,article_type,article_effective" )
				->find();
				if ( !$article["article_effective"] ) {
					$error!="该内容不存在！";

				}elseif ( $article["user_id"]==$user_id ) {
					$error="不能关注自己发布的内容";

				} 
				//else {

				// 	switch ( $article["article_type"] ) {

				// 	case C( "PROJECT_TYPE" ):
				// 		//项目
				// 		$writer=M( "project" )->getFieldByArticleId( $item_id, "user_id" );
				// 		if ( !$writer ) {
				// 			$error="该项目不存在";

				// 		}elseif ( $writer==$user_id ) {
				// 			$error="不能关注自己的项目";
				// 		}
				// 		break;

				// 	case C('Policy'):
				// 		//政策
				// 		$writer=M( "Policy" )->getFieldByArticleId( $item_id, "user_id" );
				// 		if ( !$writer ) {
				// 			$error="该政策不存在";

				// 		}elseif ( $writer==$user_id ) {
				// 			$error="不能关注自己的政策";
				// 		}
				// 		case C('')

				// 		break;
				// 	}
				// }
			}
			//else
			 {
				$table_name='focus_on_article';
				$id_name="article_id";
			}
			break;

		case 'tag':
			// 标签
			$item_table="Article";
			$focus_num_name="article_focus_number";
			$item_name="article_id";

			$table_name='focus_on_tag';
			$id_name='tag_id';
			break;

		default:
			$error="未知关注类型";
		}
	}

	if ( $error ) {
		return array( false, "操作失败：".$error );
	}

	$Focus_Table=M( $table_name );
	$focus_data=array('user_id'=>$user_id,$id_name=>$item_id,);

	//自动先尝试关注后取消
	if ( !$is_on ) {
		//取消关注,尝试取消
		//删除
		$result=$Focus_Table->where( $focus_data )->delete();
		if ( $result ) {
			//取消成功
			//对象关注数-1

		 M( $item_table )->where( array( $item_name =>$item_id ) )->setDec( $focus_num_name );
		 $NUM=M($item_table )->where( array($item_name =>$item_id))->field($focus_num_name)->find();
			return array( true, -1,$NUM[$focus_num_name] );
		}elseif ( $is_on===false ) {
			return array( false, "取消关注失败" );;
		}
	}

	if ( ( $is_on===null ) ||( $is_on===true ) ) {
		//关注
		$result=$Focus_Table->add( $focus_data );
		if ( $result ) {
			//关注成功
			//关注数+1
			M( $item_table )->where( array( $item_name =>$item_id ) )->setInc( $focus_num_name );
			$NUM = M( $item_table )->where( array( $item_name =>$item_id ) )->field($focus_num_name)->find();
			return array( true, 1 ,$NUM[$focus_num_name]);
		}elseif ( $is_on ) {
			return array( false, "关注失败" );;
		}
	}

	return array( false, "操作失败!" );

}

/**
 * 查询是否已经关注
 *
 * @author Future
 * @param int     $item_id查询目标id
 * @return bool or null;
 * null 用户未登录或自己关注自己
 * true 已经关注
 * false 未关注
 */
function is_focus_on( $item_id ) {

	$id=get_id( false );
	if ( $id< 0 ||$id==$item_id ) {
		return null;
	}

	switch ( get_id_type( $item_id ) ) {
	case 'user':
		//用户
		$table_name= 'focus_on_user';
		$id_name="user_id_focused";
		break;

	case 'article':
		//文章
		$table_name='focus_on_article';
		$id_name="article_id";
		break;

	case 'tag':
		// 标签
		$table_name='focus_on_tag';
		$id_name='tag_id';
		break;

	default:
		return false;
	}

	$Focus_Table=M( $table_name );
	$focus_data[$id_name]=$item_id;
	$result=$Focus_Table->where( $focus_data )->find();
	return (bool)$result;
}

/**
 * 添加/删除 或者 关注/取消标签
 *
 * @author Future
 * @param int     $id       用户id，文章id，圈子id
 * @param string  /array $tags     所有的标签字符串或者数组
 * @param bool    $add=true ,添加或者删除
 * [true 默认 添加]
 * [false 删除标签关联]
 * @return  mixed 出错返回错误信息，否则默默完成
 */
function link_tags( $id, $tags=null, $add=true ) {

	if ( empty( $tags ) ) {
		return "空标签无效";
	}

	$type=get_id_type( $id );

	$table=null;
	switch ( $type ) {
	case 'article':
		$table='article_have_tag';
		$id_name='article_id';
		break;

	case 'user':
		$table="focus_on_tag";
		$id_name="user_id";
		break;

	case 'circle':
		$table='circle_have_tag';
		$id_name='circle_id';
		break;
	default:
		return "无效id";
		break;
	}

	if ( !is_array( $tags ) ) {
		//tags字符串转成数组
		$tags=str_replace( '　', ' ', $tags );//替换全角空格为半角
		$tags=explode( " ", $tags );//切割字符串
		$tags=array_filter( $tags );//去掉空字符
		$Table=M( $table );
	}
	//依次处理各个标签
	foreach ( $tags as $key => $value ) {

		//获取tag id
		$Tag=M( "Tag" );
		$tag_id=$Tag->getFieldByTagTitle( $value, "tag_id" );
		if ( $tag_id<C( "MIN_TAG_ID" ) ) {
			//该标签尚未建立
			if ( $add ) {
				if ( $tag_id=$Tag->add( array( "tag_title"=>$value ) ) ) {
					//创建标签失败
					break;
				}
			}else {
				//删除没有的标签直接忽略
				break;
			}
		}

		//建立关联信息
		$tag_info[$id_name]=$id;
		$tag_info['tag_id']=$tag_id;
		//添加到关联表，数据库触发器自动计数
		if ( $add ) {
			//添加标签
			$Table->add( $tag_info );
		}else {
			//删除标签
			$Table->where( $tag_info )->delete();
		}

	}
}

/**
 *
 *
 * @author 茜茜
 */
function in_list( $user_id, $article_id, $type ) {
	switch ( $type ) {
	case 'up':
		if ( M( 'up_article' )
			->where( "user_id=$user_id and article_id=$article_id" )
			->count()==0 )
			return false;
		else
			return true;
		break;
	case 'down':
		if ( M( 'down_article' )
			->where( "user_id=$user_id and article_id=$article_id" )
			->count()==0 )
			return false;
		else
			return true;

		break;
	case 'focus':
		if ( M( 'focus_on_article' )
			->where( "user_id=$user_id and article_id=$article_id" )
			->count()==0 )
			return false;
		else
			return true;

		break;
	case 'collect':
		if ( M( 'collect_article' )
			->where( "user_id=$user_id and article_id=$article_id" )
			->count()==0 )
			return false;
		else
			return true;

		break;
	}
}

/**
 *
 *
 * @author 茜茜
 */
function edit_verify( $article_id ) {
	$curr_user_id = get_id( false );
	$article = M( 'article' )
	->where( "article_id = $article_id" )
	->find();
	$article_type = $article['article_type'];
	switch ( $article_type ) {
	case C( "POLICY_TYPE" ):
		$policy=M( 'policy' )
		->where( "article_id = $article_id" )
		->find();
		$user_id = $policy['user_id'];
		$result = M( 'belong_to_organization' )
		->where( "organization_user_id = $user_id and user_id = $curr_user_id" )
		->find();
		if ( $result || $curr_user_id == $user_id || $curr_user_id == $article['user_id'] )
			return true;
		else
			return false;
		break;
	case C( "PROJECT_TYPE" ):
		$project=M( 'project' )
		->where( "article_id = $article_id" )
		->find();
		$user_id = $project['user_id'];
		$result = M( 'belong_to_organization' )
		->where( "organization_user_id = $user_id and user_id = $curr_user_id" )
		->find();
		if ( $result || $curr_user_id == $user_id || $curr_user_id == $article['user_id'] )
			return true;
		else
			return false;
		break;
	case C( "QUESTION_TYPE" ):
		$user_id = $article['user_id'];
		if ( $curr_user_id == $user_id )
			return true;
		else
			return false;
		break;
	default:
		break;
	}
}
