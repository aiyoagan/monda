<?php

namespace monda\rpc\lib\pvc;


interface UserInterface {
    /**
     * @param string $account
     * @param string $password
     * @param string $ip
     * @return mixed
     */
    public function register(string $account, string $password, string $ip);


    /**
     * @param string $account
     * @return mixed
     * 检查手机是否注册过
     */
    public function checkAccountIsExist(string $account);


    /**
     * @param $account
     * @param $password
     * @param $ip
     * @return mixed
     * 账号登录
     */
    public function loginByAccount(string $account, string $password, string $ip);


    /**
     * @param string $name
     * @param string $password
     * @param string $ip
     * @return mixed
     * 名称登录
     */
    public function loginByName(string $name, string $password, string $ip);


    /**
     * @param string $unionId
     * @param string $nickname
     * @param string $ip
     * @return mixed
     * 微信登录
     */
    public function loginByUnionId(string $unionId, string $nickname, string $ip);


    /**
     * @param string $id
     * @return mixed
     * 判断是否登录
     */
    public function isSetPassword(string $id);


    /**
     * @param string $id
     * @param string $account
     * @return mixed
     * 修改手机
     */
    public function changeAccount(string $id, string $account);

    /**
     * @param $account
     * @param $password
     * @param $passwordConfirm
     * @return mixed
     * 重置密码
     */
    public function resetPassword(string $account, string $password, string $passwordConfirm);


    /**
     * @param string $id
     * @param string $password
     * @param string $oldPassword
     * @param string $passwordConfirm
     * @return mixed
     * 修改密码
     */
    public function changePassword(string $id, string $oldPassword, string $password, string $passwordConfirm);


    /**
     * @param string $id
     * @param string $password
     * @param string $passwordConfirm
     * @return mixed
     * 设置密码
     */
    public function setPassword(string $id, string $password, string $passwordConfirm);


    /**
     * @param string $id
     * @param string $password
     * @return mixed
     * 检查密码
     */
    public function checkOldPassword(string $id, string $password);
}