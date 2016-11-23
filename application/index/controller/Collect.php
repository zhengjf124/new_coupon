<?php

namespace app\index\controller;
/**
 * 用户收藏类
 * Class Collect
 * @package app\index\controller
 */

class Collect extends Common
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

}