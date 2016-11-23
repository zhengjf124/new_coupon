<?php

namespace app\index\model;

use think\Model;
use think\Db;

class Passport extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }


    /**
     * 获取、保存登录票据
     * @param int $user_id 用户ID
     * @param int $now 当前时间
     * @param string $type 用户类型（来源 - app web wx）
     * @return string
     */
    public function createPassport($user_id, $now, $type = 'app')
    {
        $passport = $this->findPassport($user_id);
        if (!$passport) {
            $passport = md5(time() . rand(1, 99999));
            $this->toAdd(['passport' => $passport, 'user_id' => $user_id, 'add_time' => $now, 'type' => $type]);
        }
        return $passport;
    }

    /**
     * 添加
     * @param array $data 需要保存的数据
     */
    protected function toAdd($data)
    {
        Db::name('passport')->insert($data);
        return Db::name('passport')->getLastInsID();
    }

    /**
     * 删除登录票据
     * @param int $user_id 用户ID
     * @return bool
     */
    public function toDelete($user_id)
    {
        return Passport::destroy(['user_id' => $user_id]);
    }

    /**
     * 查询用户票据
     * @param int $user_id 用户ID
     * @return array|false|\PDOStatement|string|Model
     */
    public function findPassport($user_id)
    {
        return Db::name('passport')->where(['user_id' => $user_id])->value('passport');
    }

    /**
     * 查询用户ID
     * @param string $passport 用户票据
     * @return mixed
     */
    public function findUserId($passport)
    {
        return Db::name('passport')->where(['passport' => $passport])->value('user_id');
    }
}
