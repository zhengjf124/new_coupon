<?php
namespace app\index\controller;

/**
 * 分类
 * Class Category
 * @package app\index\controller
 */
class Category extends Api
{
    public function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 一级分类列表接口 \n
     * URI : /category/onceList
     * @param :
     *  name | type   | null| description
     * ------|--------|-----|-------------
     *  sign | string | 必填 | 签名
     *
     * @return
     *  name  | type  | description
     * -------|-------|----------------------
     *  list  | array | 分类列表二维数组
     *
     * list :
     *  name     | type   | description
     * ----------|--------|----------------------
     *  cat_id   | int    | 分类ID
     *  type_name| string | 分类名称
     *  type_img | string | 分类图片
     *  sub_num  | string | 下级分类数量
     *
     * @note
     *
     */
    public function onceList()
    {
        $cat = new \app\index\model\Category;
        $list = $cat->toSelect('parent_id=0');//查分类表
        $this->_returnData(['list' => $list]);
    }

    /**
     * 二级分类列表接口 \n
     * URI : /category/secondList
     * @param :
     *  name  | type   | null| description
     * -------|--------|-----|-------------
     *  sign  | string | 必填 | 签名
     *  cat_id| int    | 必填 | 分类ID
     *
     * @return
     *  name  | type  | description
     * -------|-------|----------------------
     *  list  | array | 分类列表二维数组
     *
     * list :
     *  name     | type   | description
     * ----------|--------|----------------------
     *  cat_id   | int    | 分类ID
     *  type_name| string | 分类名称
     *  type_img | string | 分类图片
     *  sub_num  | string | 下级分类数量
     *
     * @note
     *
     */
    public function secondList()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_getParams('cat_id'))) {
            $this->_returnError('1', '分类ID不正确');
        }
        $cat = new \app\index\model\Category;
        $list = $cat->toSelect('parent_id=' . $this->_getParams('cat_id'));//查分类表
        $this->_returnData(['list' => $list]);
    }

}