<?php

namespace app\index\model;

use think\Model;
use think\Db;

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
        Db::name('user')->insert($data);
        return Db::name('user')->getLastInsID();
    }

    /**
     * 查询
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function toFind($where, $field = 'user_id')
    {
        return Db::name('user')->where($where)
            ->field($field)
            ->find();
    }
}
