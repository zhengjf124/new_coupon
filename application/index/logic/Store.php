<?php
namespace app\index\logic;

use think\Controller;

class Store extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
    }


    public function toSort($array, $sort_id)
    {
        switch ($sort_id) {
            case 10://离我最近
                return $this->arraySort($array, 'distance');
                break;
            case 20://人气最高
                return $this->arraySort($array, 'sales', 'DESC');
                break;
            case 30://评价最好
                return $this->arraySort($array, 'comment_level', 'DESC');
                break;
            /*            case 40://价格最高
                            return $this->arraySort($array, '', 'DESC');
                            break;
                        case 50://价格最低
                            return $this->arraySort($array, '', 'ASC');*/
            default:
                return $array;
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
            $arr_num["$k"] = $v[$key];
        }
        unset($k);
        unset($v);

        if ($order == 'ASC') {
            asort($arr_num);
        } else {
            arsort($arr_num);
        }

        foreach ($arr_num as $k => $v) {
            $arr[] = $array["$k"];
        }

        return $arr;
    }
}