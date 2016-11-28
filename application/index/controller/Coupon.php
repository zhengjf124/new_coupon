<?php
namespace app\index\controller;

class Coupon extends Api
{
    public function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 获取优惠券详细信息 \n
     * URI : /coupon/detail
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  coupon_id |  int   | 必须  | 优惠券ID
     *  passport  | string | 可选  | 用户票据
     *
     * @return
     *      name    |  type  | description
     * -------------|--------|----------------------
     * coupon_detail|  array |  优惠券详情
     *  store_list  |  array |  门店列表
     *
     * coupon_detail:
     *      name    |  type  | description
     * -------------|--------|----------------------
     * coupon_id    | int    |  优惠券ID
     * coupon_name  | string |  优惠券名称
     * coupon_desc  | string |  描述
     * market_price | decimal|  原价
     * coupon_price | decimal|  实际价格
     * coupon_sales | int    |  销量
     * coupon_banner| array  |  详情页图片
     * use_time     | string |  使用时间
     * use_rule     | string |  使用规则
     * collect      | int    |  是否收藏 0-未收藏、1-已收藏
     * validity     | string |  有效期
     *
     * store_list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   store_id   |  int   |  门店ID
     *  store_name  | string |  门店名称
     * comment_count|  int   |  评论次数
     *  store_phone | string |  联系电话
     *    address   | string |  地址
     * comment_level|  int   |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *   distance   | string |  距离
     *
     * @note
     *
     */
    public function detail()
    {
        $coupon_id = $this->_getParams('coupon_id');
        if (!preg_match('/^[1-9][0-9]*$/', $coupon_id)) {
            $this->_returnError(10044, '优惠券ID不合法');
        }

        $coupon_model = new \app\index\model\Coupon;
        $where = ['coupon_id' => $coupon_id, 'is_delete' => 0];
        $field = 'coupon_id,coupon_name,coupon_desc,market_price,coupon_price,coupon_sales,coupon_banner,start_time,end_time,use_time,use_rule,validity_remarks';
        $coupon_detail = $coupon_model->toFind($where, $field);

        if (!$coupon_detail || !is_array($coupon_detail)) {
            $this->_returnError(10045, '优惠券不存在');
        }
        $coupon_detail['use_rule'] = htmlspecialchars_decode($coupon_detail['use_rule']);
        $coupon_detail['coupon_banner'] = json_decode($coupon_detail['coupon_banner']);
        $coupon_detail['collect'] = 0;//未收藏

        $passport = $this->_getParams('passport');
        if (preg_match('/^[0-9a-zA-Z]{32}$/', $passport)) {
            $passport_model = new \app\index\model\Passport;
            $user_id = $passport_model->findUserId($passport);
            if ($user_id) {
                $collect_model = new \app\index\model\Collect;
                //获取用户收藏的所有商家ID
                $all_coupon_id = $collect_model->getCouponId($user_id);
                if (in_array($coupon_detail['coupon_id'], $all_coupon_id)) {
                    $coupon_detail['collect'] = 1;//已收藏
                }
            }
        }
        $coupon_detail['validity'] = date('Y.m.d', $coupon_detail['start_time']) . '至' . date('Y.m.d', $coupon_detail['end_time']);
        if (!empty($coupon_detail['validity_remarks'])) {
            $coupon_detail['validity'] .= '(' . $coupon_detail['validity_remarks'] . ')';
        }
        unset($coupon_detail['start_time']);
        unset($coupon_detail['end_time']);
        unset($coupon_detail['validity_remarks']);

        $store_model = new \app\index\model\Store;
        $store_ids = $store_model->findStoreId($coupon_detail['coupon_id']);
        if (empty($store_ids) === false) {
            $store_list = $store_model->toSelect(['store_id' => ['in', $store_ids], 'is_show' => 1], 'store_id,store_name,comment_count,store_phone,address', 0, 20);
            if ($store_list) {
                foreach ($store_list as &$value) {
                    $value['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                    $value['distance'] = '<500m';//距离
                }
                unset($value);
            }
        } else {
            $store_list = [];
        }


        return json([
            'code' => 0,
            'reason' => '操作成功',
            'data' => ['coupon_detail' => $coupon_detail, 'store_list' => $store_list]
        ]);
    }

}