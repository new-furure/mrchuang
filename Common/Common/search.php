<?php
/* 搜索用函数,包括:
 * search_user()                  搜索用户
 * search_circle()                按照circle_id搜索圈子
 * search_article()               按照标题搜索文章
 */
/*
 *返回值: 
 *-1 没有可搜索的词
 *-2 没有可搜索的cate
 *-3 搜索tag的时候出错
 *-4 根据tagid搜索userid的时候出错
*/
function search_user($kw='', $type=1, $cat=1 , $field=3, $exact=1){
    $kw = strtolower($kw);
    $kw = str_replace('　', ' ', $kw);
    $kwarray = explode(" ", $kw);
    $kwarray = array_filter($kwarray);
    $kwarray = array_unique($kwarray);
    if(count($kwarray)==0) return -1; //ERROR:No word to search.

    $condition = array();
    foreach ($kwarray as $word){
        if($exact) array_push($condition, array('eq', "$word"));
        else array_push($condition, array('like', "%$word%"));}
    array_push($condition, 'or');

    if($cat){
        $catcondition = array();
        $CONFIGS = array( "NO_ORG"    ,
            "STARTUP"   , "ENTERPRISE",
            "VC"        , "INCUBATOR" ,
            "INVITEE"   , "GOVERNMENT", );
        foreach($CONFIGS as $config){
            if($cat & C("SEARCH_USER.CATEGORY_$config")){
                if($type & C("SEARCH_USER.TYPE_PERSON")){
                    array_push($catcondition, array('eq', (C($config)<<2) | C("PERSON_ACTIVE") )); }
                if($type & C("SEARCH_USER.TYPE_ORGANIZATION")){
                    array_push($catcondition, array('eq', (C($config)<<2) | C("ORG_ACTIVE")    )); } } }
        array_push($catcondition, 'or');
        $map['user_type'] = $catcondition; }
    else{
        return -2;}

    if($field & C("SEARCH_USER.FIELD_TAG")){ //如果需要按照tag来搜的话,则单独进行处理 <-- 真是丑陋的一块代码
        $Tag = M('Tag');
        $tagcondition = array();
        foreach($kwarray as $word){
            if($exact) array_push($tagcondition, array('eq', "$word"));
            else array_push($tagcondition, array('like', "%$word%"));}
        array_push($tagcondition, 'or');
        $tagids = $Tag->field('tag_id')->where(array("tag_title" => $tagcondition))->select();
        if($tagids == false) return -3;
        //-------得到了相关tag的id们,接下来找关注了这些tag的user的id们-----------------------
        $FocusOnTag = M('FocusOnTag');
        $tagcondition = array(); // re-initializastion
        foreach ($tagids as $id) {
            array_push($tagcondition, array('eq', $id['tag_id'])); }
        array_push($tagcondition, 'or');
        $userids = $FocusOnTag->field('user_id')->where(array('tag_id' => $tagcondition))->select();
        if($userids == false) return -4;
        //------得到了user_id们,然后就是据此得到condition安排好$map就行---------------------
        $tagcondition = array(); // re-initializastion
        foreach ($userids as $id) {
            array_push($tagcondition, array('eq', $id['user_id'])); }
        array_push($tagcondition, 'or');
        $map_or['user_id'] = $tagcondition; }
    if($field & C("SEARCH_USER.FIELD_NICKNAME")) {$map_or['user_nickname'] = $condition;}
    if($field & C("SEARCH_USER.FIELD_PROFILE"))  {$map_or['user_profile']  = $condition;}
    $map_or['_logic'] = 'OR';
    $map['_complex'] = $map_or;
    $User = M('User');
    $result = $User->where($map)->select();
    // return $map; 
    // return $User->getlastSql();
    return $result;
}

function search_article($kw='', $type=0, $field=0, $order=0, $exact=1){
    if($type==0 || $field==0) return false;
    $kw = strtolower($kw);
    $kw = str_replace('　', ' ', $kw);
    $kwarray = explode(" ", $kw);
    $kwarray = array_filter($kwarray);
    $kwarray = array_unique($kwarray);
    if(count($kwarray)==0) return -1; //ERROR:No word to search.

    $condition = array();
    foreach ($kwarray as $word){
        if($exact) array_push($condition, array('eq', "$word"));
        else array_push($condition, array('like', "%$word%"));}
    array_push($condition, 'or');

    // Process field.
    if($field & C("SEARCH_ARTICLE.FIELD_TITLE"  )){$map_field['article_title']=$condition;}
    if($field & C("SEARCH_ARTICLE.FIELD_PROFILE")){$map_field['article_profile']=$condition;}
    if($field & C("SEARCH_ARTICLE.FIELD_TAG"    )){
        $tagcondition = array();
        foreach ($kwarray as $word){
            if($exact) array_push($tagcondition, array('like', "% $word %"));
            else array_push($tagcondition, array('like', "%$word%"));}
        array_push($tagcondition, 'or');
        $map_field['article_tags']=$tagcondition;
    }
    $map_field['_logic'] = 'or';

    // Process type.
    $typecondition = array();
    if($type & C("SEARCH_ARTICLE.TYPE_PROJECT" )){
        array_push($typecondition,array('eq', C('PROJECT_TYPE' )) );}
    if($type & C("SEARCH_ARTICLE.TYPE_QUESTION")){
        array_push($typecondition,array('eq', C('QUESTION_TYPE')) );}
    if($type & C("SEARCH_ARTICLE.TYPE_POLICY"  )){
        array_push($typecondition,array('eq', C('POLICY_TYPE'  )) );}
    array_push($typecondition, 'or');
    $map['article_type'] = $typecondition;

    $map['article_effective'] = 1;
    $map['article_draft']     = 0;
    $map['_complex'] = $map_field;

    switch ($order) {
        case 0: $orderstring = 'article_time desc'; break;
        case 1: $orderstring = 'article_hits desc'; break;
        case 2: $orderstring = 'article_up_number desc'; break;
        case 3: $orderstring = 'article_comment_number desc'; break;
        default: return -2; break;
    }

    $Article = M('Article');
    $result = $Article->where($map)->order($orderstring)->select();
    return $result;
}

function search_tag($kw='', $exact='1'){
//返回一维数组,其中存放着搜索结果tag数据信息
    $Tag = M('Tag');
    $kw = str_replace('　', ' ', $kw);
    $kwarray = explode(" ", $kw);
    $kwarray = array_filter($kwarray);
    $kwarray = array_unique($kwarray);
    if(count($kwarray)==0) return -1;
    $kw = $kwarray[0];
    if($exact){
        $map['tag_title'] = $kw;}
    else{
        $map['tag_title'] = array('like', "%$kw%");}
    $result = $Tag->where($map)->select();
    return $result;
}

function search_circle($circle_id=''){
    $Circle = M('Circle');
    if($circle_id!=''){
        $map['circle_id'] = intval($circle_id);
        return $Circle->where($map)->find();
    }
    else{
        return -1;
    }
}