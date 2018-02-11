逸居联盟后台系统
===============

基于ThinkPHP5开发
开发规范

## 接口规范Restful Api
*	basecontroller
*	GET api.sinoyjlm.com/news/1  --------------[模块]／news/read
*	GET api.sinoyjlm.com/news -----------------[模块]/news/index
*	配置路由
*		方法1：Route::resource('blog','index/blog');
*		方法2：路由配置文件中使用__rest__添加资源路由定义
*			return [
*			    // 定义资源路由
*			    '__rest__'=>[
*			        // 指向index模块的blog控制器
*			        'blog'=>'index/blog',
*			    ],
*			    // 定义普通路由
*			    'hello/:id'=>'index/hello',
*			]
*	设置后会自动注册7个路由规则，如下：
*	
*	标识	请求类型	生成路由规则	对应操作方法（默认）
*	index	GET			blog			index
*	create	GET			blog/create		create
*	save	POST		blog			save
*	read	GET			blog/:id		read
*	edit	GET			blog/:id/edit	edit
*	update	PUT			blog/:id		update
*	delete	DELETE		blog/:id		delete
*	使用举例
*	http://serverName/blog/
*	http://serverName/blog/128
*	http://serverName/blog/28/edit
*	详见 thinkPHP完全开发手册 ---- 资源路由



## Mysql 开发规范
*	数据库名yj前缀，中间是模块名，最后是功能名称
*	数据库名必须添加注释
*	表名必须添加注释
*	字段名必须添加注释
*	所有字段都不能为null，数字默认0 ，字符串默认为 ‘’ 空字符串（点一下空格）
*	字段名多个字母一律小写加下划线分隔

## 继承规范
*	应用模块model必须继承common模块的baseModel
*	应用模块controller必须继承common模块的baseController,baseController继承中间件控制器middle
*	应用模块service必须继承common模块的baseService

## php开发规范
*	循环结构内（for，foreach，递归）一律不能含有数据库操作

## data，model,service,controller,开发原则
*	开发原则：一律遵循提供资源接口的原则
*	data:数据库中的数据
*	model：php层面的数据表，原则是与表一一对应
*	service：是对于controller层和model层中间层抽象的服务层，
			 从model层来看，service就是一个或多个model的不同排列组合，
			 从controller层来看，service就是为一个接口专门提供的服务，
			 所以理论上是一个，一个controller文件就会对应一个service文件，
			 controller方法就会对应一个service方法,因此service也可以算作业务逻辑层。
*	controller:对于请求的接口完全按照restful API开发规范
*	controller请求（一个方法）只允许调用本模块的一个服务，具体的业务逻辑必须在该服务内，
			 而该服务应该调用该模块内的用到的一或多个model来进行业务处理，而该模块内的model必须继承common
			 内的对应表的model，至于该表与那个表进行关联这一方面，该关联如果是mysql数据表固定的关联，
			 关联表的规则应该写在common里面，这也就意味着common中的关联只能关联一个最直接的表，
			 如果业务逻辑涉及到特殊的关联，那么该关联方法就只能写在该模块的model中。

## 接口
*   如何设计好接口，对于一个好的接口的定义，一定是对于产品功能的实现是非常友好的。这也就意味着对于一个前端来说，
*   使用自己英语水平内可以理解的url就可以轻松的操作后台资源，才能是一个好的接口。
	所以由此可以得出一个设计逻辑图（上层决定下层）

	

		需求（客户）
		 |
	产品以及功能（产品经理）
		 |
  前端页面调接口（web）
	     |
（以下开始为php部分）
		 |
	url映射层（专门为前端设计url友好型接口） 
	     |
	url接口（控制器的设计）——> 现在是根据多表组成的一个模块制定的控制器模块
	  	 |					  （所以上边好像还少一层，就是url映射层，这里真正的控制器是对资源的调用，
	  	 |                     所以这个资源是留给php程序员看的）
	     |
	  service层（model层的组合，（多表组成一个模块）也就是业务逻辑）
	     |
 	  model层（每张数据表）
	     |
	  data I/O层（由每条数据决定）


## 继承调用图
			root
			 |
	common：MiddleController									common:baseModel
			 |														|
	moduleX：XController 	 common:baseService 	      |—— moduleX:model 3
			 |							|				  | 		|
			 |—— method 1 <—— moduleX:XService::method 1 <————— moduleX:model 1
			 |							|				  |			|
			 |							|				  |—— moduleX:model 2
			 |                          |
			 |                          |
			 |—— method 2 <—— moduleX:XService::method 2 ...
			 |							|
			 |—— ...					...
			 |
