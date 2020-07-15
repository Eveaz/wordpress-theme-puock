<?php

include ('inc/fun/core.php');

$puock_colors_name = ['primary','danger','info','success','warning','dark','secondary'];

//去除感谢使用wordpress创作
if(pk_is_checked('hide_footer_wp_t')){
    function my_admin_footer_text(){ return ''; }
    function my_update_footer() { return ''; }
    function annointed_admin_bar_remove() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }
    add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0);
    add_filter( 'admin_footer_text', 'my_admin_footer_text', 10 );
    add_filter( 'update_footer', 'my_update_footer', 50 );
}

//禁用5.0编辑器
if(pk_is_checked('stop5x_editor')){
    add_filter('use_block_editor_for_post', '__return_false');
    remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
}

//获取评论等级
function pk_the_author_class_out($count){
    if($count<=0){
        return '';
    }
    switch ($count){
        case $count>=1 && $count<20:$level=1;break;
        case $count>=20 && $count<40:$level=2;break;
        case $count>=40 && $count<60:$level=3;break;
        case $count>=60 && $count<80:$level=4;break;
        case $count>=120 && $count<120:$level=5;break;
        case $count>=140 && $count<140:$level=6;break;
        case $count>=160 && $count<160:$level=7;break;
        default:return '';
    }
    return '<span class="t-sm c-sub"><i class="czs-diamond-l mr-1"></i>评论达人LV.'.$level.'</span>';
}

function pk_the_author_class($echo=true,$in_comment=null){
    global $wpdb,$comment;
    if(!$comment){
        $comment = $in_comment;
    }
    if ($comment->user_id == '1') {
        $res = '<span class="t-sm text-danger"><i class="czs-diamond-l mr-1"></i>博主</span>';
    }
    else{
        $comment_author_email= $comment->comment_author_email;
        $author_count = count($wpdb->get_results(
          "SELECT comment_ID as author_count FROM $wpdb->comments WHERE comment_author_email = '$comment_author_email' "));
        $res = pk_the_author_class_out($author_count);
    }
    if(!$echo){
        return $res;
    }
    echo $res;
}

//获取Gravatar头像
function pk_get_gravatar($email,$echo=true){
    $link = get_avatar_url($email);
    if(!$echo){
        return $link;
    }
    echo $link;
}

//获取文章分类链接
function get_post_category_link($class='',$icon='',$cid=null,$default='无分类',$index=0){
    $cats = get_the_category();
    $cat = null;
    if($cid!=null){
        $cat = get_category($cid);
    }else if(count($cats)>0){
        $cat = $cats[0];
    }
    if($cat){
        return '<a class="'.$class.'" href="'.get_category_link($cat).'">'.$icon.$cat->name.'</a>';
    }else{
        return '<a class="'.$class.'" href="javascript:void(0)">'.$icon.$default.'</a>';
    }

}
//获取文章标签
function get_post_tags($class=''){
    global $puock_colors_name;
    $tags = get_the_tags();
    $out = '';
    if($tags && count($tags)>0){
        $out .= '<div class="'.$class.'">';
        foreach ($tags as $tag){
            $color_index = mt_rand(0,count($puock_colors_name)-1);
            $out .= '<a href="'.get_tag_link($tag).'" class="ahfff curp mr-1 badge badge-'.$puock_colors_name[$color_index].'"># '.$tag->name.'</a>';
        }
        $out .= '</div>';
    }else{
        //
    }
    return $out;
}

function pk_get_post_date(){
    $time = get_post_time();
    $c_time = time() - $time;
    $day = 86400;
    switch ($c_time){
        case $c_time<$day:$res='近一天内';break;
        case $c_time<($day * 2):$res='近两天内';break;
        case $c_time<($day * 3):$res='近三天内';break;
        case $c_time<($day * 4):$res='四天前';break;
        case $c_time<($day * 5):$res='五天前';break;
        case $c_time<($day * 6):$res='六天前';break;
        default:$res=date('Y-m-d',$time);
    }
    echo $res;
}

//获取随机的bootstrap的颜色表示
function pk_get_color_tag($ex=array()){
    global $puock_colors_name;
    while(true){
        $c = $puock_colors_name[mt_rand(0,count($puock_colors_name)-1)];
        if(!in_array($c,$ex)){
            return $c;
        }
    };
}


