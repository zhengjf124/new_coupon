<?php
namespace app\index\controller;

class Common extends Api
{

    private $_parameters;//参数

    public function _initialize()
    {

        parent::_initialize();
        $this->_parameters = $this->_createParameters();//获取参数
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

}
