<?php 
namespace app\common\validate;

class ArticleCommentValidate extends \think\Validate
{
	protected $rule = [       
		"article_id" => "number|max:11",
		"parent_id" => "number|max:11",
		"content" => "chsDash|max:300",		 
		"comment_user" => "", 	 
		"commented_user" => "",	 
		"is_del" => "",	 
		"create_time" => "", 
		"update_time" => "",
    ];
}