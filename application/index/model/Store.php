<?php

namespace app\index\model;

use think\Model;
use think\Db;

class Store extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 统计条数
     * @param $where
     */
    public function toCount($where)
    {
        return Db::name('store')->where($where)->count();
    }

    /**
     * 查询(多条)
     * @param array $where 查询条件
     * @param string $field 需要查询的字段
     * @param int $firstRow //从第几条开始查询
     * @param int $listRows //一页的条数
     * @param string string $key //键
     * @param string $sort //排序 ASC、DESC
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function toSelect($where, $field, $firstRow, $listRows, $key = 'sort_order', $sort = 'ASC')
    {
        return Db::name('store')->where($where)
            ->field($field)
            ->order($key, $sort)
            ->limit($firstRow, $listRows)
            ->select();
    }

    /**
     * 查询(一条)
     * @param array $where 查询条件
     * @param string $field 需要查询的字段
     * @return array|false|\PDOStatement|string|Model
     */
    public function toFind($where, $field)
    {
        return Db::name('store')->where($where)->field($field)->find();
    }

    /**
     * 获取某个优惠券对应的门店ID（数组）
     * @param int $coupon_id 优惠券ID
     * @return array
     */
    public function findStoreId($coupon_id)
    {
        return Db::name('store_coupon')->where(['coupon_id' => $coupon_id])->column('store_id');
    }

    /**
     * 获取某个门店对应的优惠券ID（数组）
     * @param int $store_id 门店ID
     * @return array
     */
    public function findCouponId($store_id)
    {
        return Db::name('store_coupon')->where(['store_id' => $store_id])->column('coupon_id');
    }
}
