<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

/**
 * 公共类
 */
class Api extends Controller
{
    protected $_now;//当前时间
    private $_parameters;//参数

    public function _initialize()
    {
        parent::_initialize();
        header("Access-Control-Allow-Origin: *");//允许跨域
        $this->_parameters = $this->_createParameters();//获取参数
        $this->_now = time();
    }

    /**
     * 获取参数
     * @param string $key 键
     * @return null
     */
    protected function _getParams($key)
    {
        if (isset($this->_parameters[$key])) {
            return $this->_parameters[$key];
        } else {
            return null;
        }
    }

    /**
     * 获取参数，同时检查令牌正确性
     * @access protected
     * @since 1.0
     * @param string $key 字段名称 用逗号隔开，如 'id,name,sex'
     * @return string
     */
    protected function _createParameters()
    {
        //获取所有数据
        $data = Request::instance()->param();
        if (!isset($data['sign'])) {
            $this->_returnError(10001, 'sign不合法');
        }
        unset($data['sign']);
        $param = $data;
        $param['token'] = AUTH_KEY;
        ksort($param);//根据键值升序排列
        $sign = '';
        foreach ($param as $value) {
            $sign .= $value . '&';
        }
        $sign = trim($sign, '&');
        $sys_sign = md5($sign);
        unset($param['token']);

        $user_sign = Request::instance()->param('sign', '');

        if ($sys_sign != $user_sign) {
            //$this->_returnError(10001, 'sign不合法');
        }

        return $param;
    }


    /**
     * 返回错误信息（JSON）
     * @access protected
     * @param string $code 返回的错误码
     * @param string $message 需要返回提示信息
     * @since 1.0
     * @return
     */
    protected function _returnError($code, $message)
    {
        echo json_encode([
            'code' => $code,
            'reason' => $message
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * 返回数据（JSON）
     * @access protected
     * @param array $data 需要返回的数据
     * @param string $message 需要返回提示信息
     * @since 1.0
     * @return string
     */
    protected function _returnData($data = array(), $message = '操作成功')
    {
        echo json_encode([
            'code' => 0,
            'reason' => $message,
            'data' => $data
        ]);
        die();
    }

    /**
     * 模拟GET表单提交
     * @access protected
     * @param string $url 链接
     * @since 1.0
     * @return string
     */
    protected function _curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error' . curl_error($ch);
        }
        curl_close($ch);
        return $info;
    }

    /**
     * 获取随机字符串
     * @access protected
     * @param int $n 随机数的长度(默认32位)
     * @since 1.0
     * @return string
     */
    protected function _getRandomString($n = 32)
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//输出字符集
        $len = strlen($str) - 1;
        $s = '';
        for ($i = 0; $i < $n; $i++) {
            $s .= $str[rand(0, $len)];
        }
        return $s;
    }

    /**
     * 获取数组的部分指定数据
     * @param array $array
     * @param int $firstRow
     * @param int $listRows
     */
    protected function getArray($array, $firstRow, $listRows)
    {
        $finishRows = $firstRow + $listRows;
        $arr = [];
        for ($i = $firstRow; $i < $finishRows; $i++) {
            if (isset($array[$i])) {
                $arr[] = $array[$i];
            }
        }
        return $arr;
    }

    /**
     * 模拟表单提交
     * @access protected
     * @param string $url 链接
     * @param array $data 数据
     * @since 1.0
     * @return string
     */
    protected function _curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Errno' . curl_error($ch);
        }
        curl_close($ch);
        return $info;
    }

}