## 模块信息
	1：admin授权登录模块
	2：article文章模块
	3：business模块
	4：common公共模块
	5：comser社区服务？模块
	6：goods商品模块
	7：member模块？
	8：order订单模块
	9：system系统模块
## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─common				公共目录
|  |  ├─controller		公共控制器             
|  |  ├─model 			公共model（与表一一对应，指定了该类对应的表，该表的关联表（凡是数据库相关的关联都在这里设置））            
|  |  ├─service			公共服务（model和controller的公共中间层）             
|  |  └─             
│  ├─module_name        模块目录
│  │  ├─config.php      模块配置文件
│  │  ├─common.php      模块函数文件
│  │  ├─controller      控制器目录（*建议按照业务逻辑来设置控制器，完全按照restful API规则）
|  |  ├─service			服务目录（与controller一一对应）	
│  │  ├─model           模型目录（继承common的model（主要设置一些个性化关联，比如查询的个性化过滤条件））
│  │  ├─view            视图目录
│  │  └─ ...            更多类库目录
│  │
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
│
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
├─thinkphp              框架系统目录
│  ├─lang               语言文件目录
│  ├─library            框架类库目录
│  │  ├─think           Think类库包目录
│  │  └─traits          系统Trait目录
│  │
│  ├─tpl                系统模板目录
│  ├─base.php           基础定义文件
│  ├─console.php        控制台入口文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函数文件
│  ├─phpunit.xml        phpunit配置文件
│  └─start.php          框架入口文件
│
├─extend                扩展类库目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~

> router.php用于php自带webserver支持，可用于快速测试
> 切换到public目录后，启动命令：php -S localhost:8888  router.php
> 上面的目录结构和名称是可以改变的，这取决于你的入口文件和配置参数。

## 命名规范

### 目录和文件

*   目录不强制规范，驼峰和小写+下划线模式均支持；
*   类库、函数文件统一以`.php`为后缀；
*   类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
*   类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

### 函数和类、属性命名
*   服务加后缀Service
*   类的命名采用驼峰法，并且首字母大写，例如 `User`、`UserType`，默认不需要添加后缀，例如`UserController`应该直接命名为`User`；
*   函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 `get_client_ip`；
*   方法的命名使用驼峰法，并且首字母小写，例如 `getUserName`；
*   属性的命名使用驼峰法，并且首字母小写，例如 `tableName`、`instance`；
*   以双下划线“__”打头的函数或方法作为魔法方法，例如 `__call` 和 `__autoload`；

### 常量和配置
*   常量以大写字母和下划线命名，例如 `APP_PATH`和 `THINK_PATH`；
*   配置参数以小写字母和下划线命名，例如 `url_route_on` 和`url_convert`；



