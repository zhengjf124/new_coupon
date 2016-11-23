<?php
namespace app\index\controller;

class Common extends Api
{
    protected $user_id;//用户ID

    public function _initialize()
    {
        parent::_initialize();
        $this->user_id = $this->_checkPassport($this->_getParams('passport'));
    }

    /**
     * 验证passport 返回登录的用户ID
     * @return mixed
     */
    private function _checkPassport($passport)
    {
        if (!preg_match('/^[0-9a-zA-Z]{32}$/', $passport)) {
            $this->_returnError('10010', 'passport不合法');
        }
        $passport_model = new \app\index\model\Passport;
        $user_id = $passport_model->findUserId($passport);
        if (preg_match('/^[1-9]\d*$/', $user_id)) {
            return $user_id;
        }
        $this->_returnError('10010', 'passport不合法');
    }
}
