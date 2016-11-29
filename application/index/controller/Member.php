<?php

namespace app\index\controller;

class Member extends Common
{
    protected $user_id;//用户ID

    public function _initialize()
    {
        parent::_initialize();
        $this->user_id = $this->_checkPassport($this->_getParams('passport'));
    }

    /**
     * 验证passport 返回登录的用户ID
     * @return mixed
     */
    private function _checkPassport($passport)
    {
        if (!preg_match('/^[0-9a-zA-Z]{32}$/', $passport)) {
            $this->_returnError('10010', 'passport不合法');
        }
        $passport_model = new \app\index\model\Passport;
        $user_id = $passport_model->findUserId($passport);
        if (preg_match('/^[1-9]\d*$/', $user_id)) {
            return $user_id;
        }
        $this->_returnError('10010', 'passport不合法');
    }


    /**
     * 获取用户优惠券列表 \n
     * URI : /member/coupon/list
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  sign      | string | 必填 |  签名
     *  passport  | string | 必填 |  用户票据
     *  use_status|  int   | 必须 |  使用状态 10-未使用 20-已使用
     *  type      |  int   | 可选 |  优惠券类型 10-优惠券 20-抵用券
     *
     * @return
     *  name  | type  | description
     * -------|-------|----------------------
     *  list  | array |  优惠券列表
     *
     * list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     * coupon_name  | string |  优惠券名称
     * coupon_desc  | string |  描述
     * subtract     | decimal|  可抵用的金额
     * full         | decimal|  需要满的金额
     * end_time     | string |  有效期
     * type         | int    |  优惠券类型 10-优惠券 20-抵用券
     * use_time     | string |  使用时间
     * number       | string |  优惠券编号(唯一标识)
     *  use_status  | int    |  使用状态 10-未使用 20-已使用
     *
     * @note
     *
     */
    public function couponList()
    {
        $use_status = $this->_getParams('use_status');
        if (!in_array($use_status, [10, 20])) {
            $this->_returnError(10048, '优惠券使用状态不合法');
        }

        $order_model = new \app\index\model\Order;
        $where = [
            'user_id' => $this->user_id,
            'use_status' => $use_status,
        ];

        $type = $this->_getParams('type');
        if (in_array($type, [10, 20])) {
            $where['type'] = $type;
        }

        $field = 'user_coupon_id,coupon_name,coupon_desc,subtract,full,end_time,type,number,use_status';
        $list = $order_model->selectUserCoupon($where, $field);
        if (is_array($list) && empty($list) === false) {
            foreach ($list as &$value) {
                $value['end_time'] = date('Y-m-d', $value['end_time']);
                $value['number'] = $value['user_coupon_id'] . '|' . $value['number'];
                unset($value['user_coupon_id']);
            }
            unset($value);
        } else {
            $list = [];
        }
        $this->_returnData(['list' => $list]);
    }

    /**
     * 获取用户优惠券详细信息 \n
     * URI : /member/coupon/detail
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *   sign     | string | 必填 |  签名
     *   number   | string | 必填 |  优惠券唯一标识
     *  passport  | string | 必填 |  用户票据
     *
     * @return
     *    name      | type  | description
     * -------------|-------|----------------------
     * coupon_detail| array |  优惠券详情
     *  store_list  | array |  门店列表
     *
     * coupon_detail:
     *      name    |  type  | description
     * -------------|--------|----------------------
     * coupon_id    | int    |  优惠券ID
     * coupon_name  | string |  优惠券名称
     * coupon_desc  | string |  描述
     * market_price | decimal|  原价
     * coupon_price | decimal|  实际价格
     * coupon_sales | int    |  销量
     * coupon_banner| array  |  详情页图片
     * use_time     | string |  使用时间
     * use_rule     | string |  使用规则
     * validity     | string |  有效期
     * is_on_sale   | int    |  是否下架 0-已下架 1-上架
     * comment_count| int    |  评论次数
     * end_time     | string |  有效期结束日期
     * number       | string |  优惠券编号
     *
     * store_list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   store_id   |  int   |  门店ID
     *  store_name  | string |  门店名称
     * comment_count|  int   |  评论次数
     *  store_phone | string |  联系电话
     *    address   | string |  地址
     * comment_level|  int   |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *   distance   | string |  距离
     *
     * @note
     *
     */
    public function couponDetail()
    {
        $number = $this->_getParams('number');
        $number = explode('|', $number);
        if (!isset($number[0]) || !preg_match('/^\d*$/', $number[0])) {
            $this->_returnError(10051, '优惠券编号不合法');
        }

        if (!isset($number[1]) || !preg_match('/^\d{6}$/', $number[1])) {
            $this->_returnError(10051, '优惠券编号不合法');
        }

        $user_coupon_id = $number[0];//用户优惠券表ID
        $number = $number[1];//优惠券编号

        $order_model = new \app\index\model\Order;
        $user_coupon_info = $order_model->findUserCoupon(['user_coupon_id' => $user_coupon_id, 'number' => $number], 'coupon_id,end_time');
        if (!$user_coupon_info || !is_array($user_coupon_info)) {
            $this->_returnError(10045, '优惠券不存在');
        }

        $coupon_id = $user_coupon_info['coupon_id'];
        if (!preg_match('/^[1-9][0-9]*$/', $coupon_id)) {
            $this->_returnError(10044, '优惠券ID不合法');
        }

        $coupon_model = new \app\index\model\Coupon;
        $where = ['coupon_id' => $coupon_id, 'is_delete' => 0];
        $field = 'coupon_id,coupon_name,coupon_desc,market_price,coupon_price,coupon_sales,coupon_banner,start_time,end_time,use_time,use_rule,validity_remarks,is_on_sale,comment_count';
        $coupon_detail = $coupon_model->toFind($where, $field);

        if (!$coupon_detail || !is_array($coupon_detail)) {
            $this->_returnError(10045, '优惠券不存在');
        }

        $coupon_detail['use_rule'] = htmlspecialchars_decode($coupon_detail['use_rule']);
        $coupon_detail['coupon_banner'] = json_decode($coupon_detail['coupon_banner']);

        $coupon_detail['validity'] = date('Y.m.d', $coupon_detail['start_time']) . '至' . date('Y.m.d', $coupon_detail['end_time']);
        if (!empty($coupon_detail['validity_remarks'])) {
            $coupon_detail['validity'] .= '(' . $coupon_detail['validity_remarks'] . ')';
        }
        unset($coupon_detail['start_time']);
        unset($coupon_detail['validity_remarks']);

        $coupon_detail['end_time'] = date('Y-m-d', $user_coupon_info['end_time']);
        $coupon_detail['number'] = $user_coupon_id;

        $store_model = new \app\index\model\Store;
        $store_ids = $store_model->findStoreId($coupon_detail['coupon_id']);
        if (empty($store_ids) === false) {
            $store_list = $store_model->toSelect(['store_id' => ['in', $store_ids], 'is_show' => 1], 'store_id,store_name,comment_count,store_phone,address', 0, 20);
            if ($store_list) {
                foreach ($store_list as &$value) {
                    $value['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                    $value['distance'] = '500';//距离
                }
                unset($value);
            }
        } else {
            $store_list = [];
        }

        return json([
            'code' => 0,
            'reason' => '操作成功',
            'data' => ['coupon_detail' => $coupon_detail, 'store_list' => $store_list]
        ]);
    }
}