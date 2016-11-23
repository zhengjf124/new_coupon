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
}