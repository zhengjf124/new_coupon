<?php

namespace app\index\model;

use think\Model;

class Test extends Model
{
    protected function initialize()
    {
        parent::initialize();
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
     * 查询
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function toSelect()
    {
        $test = new Test;
        return $test->where(['id' => ['gt', 200]])
            ->order('id', 'desc')
            ->limit(10)
            ->select();
    }

    /**
     * 查询
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function toFind()
    {
        $test = new Test;
        return $test->where(['id' => ['gt', 200]])
            ->order('id', 'desc')
            ->limit(10)
            ->select();
    }
}