function get_smiley_codes(){
    return array(":?:"=>"疑问",":razz:"=>"调皮",":sad:"=>"难过",":evil:"=>"抠鼻",":naughty:"=>"顽皮",
        ":!:"=>"吓",":smile:"=>"微笑",":oops:"=>"憨笑",":neutral:"=>"亲亲",":cry:"=>"大哭",":mrgreen:"=>"呲牙",
        ":grin:"=>"坏笑",":eek:"=>"惊讶",":shock:"=>"发呆",":???:"=>"撇嘴",":cool:"=>"酷",":lol:"=>"偷笑",
        ":mad:"=>"咒骂",":twisted:"=>"发怒",":roll:"=>"白眼",":wink:"=>"鼓掌",":idea:"=>"想法",":despise:"=>"蔑视",
        ":celebrate:"=>"庆祝",":watermelon:"=>"西瓜",":xmas:"=>"圣诞",":warn:"=>"警告",":rainbow:"=>"彩虹",
        ":loveyou:"=>"爱你",":love:"=>"爱",":beer:"=>"啤酒",
    );
}

function get_smiley_image($key){
    $imgKey = mb_substr($key,1,mb_strlen($key)-2);
    if($imgKey=='?'){
        $imgKey = 'doubt';
    }
    if($imgKey=='!'){
        $imgKey = 'scare';
    }
    if($imgKey=='???'){
        $imgKey = 'bz';
    }
    return $imgKey;
}

function custom_smilies_src( $old, $img ) {
    return get_stylesheet_directory_uri().'/assets/img/smiley/'.$img;
}
add_filter( 'smilies_src' , 'custom_smilies_src' , 10 , 2 );

function puock_twemoji_smiley(){
    global $wpsmiliestrans;
    if ( !get_option( 'use_smilies' ) )
        return;
    $wpsmiliestrans = array();
    foreach (get_smiley_codes() as $key=>$val){
        $wpsmiliestrans[$key]=get_smiley_image($key).'.png';
    }
    return $wpsmiliestrans;
}
add_action('init', 'puock_twemoji_smiley', 3);

function get_wpsmiliestrans(){
    global $wpsmiliestrans,$output;
    $wpsmilies = array_unique($wpsmiliestrans);
    foreach($wpsmilies as $alt => $src_path){
        $output .= '<a class="add-smily" data-smilies="'.$alt.'"><img class="wp-smiley" src="'.get_bloginfo('template_directory').'/assets/img/smiley/'.rtrim($src_path, "png").'png" /></a>';
    }
    return $output;
}

add_action('media_buttons_context', 'smilies_custom_button');
function smilies_custom_button($context) {
    $context .= '<style>.smilies-wrap{background:#fff;border: 1px solid #ccc;
    box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.24);padding: 10px;position: absolute;top: 60px;
    width: 380px;display:none}.smilies-wrap img{height:24px;width:24px;cursor:pointer;margin-bottom:5px}
     .is-active.smilies-wrap{display:block}</style>
    <a id="insert-media-button" style="position:relative" class="button insert-smilies add_smilies" 
    title="添加表情" data-editor="content" href="javascript:;">  
    <span>添加表情</span>  
    </a><div class="smilies-wrap">'. get_wpsmiliestrans() .'</div>
    <script>jQuery(document).ready(function(){jQuery(document).on("click", ".insert-smilies",function()
     { if(jQuery(".smilies-wrap").hasClass("is-active")){
         jQuery(".smilies-wrap").removeClass("is-active");}else{jQuery(".smilies-wrap").addClass("is-active");}}
         );jQuery(document).on("click", ".add-smily",function() { send_to_editor(" " 
         + jQuery(this).data("smilies") + " ");jQuery(".smilies-wrap").removeClass("is-active");return false;})
         ;});</script>';
    return $context;
}

function get_post_images($post_id=null){
    global $post;
    if($post_id==null && $post){
        $content = $post->post_content;
    }else{
        $content = get_post($post_id)->post_content;
    }
    preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
    if($matches && $matches[1]){
        $res = $matches[1][0];
    }else{
        $res = get_stylesheet_directory_uri().'/assets/img/random/'.mt_rand(1, 8).'.jpg';
    }
    return $res;
}

//分页功能
if (!function_exists('pk_paging')){
    function pk_paging($pnum=2,$position='right') {
        if (is_singular()) {
            return;
        };
        global $wp_query, $paged;
        $max_page = $wp_query->max_num_pages;
        if ($max_page == 1) return;
        echo '<div class="mt20 clearfix"><ul class="pagination float-'.$position.'">';
        if (empty($paged)) $paged = 1;
        echo '<li class="prev-page puock-bg">';
        previous_posts_link('&laquo;');
        echo '</li>';
        if ($paged > $pnum + 1) page_link(1);
        if ($paged > $pnum + 2) echo "<li><a href='javascript:void(0)'>...</a></li>";
        for ($i = $paged - $pnum; $i <= $paged + $pnum; $i++) {
            if ($i > 0 && $i <= $max_page) {
                if($i == $paged){
                    echo "<li ><a class='cur'>{$i}</a></li>";
                }else{
                    page_link($i);
                }
            }
        }
        if ($paged < $max_page - $pnum - 1) echo "<li><a href='javascript:void(0)'>...</a></li>";
        echo '<li class="next-page">';
        next_posts_link('&raquo;');
        echo '</li>';
        echo '</ul></div>';
    }
    function page_link($i, $title = '') {
        echo "<li><a href='", esc_html(get_pagenum_link($i)) , "'>{$i}</a></li>";
    }
}

