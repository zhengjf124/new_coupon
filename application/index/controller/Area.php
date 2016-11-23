<?php

namespace app\index\controller;

class Area extends Api
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取城市 \n
     * URI : /area/city
     * @param :
     *  name  |  type  | null | description
     * -------|--------|------|-------------
     *  sign  | string | 必填 | 签名
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     *   list   |  array  |  城市列表
     *
     * list :
     *    name   |  type  | description
     * ----------|--------|----------------------
     *   city_id |  int   |  城市ID
     * city_name | string |  城市名称
     *
     */
    public function city()
    {
        $area = new \app\index\model\Area;
        $list = $area->toSelect(['type' => 3], 'area_id as city_id,name as city_name');
        $this->_returnData(['list' => $list]);
    }


    /**
     * 获取县/区 \n
     * URI : /area/district
     * @param :
     *  name    | type   | null | description
     * ---------|--------|------|-------------
     *  sign    | string | 必填 |  签名
     *  city_id | int    | 必填 |  城市ID
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     *   list   |  array  |  县、区列表
     *
     * list :
     *      name     |  type  | description
     * --------------|--------|----------------------
     *  district_id  |  int   |  县/区ID
     * district_name | string |  县/区名称
     *    sub_num    |  int   |  县/区下的商圈数量
     */
    public function district()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_getParams('city_id'))) {
            $this->_returnError(10020, '城市ID不合法');
        }
        $area = new \app\index\model\Area;
        $list = $area->selectDistrict($this->_getParams('city_id'));
        $this->_returnData(['list' => $list]);
    }

    /**
     * 获取获取商圈 \n
     * URI : /area/trading
     * @param :
     *     name    | type   | null | description
     * ------------|--------|------|-------------
     *     sign    | string | 必填 |  签名
     *  district_id|  int   | 必填 |  县/区ID
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     *   list   |  array  |  商圈列表
     *
     * list :
     *     name     |  type  | description
     * -------------|--------|----------------------
     *  trading_id  |  int   |  商圈ID
     *  trading_name| string |  商圈名称
     *
     */
    public function tradingArea()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_getParams('district_id'))) {
            $this->_returnError(10021, '县区ID不合法');
        }
        $area = new \app\index\model\Area;
        $list = $area->selectTradingArea($this->_getParams('district_id'));
        $this->_returnData(['list' => $list]);
    }
}