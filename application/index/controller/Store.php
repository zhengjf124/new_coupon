<?php

namespace app\index\controller;


class Store extends Common
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取商家列表 \n
     * URI : /store/list
     * @param :
     *  name      | type  | null | description
     * -----------|-------|------|-------------
     *  sign      | string| 必填  | 签名
     *  city_id   | int   | 必填  | 城市ID
     *  page      | int   | 必填  | 页码
     *  list_rows | int   | 必填  | 一页的条数
     *  trading_id| int   | 可选  | 商圈ID
     *  cat_id    | int   | 可选  | 分类ID
     *  sort_id   | int   | 可选  | 排序ID
     *
     * @return
     *    name    |  type   | description
     * -----------|---------|----------------------
     * total_page |   int   |  总页数
     *   list     |  array  |  商家列表
     *
     * list :
     *      name     |  type  | description
     * --------------|--------|----------------------
     *  store_id     | int    |  商家ID
     *  store_name   | string |  商家名称
     *  comment_count| string |  评论次数
     *  label        | string |  标签
     *  keywords     | string |  关键字
     *  avg_price    | float  |  人均消费
     *  store_img    | string |  列表引导图
     *  sales        | int    |  销量
     *  comment_level| int    |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *  distance     | string |  距离
     *
     * @note
     *
     */
    public function getList()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_getParams('city_id'))) {
            $this->_returnError(10020, '城市ID不合法');
        }

        $nowPage = $this->_getParams('page');//当前页码
        if (!preg_match('/^[1-9][0-9]*$/', $nowPage)) {
            $this->_returnError(10040, '页码不合法');
        }

        $listRows = $this->_getParams('list_rows');//一页的条数
        if (!preg_match('/^[1-9][0-9]*$/', $listRows)) {
            $this->_returnError(10046, '一页条数不合法');
        }

        if (preg_match('/^[1-9][0-9]*$/', $this->_getParams('cat_id'))) {
            $cat_model = new \app\index\model\Category;
            $cat_ids = $cat_model->selectCatId(['parent_id' => $this->_getParams('cat_id'), 'is_show' => 1]);
            $cat_ids[] = $this->_getParams('cat_id');
            $where['cat_id'] = array('in', $cat_ids);
        }

        if (preg_match('/^[1-9][0-9]*$/', $this->_getParams('trading_id'))) {
            $where['trading_id'] = $this->_getParams('trading_id');
        } else {
            $where['city_id'] = $this->_getParams('city_id');
        }

        $where['is_show'] = 1;

        $store_model = new \app\index\model\Store;
        $totalRows = $store_model->toCount($where);//总条数
        $totalPages = ceil($totalRows / $listRows);//总页数
        if ($nowPage > $totalPages && $totalPages > 0) {
            $this->_returnError(10041, '页码超过了总页数');
        }

        $firstRow = $listRows * ($nowPage - 1);//从第几条开始查询
        $field = 'store_id,store_name,comment_count,label,keywords,avg_price,store_img,sales';
        $list = $store_model->toSelect($where, $field, '', '');
        $data['total_page'] = $totalPages;//总页数
        if (!is_array($list)) {
            $list = [];
        } else {
            foreach ($list as &$value) {
                $value['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                $value['distance'] = '500';//距离
            }
            unset($value);
        }

        $sort_id = $this->_getParams('sort_id');
        if (!preg_match('/^[1-9][0-9]*$/', $sort_id)) {
            $sort_id = 10;
        }

        $store_logic = new \app\index\logic\Store;
        $list2 = $store_logic->toSort($list, $sort_id);
        $data['list'] = $this->getArray($list2, $firstRow, $listRows);
        $this->_returnData($data);
    }


    /**
     * 获取商家详细信息 \n
     * URI : /store/detail
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *    sign    | string | 必填  | 签名
     *  store_id  |  int   | 必须  | 商家ID
     *  passport  | string | 可选  | 用户票据(没有时必须传0)
     *
     * @return
     *      name    |  type  | description
     * -------------|--------|----------------------
     *  store_detail|  array |  商家详情
     *  coupon_list |  array |  优惠券列表
     *
     * store_detail:
     *      name      |  type  | description
     * ---------------|--------|----------------------
     *    store_id    |  int   |  商家ID
     *   store_name   | string |  商家名称
     * comment_count  |  int   |  评论次数
     *     label      | string |  标签
     *   keywords     | string |  关键字
     *   avg_price    | float  |  人均消费
     * store_banner   | array |  详情页图片
     *  store_phone   | string |  联系电话
     *    address     | string |  地址
     * comment_level  |  int   |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *   distance     | string |  距离
     *   collect      |  int   |  是否收藏 0-未收藏、1-已收藏
     * mobile_pay_num |  int   |  手机买单数量
     *
     * coupon_list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   coupon_id  |  int   |  优惠券ID
     *  coupon_name | string |  优惠券名称
     *  coupon_desc | string |  优惠券描述
     *  coupon_price| float  |  优惠券价格
     *  market_price| float  |  原价
     *  coupon_sales|  int   |  销量
     *   coupon_img |  int   |  列表引导图
     *    is_res    |  int   |  是否预约 0-免预约，1-需要预约
     *    stand_by  |  float |  立减金额（0代表没有立减）
     *
     * @note
     *
     *
     */
    public function detail()
    {
        $store_id = $this->_getParams('store_id');
        if (!preg_match('/^[1-9][0-9]*$/', $store_id)) {
            $this->_returnError(10042, '商家ID不合法');
        }
        $store_model = new \app\index\model\Store;
        $where = ['store_id' => $store_id, 'is_delete' => 0];
        $field = 'store_id,store_name,comment_count,label,keywords,avg_price,store_banner,store_phone,address';
        $store_detail = $store_model->toFind($where, $field);
        if (!$store_detail || !is_array($store_detail)) {
            $this->_returnError(10043, '商家不存在');
        }
        $store_banner = json_decode($store_detail['store_banner']);
        if ($store_banner) {
            $store_detail['store_banner'] = $store_banner;
        } else {
            $store_detail['store_banner'] = [];
        }
        $store_detail['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
        $store_detail['distance'] = '500';//距离
        $store_detail['collect'] = 0;//未收藏
        $store_detail['mobile_pay_num'] = 38;//手机买单数量

        $passport = $this->_getParams('passport');
        if (preg_match('/^[0-9a-zA-Z]{32}$/', $passport)) {
            $passport_model = new \app\index\model\Passport;
            $user_id = $passport_model->findUserId($passport);
            if ($user_id) {
                $collect_model = new \app\index\model\Collect;
                //获取用户收藏的所有商家ID
                $all_store_id = $collect_model->getStoreId($user_id);
                if (in_array($store_detail['store_id'], $all_store_id)) {
                    $store_detail['collect'] = 1;//已收藏
                }
            }
        }

        $coupon_ids = $store_model->findCouponId($store_detail['store_id']);
        if (empty($coupon_ids) === false) {
            $coupon_model = new \app\index\model\Coupon;
            $coupon_list = $coupon_model->toSelect(['coupon_id' => ['in', $coupon_ids], 'is_delete' => 0, 'is_on_sale' => 1], 'coupon_id,coupon_name,coupon_desc,coupon_price,market_price,coupon_sales,coupon_img,is_res,stand_by', 0, 30);

        } else {
            $coupon_list = [];
        }
        $this->_returnData(['store_detail' => $store_detail, 'coupon_list' => $coupon_list]);
    }

    /**
     * 获取商家详细信息 \n
     * URI : /store/sort
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  sign      | string | 必填  | 签名
     *
     * @return
     *      name   |  type  | description
     * ------------|--------|----------------------
     *    sort_id  |  int   |  排序ID
     *   sort_name | string |  排序名称
     *
     */
    public function getSort()
    {
        $data[0]['sort_id'] = 10;
        $data[0]['sort_name'] = '离我最近';
        $data[1]['sort_id'] = 20;
        $data[1]['sort_name'] = '人气最高';
        $data[2]['sort_id'] = 30;
        $data[2]['sort_name'] = '评价最好';
        $data[3]['sort_id'] = 40;
        $data[3]['sort_name'] = '价格最高';
        $data[4]['sort_id'] = 50;
        $data[4]['sort_name'] = '价格最低';
        $this->_returnData($data);
    }
}