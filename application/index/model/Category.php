<?php

namespace app\index\model;

use think\Model;

class Category extends Model
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
    public function toSelect($where)
    {
        $cat = new Category;
        $sql = "SELECT a.cat_id,a.type_name,a.type_img,(SELECT COUNT(b.cat_id) FROM " . config("database.prefix") . "category as b  WHERE b.parent_id=a.cat_id) as sub_num FROM " . config("database.prefix") . "category as a WHERE is_show=1 and " . $where . " ORDER BY sort_order,is_hot DESC";
        return $cat->query($sql);
    }


    /**
     * 查询分类ID
     * @param $where
     * @return array
     */
    public function selectCatId($where)
    {
        return db('category')->where($where)->column('cat_id');
    }
}
