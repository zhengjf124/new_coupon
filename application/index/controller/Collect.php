<?php

namespace app\index\controller;
/**
 * 用户收藏类
 * Class Collect
 * @package app\index\controller
 */

class Collect extends Member
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 收藏\取消收藏门店 \n
     * URI : /collect/store
     * @param :
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *    sign   | string | 必填 |  签名
     *  passport | string | 必填 |  用户登录凭证
     *  store_id |  int   | 必填 |  商家ID
     *
     * @return
     *   name   |  type  | description
     * ---------|--------|--------------
     *  collect |  int   |  收藏状态 0-未收藏、1-已收藏
     *
     * @note
     *
     */
    public function store()
    {
        $store_id = $this->_getParams('store_id');
        if (!preg_match('/^[1-9][0-9]*$/', $store_id)) {
            $this->_returnError(10042, '商家ID不合法');
        }

        $store_model = new \app\index\model\Store;
        $store_detail = $store_model->toFind(['store_id' => $store_id, 'is_delete' => 0], 'store_id');
        if (!$store_detail || !is_array($store_detail)) {
            $this->_returnError(10043, '商家不存在');
        }
        $collect_model = new \app\index\model\Collect;
        $all_store_id = $collect_model->getStoreId($this->user_id);//获取用户收藏的所有商家ID
        if (in_array($store_id, $all_store_id)) {
            //已收藏,删除已收藏的门店
            $collect_model->deleteStore($store_id, $this->user_id);
            $this->_returnData(['collect' => 0]);
        } else {
            //未收藏,收藏门店
            $collect_model->addStore(['user_id' => $this->user_id, 'store_id' => $store_id, 'add_time' => $this->_now]);
            $this->_returnData(['collect' => 1]);
        }
    }


    /**
     * 收藏\取消收藏优惠券 \n
     * URI : /collect/coupon
     * @param :
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *    sign   | string | 必填 |  签名
     *  passport | string | 必填 |  用户登录凭证
     *  coupon_id|  int   | 必填 |  优惠券ID
     *
     * @return
     *   name   |  type  | description
     * ---------|--------|--------------
     *  collect |  int   |  收藏状态 0-未收藏、1-已收藏
     *
     * @note
     *
     */
    public function coupon()
    {
        $coupon_id = $this->_getParams('coupon_id');
        if (!preg_match('/^[1-9][0-9]*$/', $coupon_id)) {
            $this->_returnError(10044, '优惠券ID不合法');
        }

        $coupon_model = new \app\index\model\Coupon();
        $coupon_detail = $coupon_model->toFind(['coupon_id' => $coupon_id, 'is_delete' => 0, 'is_on_sale' => 1], 'coupon_id');
        if (!$coupon_detail || !is_array($coupon_detail)) {
            $this->_returnError(10045, '优惠券不存在');
        }

        $collect_model = new \app\index\model\Collect;
        $all_coupon_id = $collect_model->getCouponId($this->user_id);//获取用户收藏的所有优惠券ID
        if (in_array($coupon_id, $all_coupon_id)) {
            //已收藏,删除已收藏的优惠券
            $collect_model->deleteCoupon($coupon_id, $this->user_id);
            $this->_returnData(['collect' => 0]);
        } else {
            //未收藏,收藏优惠券
            $collect_model->addCoupon(['user_id' => $this->user_id, 'coupon_id' => $coupon_id, 'add_time' => $this->_now]);
            $this->_returnData(['collect' => 1]);
        }
    }


    /**
     * 获取商家收藏列表 \n
     * URI : /collect/store/list
     * @param :
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *    sign   | string | 必填 |  签名
     *  passport | string | 必填 |  用户登录凭证
     *
     * @return
     *   name  |  type  | description
     * --------|--------|--------------
     *  list   | array  |  商家收藏列表
     *
     * list :
     *      name     |  type  | description
     * --------------|--------|----------------------
     *  store_id     | int    |  商家ID
     *  store_name   | string |  商家名称
     *  label        | string |  标签
     *  avg_price    | float  |  人均消费
     *  store_img    | string |  列表引导图
     *  comment_level| int    |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *  distance     | string |  距离
     *
     * @note
     *
     */
    public function storeList()
    {
        $collect_model = new \app\index\model\Collect;
        $all_store_id = $collect_model->getStoreId($this->user_id);
        if (isset($all_store_id)) {
            $store_model = new \app\index\model\Store;
            $field = 'store_id,store_name,label,avg_price,store_img';
            $list = $store_model->toSelect(['store_id' => ['in', $all_store_id], 'is_show' => 1], $field, '', '');
            if ($list) {
                foreach ($list as &$value) {
                    $value['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                    $value['distance'] = '500';//距离
                }
                unset($value);
            }
        } else {
            $list = [];
        }
        $this->_returnData(['list' => $list]);
    }

    /**
     * 获取优惠券收藏列表 \n
     * URI : /collect/store/list
     * @param :
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *    sign   | string | 必填 |  签名
     *  passport | string | 必填 |  用户登录凭证
     *
     * @return
     *   name  |  type  | description
     * --------|--------|--------------
     *  list   | array  |  优惠券收藏列表
     *
     * list :
     *    name      |  type  | description
     * -------------|--------|----------------------
     *  coupon_id   | int    |  优惠券ID
     *  coupon_name | string |  优惠券名称
     *  coupon_desc | string |  优惠券描述
     *  market_price| float  |  市场价
     *  coupon_price| string |  优惠券价格
     *  coupon_img  | string |  优惠券列表引导图
     *  coupon_sales| int    |  优惠券销量
     *  is_res      | int    |  是否预约 0-免预约，1-需要预约
     *  is_on_sale  | int    |  是否下架 0-已下架 1-上架
     *
     * @note
     *
     */
    public function couponList()
    {
        $collect_model = new \app\index\model\Collect;
        $all_coupon_id = $collect_model->getCouponId($this->user_id);
        if (isset($all_coupon_id)) {
            $coupon_model = new \app\index\model\Coupon;
            $field = 'coupon_id,coupon_name,coupon_desc,market_price,coupon_price,coupon_img,coupon_sales,is_res,is_on_sale';
            $list = $coupon_model->toSelect(['coupon_id' => ['in', $all_coupon_id]], $field, '', '');
        } else {
            $list = [];
        }
        $this->_returnData(['list' => $list]);
    }
}