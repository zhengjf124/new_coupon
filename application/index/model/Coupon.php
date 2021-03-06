<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Coupon extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 查询(一条)
     * @param array $where 查询条件
     * @param string $field 需要查询的字段
     * @return array|false|\PDOStatement|string|Model
     */
    public function toFind($where, $field)
    {
        return Db::name('coupon')->where($where)->field($field)->find();
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
        return Db::name('coupon')->where($where)
            ->field($field)
            ->order($key, $sort)
            ->limit($firstRow, $listRows)
            ->select();
    }
}