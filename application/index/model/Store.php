<?php

namespace app\index\model;

use think\Model;

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
        return db('store')->where($where)->count();
    }

    /**
     * 添加
     * @param array $data 需要保存的数据
     */
    public function toAdd($data)
    {
        $test = new Test;
        $test->data($data);
        $test->save();
        return $test->id;
    }

    /**
     * 修改
     * @param $where
     * @param $data
     * @return false|int
     */
    public function toUpdate($where, $data)
    {
        return Test::save($data, $where);
    }

    /**
     * 删除
     * @param $where
     * @return int
     */
    public function toDelete($where)
    {
        return Test::destroy($where);
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
        $store = new Store;
        return $store->where($where)
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
        return db('store')->where($where)->field($field)->find();
    }
}
