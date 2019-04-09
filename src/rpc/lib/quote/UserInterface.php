<?php
/**
 * 行情报价
 * Created by PhpStorm.
 * User: wong
 * Date: 2019/4/9
 * Time: 下午2:41
 */

namespace monda\rpc\lib\quote;


interface UserInterface {

    /**
     * unionid登录
     * @param string $unionid
     * @param string $openid
     * @param string $nickname
     * @param string $ip
     * @return mixed
     * @author wong
     */
    public function loginByUnionId(string $unionid, string $openid, string $nickname, string $ip);

    /**
     * 检查手机号是否绑定
     * @param string $account
     * @return mixed
     * @author wong
     */
    public function checkAccount(string $account);

    /**
     * 绑定账号
     * @param string $account
     * @param string $unionid
     * @param string $openid
     * @param string $nickname
     * @param string $ip
     * @return mixed
     * @author wong
     */
    public function bindAccount(string $account, string $unionid, string $openid,  string $nickname,string $ip);

    /**
     * 重置密码
     * @param string $id
     * @param string $password
     * @param string $oldPassword
     * @return mixed
     * @author wong
     */
    public function resetPassword(string $id, string $password, string $oldPassword);
}