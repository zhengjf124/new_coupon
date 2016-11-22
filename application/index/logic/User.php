<?php

namespace app\index\logic;

use think\controller;
use think\cache;

class User extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 保存短信验证码和接收手机号码
     * @access public
     * @param array $data 需要保存的数据
     * @since 1.0
     * @return bool
     */
    public function saveNoteCode($data)
    {
        /*5分钟内有效*/
        Cache::set('mobile_code_' . $data['mobile'], $data, 300);
        return true;
    }

    /**
     * 获取短信验证码和接收手机号码
     * @access public
     * @since 1.0
     * @return bool
     */
    public function getNoteCode($mobile)
    {
        return Cache::get('mobile_code_' . $mobile);
    }

    /**
     * 删除短信验证码和接收手机号码
     * @access public
     * @since 1.0
     * @return bool
     */
    public function delNoteCode($mobile)
    {
        Cache::rm('mobile_code_' . $mobile);
        return true;
    }

    /**
     * 验证短信验证码是否正确
     * @access public
     * @since 1.0
     * @return bool
     */
    public function checkNoteCode()
    {
        //null
    }

    /**
     * 将对象转换为数组
     * @param $array
     * @return array
     */
    public function objectArray($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->objectArray($value);
            }
        }
        return $array;
    }

    /**
     * 密码加密
     * @access public
     * @param string $string 需要加密字符串
     * @param string $string 密钥
     * @since 1.0
     * @return string
     */
    public function passwordEncryption($string, $pwd_key)
    {
        return sha1(md5($string . '&' . $pwd_key));
    }

    /**
     * 获取IP地址
     * @access public
     * @since 1.0
     * @return string
     */
    function getIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = '';
        return $ip;
    }
}