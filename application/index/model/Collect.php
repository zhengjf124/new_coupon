<?php

namespace app\index\model;

use think\Model;
use think\Db;

class Collect extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 获取用户收藏的所有商家ID
     * @param $user_id
     * @return array
     */
    public function getStoreId($user_id)
    {
        return Db::name('store_collect')->where(['user_id' => $user_id])->column('store_id');
    }

    /**
     * 获取用户收藏的所有优惠券ID
     * @param $user_id
     * @return array
     */
    public function getCouponId($user_id)
    {
        return Db::name('coupon_collect')->where(['user_id' => $user_id])->column('coupon_id');
    }

    /**
     * 删除用户收藏的门店
     * @param int $store_id 门店ID
     * @param int $user_id 用户ID
     * @return int
     */
    public function deleteStore($store_id, $user_id)
    {
        return Db::name('store_collect')->where(['store_id' => $store_id, 'user_id' => $user_id])->delete();
    }

    /**
     * 删除用户收藏的优惠券
     * @param int $store_id 门店ID
     * @param int $user_id 用户ID
     * @return int
     */
    public function deleteCoupon($coupon_id, $user_id)
    {
        return Db::name('coupon_collect')->where(['coupon_id' => $coupon_id, 'user_id' => $user_id])->delete();
    }

    /**
     * 添加门店收藏记录
     * @param array $data 需要添加的参数
     */
    public function addStore($data)
    {
        return Db::name('store_collect')->insert($data);
    }

    /**
     * 添加优惠券收藏记录
     * @param array $data 需要添加的参数
     */
    public function addCoupon($data)
    {
        return Db::name('coupon_collect')->insert($data);
    }
}