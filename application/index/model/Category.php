<?php

namespace app\index\model;

use think\Model;
use think\Db;

class Category extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 查询
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function toSelect($where)
    {
        $sql = "SELECT a.cat_id,a.type_name,a.type_img,(SELECT COUNT(b.cat_id) FROM " . config("database.prefix") . "category as b  WHERE b.parent_id=a.cat_id) as sub_num FROM " . config("database.prefix") . "category as a WHERE is_show=1 and " . $where . " ORDER BY sort_order,is_hot DESC";
        return Db::query($sql);
    }


    /**
     * 查询分类ID
     * @param $where
     * @return array
     */
    public function selectCatId($where)
    {
        return Db::name('category')->where($where)->column('cat_id');
    }
}
