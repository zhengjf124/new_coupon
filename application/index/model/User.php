<?php

namespace app\index\model;

use think\Model;

class User extends Model
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
        $user = new User;
        $user->data($data);
        $user->save();
        return $user->user_id;
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
     * @param $where
     * @param $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function toSelect($where, $field)
    {
        $test = new User;
        return $test->where($where)
            ->field($field)
            ->order('id', 'desc')
            ->select();
    }

    /**
     * 查询
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function toFind($where, $field = 'user_id')
    {
        $test = new User;
        return $test->where($where)
            ->field($field)
            ->find();
    }
}
