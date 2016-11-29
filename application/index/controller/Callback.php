<?php

namespace app\index\controller;

class Callback extends Api
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 微信回调地址 \n
     * URI : /callback/wechat
     * @param :
     *     name   |  type  | null | description
     * -----------|--------|------|-------------
     *    sign    | string | 必填  | 签名
     *
     * @return
     *  name   |  type  | description
     * --------|--------|-------------
     * --------| ------ |  无
     *
     */
    public function weChat()
    {

    }


    /**
     * 支付宝回调地址 \n
     * URI : /callback/alipay
     * @param :
     *     name   |  type  | null | description
     * -----------|--------|------|-------------
     *    sign    | string | 必填  | 签名
     *
     * @return
     *  name   |  type  | description
     * --------|--------|-------------
     * --------| ------ |  无
     *
     */
    public function aliPay()
    {
        $a = \think\Db::name('coupon')->where(['coupon_id' => 2])->find();
        $this->addUserCoupon(22, $a, 1, 9, json_encode([1]));
        return json($a);
    }

    /**
     * 添加用户优惠券
     * @param int $user_id 用户ID
     * @param array $coupon_info 优惠券信息
     * @param int $coupon_num 优惠券数量
     * @param int $order_id 订单ID
     */
    private function addUserCoupon($user_id, $coupon_info, $coupon_num, $order_id, $store_id)
    {
        $data['user_id'] = $user_id;
        $data['coupon_name'] = $coupon_info['coupon_name'];
        $data['full'] = $coupon_info['full'];
        $data['subtract'] = $coupon_info['subtract'];
        $data['type'] = $coupon_info['type'];
        $data['start_time'] = $coupon_info['start_time'];
        $data['end_time'] = $coupon_info['end_time'];
        $data['store_id'] = $store_id;
        $data['is_res'] = $coupon_info['is_res'];
        $data['coupon_desc'] = $coupon_info['coupon_desc'];
        $data['use_time'] = $coupon_info['use_time'];
        $data['use_rule'] = $coupon_info['use_rule'];
        $data['validity_remarks'] = $coupon_info['validity_remarks'];
        $data['coupon_price'] = $coupon_info['coupon_price'];
        $data['coupon_id'] = $coupon_info['coupon_id'];
        $data['order_id'] = $order_id;
        $data['add_time'] = $this->_now;

        $order_model = new \app\index\model\Order;
        if (preg_match('/^[1-9]\d*$/', $coupon_num)) {
            for ($i = 0; $i < $coupon_num; $i++) {
                $data['number'] = rand(100000, 999999);
                $order_model->toAddUserCoupon($data);
            }
            return true;
        } else {
            return false;
        }
    }

}