//获取面包屑导航
function pk_breadcrumbs() {
    global $cat,$other_page_title;
    $out = '<div id="breadcrumb" class="p-block '.(pk_open_box_animated('animated fadeInUp',false)).'">';
    $out .= '<nav aria-label="breadcrumb">';
    $out .= '<ol class="breadcrumb">';
    $out .= '<li class="breadcrumb-item"><a class="a-link" href="'.home_url().'">首页</a></li>';
    if(is_single() || is_category()){
        $categorys = get_the_category();
        if(count($categorys)<=0 && is_single()){
            return false;
        }
        $category = $categorys[0];
        if($category==null && is_category()){
            $category = get_category($cat);
        }
        $cats = get_category_parents($category->term_id, true,'');
        $cats = str_replace("<a",'<li class="breadcrumb-item"><a class="a-link"',$cats);
        $cats = str_replace("</a>",'</a></li>',$cats);
        $out .= $cats;
        if(is_single()){
            $out .= '<li class="breadcrumb-item active " aria-current="page">正文</li>';
        }else if(is_category()){
            $out .= '<li class="breadcrumb-item active " aria-current="page">文章列表</li>';
        }
    }else if(is_search()){
        $out .= '<li class="breadcrumb-item active " aria-current="page">'.($_GET['s']).'</li>';
        $out .= '<li class="breadcrumb-item active " aria-current="page">搜索结果</li>';
    }else if(is_author()){
        $out .= '<li class="breadcrumb-item active " aria-current="page">'.get_the_author_meta('nickname').'的文章列表</li>';
    }else if(is_page()){
        global $post;
        $out .= '<li class="breadcrumb-item active " aria-current="page">'.($post->post_title).'</li>';
    }else if(is_tag()){
        $tag_name = single_tag_title('',false);
        $out .= '<li class="breadcrumb-item active " aria-current="page">标签</li>';
        $out .= '<li class="breadcrumb-item active " aria-current="page">'.($tag_name).'</li>';
    }else if(is_404()){
        $out .= '<li class="breadcrumb-item active " aria-current="page">你访问的资源不存在</li>';
    }else if(isset($other_page_title)){
        $out .= '<li class="breadcrumb-item active " aria-current="page">'.$other_page_title.'</li>';
    }
    $out .= '</div></nav></ol>';
    return $out;
}

//获取阅读数量
function pk_get_post_views(){
    if(function_exists('the_views')) {
        echo the_views();
    }else{
        echo 0;
    }
}

//字数统计
function count_words ($text='') {
    global $post;
    $text == '' ? $text = $post->post_content : null;
    $text = $post->post_content;
    return mb_strlen($text, 'UTF-8');
}

//给文章内容添加灯箱
function light_box_text_replace ($content) {
    $pattern = "/<a(.*?)href=('|\")([A-Za-z0-9\/_\.\~\:-]*?)(\.bmp|\.gif|\.jpg|\.jpeg|\.png)('|\")([^\>]*?)>/i";
    $replacement = '<a$1href=$2$3$4$5$6 class="fancybox" data-no-instant target="_blank">';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}
add_filter('the_content', 'light_box_text_replace', 99);


//给图片加上alt/title
function content_img_add_alt_title($content){
    global $post;
    preg_match_all('/<img (.*?)\/>/', $content, $images);
    if(!is_null($images)) {
        foreach($images[1] as $index => $value){
            $new_img = str_replace('<img', '<img title='.$post->post_title.'
             alt='.$post->post_title, $images[0][$index]);
        $content = str_replace($images[0][$index], $new_img, $content);}
    }
    return $content;
}
add_filter('the_content', 'content_img_add_alt_title', 99);

//加上bootstrap的表格class
function pk_bootstrap_table_class($content){
    global $post;
    preg_match_all('/<table.*?>[\s\S]*<\/table>/', $content, $tables);
    if(!is_null($tables)) {
        foreach($tables[0] as $index => $value){
            $out = str_replace('<table', '<table class="table table-bordered puock-text"', $tables[0][$index]);
            $content = str_replace($tables[0][$index], $out, $content);}
    }
    return $content;
}
add_filter('the_content', 'pk_bootstrap_table_class', 99);

require_once dirname(__FILE__).'/fun-custom.php';