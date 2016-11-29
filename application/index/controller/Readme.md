文档说明                          {#接口说明！！！}
============


基础说明
------------

测试环境域名：192.168.199.220

shop基本约定，接口使用http协议get/post请求，返回数据均使用JSON格式返回。\n

所有接口都必须进行加密验证，否则无法使用接口\n
加密规则：\n
	将接口文档中除了sign参数以外的所有参数加上后台提供的token根据键值进行升序排列，\n
	将排列后各个健对应的值按顺序用&进行连接，再使用md5加密得到sign。\n\n

	token 为健  值为 coupon357932(测试环境使用)\n\n

例：
         name   |  type  | null | description
     -----------|--------|------|-------------
      password  | string | 必填  |   密码
       mobile   | string | 必填  |  手机号码      


    将mobile,password,token进行排序(升序)
    结果为：
    	mobile => 13688888888
    	password => 123456
    	token => coupon357932
    sign = md5(13688888888&123456&coupon357932)
    
   	之后将参数用http协议POST/GET 传给服务器即可\n


\n\n\n

接口目录        {#接口目录！！}
============
[注册登录短信接口](classapp_1_1index_1_1controller_1_1_user.html)\n
[分类接口](classapp_1_1index_1_1controller_1_1_category.html)\n
[区域接口](classapp_1_1index_1_1controller_1_1_area.html)\n
[收藏接口](classapp_1_1index_1_1controller_1_1_collect.html)\n
[门店相关接口](classapp_1_1index_1_1controller_1_1_store.html)\n
[优惠券相关接口](classapp_1_1index_1_1controller_1_1_coupon.html)\n
[订单相关接口](classapp_1_1index_1_1controller_1_1_order.html)\n
[个人中心相关接口](classapp_1_1index_1_1controller_1_1_member.html)\n


\n
\n
\n

错误码                          {#错误码！！！}
============

全局返回码说明如下：

返回码			说明
  
-1	        系统繁忙，此时请开发者稍候再试

0		    请求成功

10001		sign不合法

10002		短信发送超过上限

10003		手机号码已经被注册

10004		密码不合法

10005		用户名为空

10006		用户来源不合法

10007		短信验证码不合法

10008		手机号码不合法

10009		用户名或密码错误

10010		passport不合法

10011  		短信验证码错误

10012		接收短信的手机号与提交的手机号不匹配

10013 		短信已发送，请勿重复操作

10014       短信发送失败，请重试















10020		城市ID不合法

10021		县区ID不合法









10040		页码不合法

10041		页码超过了总页数

10042		商家ID不合法

10043		商家不存在

10044		优惠券ID不合法

10045		优惠券不存在

10046       一页条数不合法

10047       优惠券数量不合法

10048       优惠券使用状态不合法

10049       优惠券类型不合法

10050       该优惠券已下架

10051       优惠券编号不合法





10060       提交订单失败

10061       订单编号不合法

10062       订单不存在

10063       支付类型不合法

10064       该订单已完成支付，请勿重复支付
