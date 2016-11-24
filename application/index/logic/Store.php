<?php
namespace app\index\logic;

use think\Controller;

class Store extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
    }


    public function toSort($sort_id)
    {
        switch ($sort_id) {
            case 10://离我最近

                break;
            case 20://人气最高

                break;
            case 30://评价最好

                break;
            case 40://价格最高

                break;
            default:

        }
    }

    /**
     * @param array $array
     * @param string $key
     * @param string $order
     */
    private function arraySort($array, $key, $order = 'ASC')
    {
        $arr_num = $arr = [];
        foreach ($array as $k => $v) {
            $arr_num[$k] = $v[$key];
        }

        if ($order == 'ASC') {
            asort($arr_num);
        } else {
            arsort($arr_num);
        }

        foreach ($arr_num as $k => $v) {
            $arr[$k] = $array[$k];
        }

        return $arr;
    }
}