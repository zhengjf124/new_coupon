<?php

namespace app\index\controller;
/**
 * Class pay
 * @package app\index\controller
 */
class Pay extends Member
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 去付款 \n
     * URI : /pay/to
     * @param :
     *  name      | type   | null | description
     * -----------|--------|------|-------------
     *  sign      | string | 必填 | 签名
     *  passport  | string | 必填 | 用户登录凭证
     *  pay_id    | int    | 必填 | 支付类型 10-微信支付 20-支付宝支付 30-银行卡支付 40-余额支付
     *  order_sn  | string | 必填 | 订单编号
     *
     * @return
     *  name     |  type  | description
     * ----------|--------|----------------------
     *  order_sn | string |  订单编号
     *
     */
    public function toPay()
    {
        $order_sn = $this->_getParams('order_sn');
        if (preg_match('/^/d{20}$/', $order_sn)) {
            $this->_returnError(10061, '订单编号不合法');
        }

        $pay_id = $this->_getParams('pay_id');
        if (!in_array($pay_id, [10, 20, 30, 40])) {
            $this->_returnError(10063, '支付类型不合法');
        }

        $order_model = new \app\index\model\Order;

        $where = ['user_id' => $this->user_id, 'order_sn' => $order_sn];
        $field = 'order_id,pay_status';
        $order_info = $order_model->toFindOrder($where, $field); //获取订单详情
        if (empty($order_info)) {
            $this->_returnError(10062, '订单不存在');
        }

        if ($order_info['pay_status'] == 1) {
            $this->_returnError(10064, '该订单已完成支付，请勿重复支付');
        }

        switch ($pay_id) {
            case 10: //微信支付

                break;
            case 20: //支付宝支付
                $string =
                    '{
                        "out_trade_no":"20150320010101001",
                        "scene":"bar_code,wave_code",
                        "auth_code":"28763443825664394",
                        "subject":"Iphone6 16G",
                        "seller_id":"2088102146225135",
                        "total_amount":88.88,
                        "discountable_amount":8.88,
                        "undiscountable_amount":80.00,
                        "body":"Iphone6 16G",
                        "goods_detail":[{
                            "goods_id":"apple-01",
                            "alipay_goods_id":"20010001",
                            "goods_name":"ipad",
                            "quantity":1,
                            "price":2000,
                            "goods_category":"34543238",
                            "body":"特价手机",
                            "show_url":"http://www.alipay.com/xxx.jpg"
                        }],
                        "operator_id":"yx_001",
                        "store_id":"NJ_001",
                        "terminal_id":"NJ_T_001",
                        "alipay_store_id":"2016041400077000000003314986",
                        "extend_params":{
                            "sys_service_provider_id":"2088511833207846",
                            "hb_fq_num":"3",
                            "hb_fq_seller_percent":"100"
                        },
                        "timeout_express":"90m",
                        "royalty_info":{
                            "royalty_type":"ROYALTY",
                            "royalty_detail_infos":[{
                                "serial_no":1,
                                "trans_in_type":"userId",
                                "batch_no":"123",
                                "out_relation_id":"20131124001",
                                "trans_out_type":"userId",
                                "trans_out":"2088101126765726",
                                "trans_in":"2088101126708402",
                                "amount":0.1,
                                "desc":"分账测试1",
                                "amount_percentage":"100"
                            }]
                        },
                        "sub_merchant":{
                            "merchant_id":"19023454"
                            }
                        }
                    }';
                break;
            case 30: //银行卡支付

                break;
            case 40:
                //余额支付

                break;
            default:
                $this->_returnError(10063, '支付类型不合法');
        }


        $this->_returnData();
    }

}

