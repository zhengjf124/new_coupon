<?php

namespace app\index\model;

use think\model;
use think\Db;

class Area extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 查询
     * @param array $where 查询条件
     * @param string $field 查询的字段
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function toSelect($where, $field)
    {
        return db('china_area')->where($where)->field($field)->select();
    }

    /**
     * 获取县/区
     * @param int $city_id 城市ID
     * @return mixed
     */
    public function selectDistrict($city_id)
    {
        return Db::query("SELECT c.area_id as district_id,c.name as district_name,(SELECT count(d.area_id) FROM " . config("database.prefix") . "trading_area AS d WHERE c.area_id=d.district_id) AS sub_num FROM " . config("database.prefix") . "china_area AS c WHERE c.type=4 AND c.parent_id=" . $city_id);
    }

    /**
     * 查询商圈表
     * @param int $district_id 县/区
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function selectTradingArea($district_id)
    {
        return db('trading_area')->where(['district_id' => $district_id])->field('area_id as trading_id,area_name as trading_name')->select();
    }

}