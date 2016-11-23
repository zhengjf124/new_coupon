<?php
namespace app\index\controller;

require_once(APP_PATH . 'index/lib/alidayu/TopSdk.php');

class User extends Api
{
    public function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 用户登入接口 \n
     * URI : /user/login
     * @param :
     *     name   |  type  | null | description
     * -----------|--------|------|-------------
     *    sign    | string | 必填  | 签名
     *   mobile   | string | 必填  |  手机号码
     *  password  | string | 必填  |   密码
     *
     * @return
     *  name   |  type  | description
     * --------|--------|-------------
     * passport| string |  用户票据
     *
     */
    public function login()
    {
        if (!preg_match('/^1[34578][0-9]{9}$/', $this->_getParams('mobile'))) {
            $this->_returnError('10008', '手机号码不合法');
        }

        if (!preg_match('/^[\w+]{6,16}$/', $this->_getParams('password'))) {
            $this->_returnError('10004', '密码不合法');
        }

        $model_user = new \app\index\model\User;
        $user_info = $model_user->toFind(['mobile' => $this->_getParams('mobile')], 'user_id,password,pwd_key');
        if (!$user_info) {
            $this->_returnError('10009', '用户名或密码错误');
        }

        $logic_user = new \app\index\logic\User;
        if ($logic_user->passwordEncryption($this->_getParams('password'), $user_info['pwd_key']) != $user_info['password']) {
            $this->_returnError('10009', '用户名或密码错误');
        }

        $model_passport = new \app\index\model\Passport;

        $passport = $model_passport->createPassport($user_info['user_id'], $this->_now);
        $data = [
            'passport' => $passport
        ];
        $this->_returnData($data);
    }


    /**
     * 用户注册接口 \n
     * URI : /user/register
     * @param :
     *  name      |   type   |  null  | description
     * -----------|----------|--------|-------------
     *  sign      |  string  | 必填   |  签名
     *  mobile    |  string  |  必填  |  手机号码
     *  password  |  string  |  必填  |  密码
     *  platfrom  |  string  |  必填  |  平台(android、ios、web、wx)
     *  note_code |   int    |  必填  |  短信验证码
     *
     * @return
     *   name   |  type  | description
     * ---------|--------|--------------
     * passport | string | 用户票据
     *
     */
    public function register()
    {
        if (!preg_match('/^1[34578][0-9]{9}$/', $this->_getParams('mobile'))) {
            $this->_returnError(10008, '手机号码不合法');
        }

        if (!preg_match('/^[\w+]{6,16}$/', $this->_getParams('password'))) {
            $this->_returnError(10004, '密码不合法');
        }

        $note_code = $this->_getParams('note_code');
        if (!preg_match('/^[0-9]{4,6}$/', $note_code)) {
            $this->_returnError(10007, '短信验证码不合法');
        }

        //平台
        $all_from = ['android', 'ios', 'web', 'wx'];
        if (!in_array($this->_getParams('platfrom'), $all_from)) {
            $this->_returnError(10006, '平台不合法');
        }

        $logic_user = new \app\index\logic\User;
        $code_info = $logic_user->getNoteCode($this->_getParams('mobile'));
        if (!$code_info || $note_code != $code_info['code']) {
            $this->_returnError(10011, '短信验证码错误');
        }

        if ($this->_getParams('mobile') != $code_info['mobile']) {
            $this->_returnError(10012, '接收短信的手机号与提交的手机号不匹配');
        }

        $model_user = new \app\index\model\User;

        $user_info = $model_user->toFind(['mobile' => $this->_getParams('mobile')]);
        if ($user_info) {
            $this->_returnError(10003, '手机号码已经被注册');
        }

        $data['pwd_key'] = $this->_getRandomString(8);//获取8位随机数
        $data['nick_name'] = '匿名用户';
        $data['mobile'] = $this->_getParams('mobile');
        $data['password'] = $logic_user->passwordEncryption($this->_getParams('password'), $data['pwd_key']);
        $data['nick_name'] = $this->_getParams('mobile');
        $data['user_from'] = $this->_getParams('platfrom');
        $data['reg_time'] = $this->_now;
        $data['last_login'] = $this->_now;
        $data['login_count'] = 1;
        $data['last_ip'] = $logic_user->getIP();//获取IP地址
        //添加数据库
        $user_id = $model_user->toAdd($data);
        $passport_model = new \app\index\model\Passport;
        //获取、保存登录票据
        $passport = $passport_model->createPassport($user_id, $this->_now);
        //删除保存的短信验证码信息
        $logic_user->delNoteCode($this->_getParams('mobile'));
        $data = [
            'passport' => $passport
        ];
        $this->_returnData($data);
    }

    /**
     * 发送短信验证码 \n
     * URI : /user/sendNoteCode
     * @param :
     *     name   |  type  | null | description
     * -----------|--------|------|-------------
     *  sign      | string | 必填  | 签名
     *   mobile   | string | 必填  |  手机号码
     *
     * @return
     *  name   |  type  | description
     * --------|--------|-------------
     * ------- | -----  |   无
     *
     */
    public function sendNoteCode()
    {
        if (!preg_match('/^1[34578][0-9]{9}$/', $this->_getParams('mobile'))) {
            $this->_returnError('10008', '手机号码不合法');
        }
        $user = new \app\index\logic\User;
        if ($user->getNoteCode($this->_getParams('mobile'))) {
            $this->_returnError(10013, '短信已发送，请勿重复操作');
        }

        $code = rand(100000, 999999);
        date_default_timezone_set('Asia/Shanghai');
        $c = new \TopClient;
        $appkey = '23471823';
        $secret = '33bd1b34ce9ca370adf3d6493e8c4759';
        $c->appkey = $appkey;
        $c->secretKey = $secret;
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        //$req->setExtend("123456");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("大鱼测试");
        $req->setSmsParam('{"code":"' . $code . '","product":"E购联盟"}');
        $req->setRecNum($this->_getParams('mobile'));
        $req->setSmsTemplateCode("SMS_16751324");
        $resp = $c->execute($req);
        //将对象转换成数组
        $resp = $user->objectArray($resp);
        if (isset($resp['result']['err_code']) && $resp['result']['err_code'] == 0) {
            $user->saveNoteCode(array('code' => $code, 'mobile' => $this->_getParams('mobile')));
            $this->_returnData();
        } else if (isset($resp['code']) && $resp['code'] == 15) {
            $this->_returnError(10002, '短信发送超过上限');
        } else {
            $this->_returnError(10014, '短信发送失败，请重试');
        }
    }
}