### 所有的表名
 yj_activity_prize         
 yj_activity_qiangpiao     
 yj_admin                  
 yj_admin_role             
 yj_adv                    
 yj_adv_item               
 yj_app_cate               
 yj_app_noti               
 yj_article                
 yj_article_cate           
 yj_article_content        
 yj_article_photo          
 yj_block                  
 yj_block_item             
 yj_block_page             
 yj_cloud_cate             
 yj_cloud_goods            
 yj_cloud_goods_attr       
 yj_cloud_goods_photo      
 yj_cloud_number           
 yj_cloud_order            
 yj_cloud_share            
 yj_cloud_share_photo      
 yj_coupons_qiangpiao      
 yj_data_area              
 yj_data_business          
 yj_data_city              
 yj_data_province          
 yj_data_shequ             
 yj_data_street            
 yj_h_admin                
 yj_h_role                 
 yj_home_module            
 yj_hongbao                
 yj_hongbao_log            
 yj_house_attr             
 yj_house_cate             
 yj_house_order            
 yj_jpush_device           
 yj_jpush_log              
 yj_jpush_tag              
 yj_maidan                 
 yj_maidan_order           
 yj_mall_cate              
 yj_mall_order             
 yj_mall_product           
 yj_medicine               
 yj_member                 
 yj_member_addr            
 yj_member_cloud           
 yj_member_collect         
 yj_member_feedback        
 yj_member_help            
 yj_member_invite          
 yj_member_log             
 yj_member_message         
 yj_notice                 
 yj_order                  
 yj_order_complaint        
 yj_order_cuilog           
 yj_order_log              
 yj_order_photo            
 yj_order_voice            
 yj_org                    
 yj_org_bash               
 yj_paotui_cate            
 yj_paotui_order           
 yj_payment                
 yj_payment_log            
 yj_session                
 yj_shop                   
 yj_shop_account           
 yj_shop_album             
 yj_shop_album_photo       
 yj_shop_cate              
 yj_shop_comment           
 yj_shop_comment_photo     
 yj_shop_log               
 yj_shop_msg               
 yj_shop_print             
 yj_shop_tixian            
 yj_shop_verify            
 yj_sms_log                
 yj_staff                  
 yj_staff_account          
 yj_staff_comment          
 yj_staff_comment_photo    
 yj_staff_fields           
 yj_staff_log              
 yj_staff_msg              
 yj_staff_tixian           
 yj_staff_verify           
 yj_system_config          
 yj_system_logs            
 yj_system_module          
 yj_themes                 
 yj_tuan                   
 yj_tuan_order             
 yj_tuan_ticket            
 yj_upload_photo           
 yj_user_vote              
 yj_volunteer              
 yj_vote                   
 yj_vote_options           
 yj_waimai                 
 yj_waimai_cate            
 yj_waimai_comment         
 yj_waimai_comment_photo   
 yj_waimai_order           
 yj_waimai_order_product   
 yj_waimai_product         
 yj_waimai_product_cate    
 yj_waimai_product_spec    
 yj_waimai_youhui          
 yj_weixin                 
 yj_weixin_auto            
 yj_weixin_coupon          
 yj_weixin_couponsn        
 yj_weixin_goldegg         
 yj_weixin_goldeggsn       
 yj_weixin_help            
 yj_weixin_helplist        
 yj_weixin_helpprize       
 yj_weixin_helpsn          
 yj_weixin_keyword         
 yj_weixin_log             
 yj_weixin_lottery         
 yj_weixin_lotterysn       
 yj_weixin_menu            
 yj_weixin_packet          
 yj_weixin_packetling      
 yj_weixin_packetling_copy 
 yj_weixin_packetsn        
 yj_weixin_prize           
 yj_weixin_relay           
 yj_weixin_relaylist       
 yj_weixin_relayprize      
 yj_weixin_relaysn         
 yj_weixin_reply           
 yj_weixin_scratch         
 yj_weixin_scratchsn       
 yj_weixin_shake           
 yj_weixin_shakeprize      
 yj_weixin_shakesn         
 yj_weixin_welcome         
 yj_weixiu_attr            
 yj_weixiu_cate            
 yj_weixiu_order           
 yj_wuye_admin             
 yj_wuye_node              
 yj_wye_role               
 yj_xiaoqu                 
 yj_xiaoqu_activity        
 yj_xiaoqu_activity_cate   
 yj_xiaoqu_activity_sign   
 yj_xiaoqu_apply           
 yj_xiaoqu_banner          
 yj_xiaoqu_baoxiu          
 yj_xiaoqu_baoxiu_photo    
 yj_xiaoqu_bianmin         
 yj_xiaoqu_bianmin_cate    
 yj_xiaoqu_bianmin_report  
 yj_xiaoqu_bill            
 yj_xiaoqu_nav             
 yj_xiaoqu_news            
 yj_xiaoqu_report          
 yj_xiaoqu_report_photo    
 yj_xiaoqu_tieba           
 yj_xiaoqu_tieba_cate      
 yj_xiaoqu_tieba_photo     
 yj_xiaoqu_tieba_reply     
 yj_xiaoqu_wuye            
 yj_xiaoqu_wuye_account    
 yj_xiaoqu_wuye_log        
 yj_xiaoqu_wuye_tixian     
 yj_xiaoqu_yezhu           
 yj_yezhu_tag       yj_activity_prize         
 yj_activity_qiangpiao     
 yj_admin                  
 yj_admin_role             
 yj_adv                    
 yj_adv_item               
 yj_app_cate               
 yj_app_noti               
 yj_article                
 yj_article_cate           
 yj_article_content        
 yj_article_photo          
 yj_block                  
 yj_block_item             
 yj_block_page             
 yj_cloud_cate             
 yj_cloud_goods            
 yj_cloud_goods_attr       
 yj_cloud_goods_photo      
 yj_cloud_number           
 yj_cloud_order            
 yj_cloud_share            
 yj_cloud_share_photo      
 yj_coupons_qiangpiao      
 yj_data_area              
 yj_data_business          
 yj_data_city              
 yj_data_province          
 yj_data_shequ             
 yj_data_street            
 yj_h_admin                
 yj_h_role                 
 yj_home_module            
 yj_hongbao                
 yj_hongbao_log            
 yj_house_attr             
 yj_house_cate             
 yj_house_order            
 yj_jpush_device           
 yj_jpush_log              
 yj_jpush_tag              
 yj_maidan                 
 yj_maidan_order           
 yj_mall_cate              
 yj_mall_order             
 yj_mall_product           
 yj_medicine               
 yj_member                 
 yj_member_addr            
 yj_member_cloud           
 yj_member_collect         
 yj_member_feedback        
 yj_member_help            
 yj_member_invite          
 yj_member_log             
 yj_member_message         
 yj_notice                 
 yj_order                  
 yj_order_complaint        
 yj_order_cuilog           
 yj_order_log              
 yj_order_photo            
 yj_order_voice            
 yj_org                    
 yj_org_bash               
 yj_paotui_cate            
 yj_paotui_order           
 yj_payment                
 yj_payment_log            
 yj_session                
 yj_shop                   
 yj_shop_account           
 yj_shop_album             
 yj_shop_album_photo       
 yj_shop_cate              
 yj_shop_comment           
 yj_shop_comment_photo     
 yj_shop_log               
 yj_shop_msg               
 yj_shop_print             
 yj_shop_tixian            
 yj_shop_verify            
 yj_sms_log                
 yj_staff                  
 yj_staff_account          
 yj_staff_comment          
 yj_staff_comment_photo    
 yj_staff_fields           
 yj_staff_log              
 yj_staff_msg              
 yj_staff_tixian           
 yj_staff_verify           
 yj_system_config          
 yj_system_logs            
 yj_system_module          
 yj_themes                 
 yj_tuan                   
 yj_tuan_order             
 yj_tuan_ticket            
 yj_upload_photo           
 yj_user_vote              
 yj_volunteer              
 yj_vote                   
 yj_vote_options           
 yj_waimai                 
 yj_waimai_cate            
 yj_waimai_comment         
 yj_waimai_comment_photo   
 yj_waimai_order           
 yj_waimai_order_product   
 yj_waimai_product         
 yj_waimai_product_cate    
 yj_waimai_product_spec    
 yj_waimai_youhui          
 yj_weixin                 
 yj_weixin_auto            
 yj_weixin_coupon          
 yj_weixin_couponsn        
 yj_weixin_goldegg         
 yj_weixin_goldeggsn       
 yj_weixin_help            
 yj_weixin_helplist        
 yj_weixin_helpprize       
 yj_weixin_helpsn          
 yj_weixin_keyword         
 yj_weixin_log             
 yj_weixin_lottery         
 yj_weixin_lotterysn       
 yj_weixin_menu            
 yj_weixin_packet          
 yj_weixin_packetling      
 yj_weixin_packetling_copy 
 yj_weixin_packetsn        
 yj_weixin_prize           
 yj_weixin_relay           
 yj_weixin_relaylist       
 yj_weixin_relayprize      
 yj_weixin_relaysn         
 yj_weixin_reply           
 yj_weixin_scratch         
 yj_weixin_scratchsn       
 yj_weixin_shake           
 yj_weixin_shakeprize      
 yj_weixin_shakesn         
 yj_weixin_welcome         
 yj_weixiu_attr            
 yj_weixiu_cate            
 yj_weixiu_order           
 yj_wuye_admin             
 yj_wuye_node              
 yj_wye_role               
 yj_xiaoqu                 
 yj_xiaoqu_activity        
 yj_xiaoqu_activity_cate   
 yj_xiaoqu_activity_sign   
 yj_xiaoqu_apply           
 yj_xiaoqu_banner          
 yj_xiaoqu_baoxiu          
 yj_xiaoqu_baoxiu_photo    
 yj_xiaoqu_bianmin         
 yj_xiaoqu_bianmin_cate    
 yj_xiaoqu_bianmin_report  
 yj_xiaoqu_bill            
 yj_xiaoqu_nav             
 yj_xiaoqu_news            
 yj_xiaoqu_report          
 yj_xiaoqu_report_photo    
 yj_xiaoqu_tieba           
 yj_xiaoqu_tieba_cate      
 yj_xiaoqu_tieba_photo     
 yj_xiaoqu_tieba_reply     
 yj_xiaoqu_wuye            
 yj_xiaoqu_wuye_account    
 yj_xiaoqu_wuye_log        
 yj_xiaoqu_wuye_tixian     
 yj_xiaoqu_yezhu           
 yj_yezhu_tag       
