<?php

namespace app\index\controller;


class Store extends Api
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
     *  trading_id| int   | 可选  | 商圈ID(没有时必须传0)
     *  cat_id    | int   | 可选  | 分类ID(没有时必须传0)
     *  sort_id   | int   | 可选  | 排序ID(没有时必须传0)
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     *   list   |  array  |  商家列表
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
     *  comment_level| int    |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *  distance     | string |  距离
     *
     * @note
     *
     */
    public function getList()
    {

        $param = $this->_createParameters('city_id,page,list_rows,trading_id,cat_id,sort_id');
        if (!preg_match('/^[1-9][0-9]*$/', $param['city_id'])) {
            $this->_returnError(10020, '城市ID不合法');
        }

        $nowPage = $param['page'];//当前页码
        if (!preg_match('/^[1-9][0-9]*$/', $nowPage)) {
            $this->_returnError(10040, '页码不合法');
        }

        $listRows = $param['list_rows'];//一页的条数
        if (!preg_match('/^[1-9][0-9]*$/', $listRows)) {
            $this->_returnError(10046, '一页条数不合法');
        }

        if (preg_match('/^[1-9][0-9]*$/', $param['cat_id'])) {
            $cat_model = new \app\index\model\Category;
            $cat_ids = $cat_model->selectCatId(['parent_id' => $param['cat_id'], 'is_show' => 1]);
            $cat_ids[] = $param['cat_id'];
            $where['cat_id'] = array('in', $cat_ids);
        }

        if (preg_match('/^[1-9][0-9]*$/', $param['trading_id'])) {
            $where['trading_id'] = $param['trading_id'];
        } else {
            $where['city_id'] = $param['city_id'];
        }

        /*        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['sort_id'])) {
                    $this->_returnError(10042, '排序ID不合法');
                }*/
        //排序未完成
        /*        switch ($this->_parameters['sort_id']) {
                    case 2:

                        break;
                }*/

        $where['is_show'] = 1;

        $store_model = new \app\index\model\Store;
        $totalRows = $store_model->toCount($where);//总条数
        $totalPages = ceil($totalRows / $listRows);//总页数
        if ($nowPage > $totalPages) {
            $this->_returnError(10041, '页码超过了总页数');
        }

        $firstRow = $listRows * ($nowPage - 1);//从第几条开始查询
        $field = 'store_id,store_name,comment_count,label,keywords,avg_price,store_img';
        $list = $store_model->toSelect($where, $field, $firstRow, $listRows);
        $data['total_page'] = $totalPages;//总页数
        if (!is_array($list)) {
            $list = [];
        } else {
            foreach ($list as &$value) {
                $value['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                $value['distance'] = '<500m';//距离
            }
        }
        $data['list'] = $list;
        $this->_returnData($data);
    }

    /**
     * 获取商家详细信息 \n
     * URI : /store/detail
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
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
     *      name    |  type  | description
     * -------------|--------|----------------------
     *    store_id  |  int   |  商家ID
     *   store_name | string |  商家名称
     * comment_count|  int   |  评论次数
     *     label    | string |  标签
     *   keywords   | string |  关键字
     *   avg_price  | float  |  人均消费
     * store_banner | string |  详情页图片
     *  store_phone | string |  联系电话
     *    address   | string |  地址
     * comment_level|  int   |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *   distance   | string |  距离
     *   collect    |  int   |  是否收藏 0-未收藏、1-已收藏
     *
     * coupon_list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   coupon_id  |  int   |  优惠券ID
     *  coupon_name | string |  优惠券名称
     *  coupon_price| float  |  优惠券价格
     *  market_price| float  |  原价
     *  coupon_sales|  int   |  销量
     *   coupon_img |  int   |  列表引导图
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/store/detail/parameters/%7B%22store_id%22:%223%22%7D
     *
     */
    public function detail()
    {
        //return json(['/Public/upload/goods/2016/11-04/581bfd282706a.png']);
        $param = $this->_createParameters('store_id,passport');
        if (!preg_match('/^[1-9][0-9]*$/', $param['store_id'])) {
            $this->_returnError(10042, '商家ID不合法');
        }
        $store_model = new \app\index\model\Store;
        $where = ['store_id' => $param['store_id'], 'is_delete' => 0];
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
        $store_detail['distance'] = '<500m';//距离
        $store_detail['collect'] = 0;//未收藏

        if (preg_match('/^[0-9a-zA-Z]{32}$/', $param['passport'])) {
            $passport_model = new \app\index\model\Passport;
            $user_id = $passport_model->findUserId($param['passport']);
            if ($user_id) {
                //收藏部分未完成
                //$collect_info = M('store_favorite')->where(array('user_id' => $user_id, 'store_id' => $store_detail['store_id']))->field('collect_id')->find();
                //if ($collect_info) {
                //$store_detail['collect'] = 1;//已收藏
                //}
            }
        }

        /*        $coupon_ids = M('coupons_store')->where(array('store_id' => $store_detail['store_id']))->getField('coupon_id', true);
                if ($coupon_ids) {
                    $coupon_list = M('coupons_sale')->where(array('coupon_id' => array('in', $coupon_ids), 'is_delete' => 0))->field('coupon_id,coupon_name,coupon_price,market_price,coupon_sales,coupon_img')->order('sort_order')->select();
                }
                if (!isset($coupon_list)) {
                    $coupon_list = array();
                }*/
        $coupon_list = [];
        $this->_returnData(['store_detail' => $store_detail, 'coupon_list' => $coupon_list]);
    }
}