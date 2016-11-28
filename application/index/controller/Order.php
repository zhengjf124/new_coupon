<?php

namespace app\index\controller;
/**
 * Class Order
 * @package app\index\controller
 */
class Order extends Common
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 提交优惠券订单 \n
     * URI : /order/add
     * @param :
     *  name      | type   | null | description
     * -----------|--------|------|-------------
     *  sign      | string | 必填 | 签名
     *  passport  | string | 必填 | 用户登录凭证
     *  coupon_id | int    | 必填 | 优惠券ID
     *
     * @return
     *  name     |  type  | description
     * ----------|--------|----------------------
     *  order_sn | string |  订单编号
     *
     */
    public function addCoupon()
    {
        $coupon_id = $this->_getParams('coupon_id');
        if (!preg_match('/^[1-9][0-9]*$/', $coupon_id)) {
            $this->_returnError(10044, '优惠券ID不合法');
        }

        $coupon_num = $this->_getParams('coupon_num');
        if (!preg_match('/^[1-9][0-9]*$/', $coupon_num)) {
            $this->_returnError(10047, '优惠券数量不合法');
        }

        $coupon_model = new \app\index\model\Coupon;
        $where = ['coupon_id' => $coupon_id, 'is_delete' => 0];
        $field = 'coupon_id,coupon_name,coupon_title,coupon_desc,market_price,coupon_price,coupon_img,original_img,is_res,full,subtract,type,start_time,end_time,use_time,use_rule,validity_remarks,stand_by';
        $coupon_info = $coupon_model->toFind($where, $field);

        if (!$coupon_info || !is_array($coupon_info) || empty($coupon_info)) {
            $this->_returnError(10045, '优惠券不存在');
        }

        $coupon_info['add_time'] = $this->_now;
        //优惠券总金额(已扣掉立减金额)
        $coupon_money = ($coupon_info['coupon_price'] - $coupon_info['stand_by']) * $coupon_num;

        $data['order_sn'] = date('YmdHis', $this->_now) . rand(100000, 999999); //订单编号
        $data['coupon_id'] = $coupon_id; //优惠券ID
        $data['coupon_num'] = $coupon_num; //优惠券数量
        $data['user_id'] = $this->user_id; //用户ID
        $data['order_amount'] = $coupon_money; //应付金额
        $data['add_time'] = $this->_now; //下单时间

        $order_model = new \app\index\model\Order;
        $order_id = $order_model->toAddOrder($data);
        if ($order_id) {
            $coupon_info['order_id'] = $order_id;
            $order_model->toAddCoupon($coupon_info); //添加优惠券记录
            $this->_returnData(['order_sn' => $data['order_sn']]);
        } else {
            $this->_returnError(10060, '提交订单失败');
        }
    }
}