<?php

namespace app\index\model;

use think\Model;
use think\Db;

class Order extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 添加数据到优惠券购买订单表
     * @param array $data 需要添加的数据
     * @return string
     */
    public function toAddOrder($data)
    {
        Db::name('buy_order')->insert($data);
        return Db::name('buy_order')->getLastInsID();
    }

    /**
     * 添加数据到优惠券购买记录表
     * @param array $data 需要添加的数据
     * @return int|string
     */
    public function toAddCoupon($data)
    {
        return Db::name('order_coupon')->insert($data);
    }

    /**
     * 添加数据到用户优惠券记录表
     * @param array $data 需要添加的数据
     * @return int|string
     */
    public function toAddUserCoupon($data)
    {
        Db::name('user_coupon')->insert($data);
        return Db::name('user_coupon')->getLastInsID();
    }

    /**
     * 获取用户优惠券表数据(多条)
     * @param array $where 条件
     * @param string $field 需要获取的字段
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function selectUserCoupon($where, $field)
    {
        return Db::name('user_coupon')->where($where)->field($field)->select();
    }

    /**
     * 获取用户优惠券表数据(一条)
     * @param array $where 条件
     * @param string $field 需要获取的字段
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function findUserCoupon($where, $field)
    {
        return Db::name('user_coupon')->where($where)->field($field)->find();
    }

    /**
     * 查询（一条）订单信息
     * @param array $where 查询条件
     * @param string $field 查询字段
     */
    public function toFindOrder($where, $field)
    {
        return Db::name('buy_order')->where($where)->field($field)->find();
    }

}