<?php

namespace monda\rpc\lib\card;


interface UserInterface {
    /**
     * @param string $unionid
     * @param string $nickname
     * @param string $ip
     * @return mixed
     * 使用unionId登录
     */
    public function loginByUnionId(string $unionid, string $nickname, string $ip);


    /**
     * @param string $account
     * @param string $unionid
     * @param string $nickname
     * @param string $openid
     * @param string $ip
     * @return mixed
     * 绑定账号
     */
    public function bindAccount(string $account, string $unionid, string $nickname, string $openid, string $ip);
}