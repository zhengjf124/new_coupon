<?php

namespace app\index\controller;

class Comment extends Member
{
    public function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 添加评论 \n
     * URI : /comment/add
     * @param :
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *  sign     | string | 必填 |  签名
     *  passport | string | 必填 |  用户登录凭证
     *  number   | string | 必填 |  优惠券编号
     *  content  | string | 必填 |  评论内容
     *
     * @return
     *   name  |  type  | description
     * --------|--------|--------------
     *         |        |
     *
     *
     * @note
     *
     */
    public function addComment()
    {
        $number = $this->_getParams('number');
        $number = explode('|', $number);
        if (!isset($number[0]) || !preg_match('/^\d*$/', $number[0])) {
            $this->_returnError(10051, '优惠券编号不合法');
        }

        if (!isset($number[1]) || !preg_match('/^\d{6}$/', $number[1])) {
            $this->_returnError(10051, '优惠券编号不合法');
        }

        $content = $this->_getParams('content'); //评论内容
        $level = $this->_getParams('level'); //评论星级

        $user_coupon_id = $number[0];//用户优惠券表ID
        $number = $number[1];//优惠券编号

        $order_model = new \app\index\model\Order;
        $user_coupon_info = $order_model->findUserCoupon(['user_coupon_id' => $user_coupon_id, 'number' => $number], 'coupon_id,end_time,order_id,store_id');
        if (!$user_coupon_info || !is_array($user_coupon_info)) {
            $this->_returnError(10045, '优惠券不存在');
        }

        $coupon_id = $user_coupon_info['coupon_id'];
        if (!preg_match('/^[1-9][0-9]*$/', $coupon_id)) {
            $this->_returnError(10044, '优惠券ID不合法');
        }


        $user_model = new \app\index\model\User;
        $user_info = $user_model->toFind(['user_id' => $this->user_id], 'user_name');

        $data['coupon_id'] = $coupon_id; //优惠券ID
        $data['coupon_id'] = $this->user_id; //用户ID
        $data['user_name'] = $user_info['user_name']; //用户名
        $data['content'] = $content; //评论内容


        $data['ip_address'] = $this->getIP(); //IP地址
        $data['add_time'] = $this->_now; //评论时间

        $data['order_id'] = $user_coupon_info['order_id'];

        $data['store_id'] = $user_coupon_info['store_id'];

        return json($data);

    }

}