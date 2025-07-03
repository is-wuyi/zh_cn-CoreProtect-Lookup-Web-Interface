<?php
/**
 * 会话类
 *
 * CoreProtect 查询 Web 界面
 * 作者：Simon Chuu
 * 版权所有 © 2015-2020 Simon Chuu
 * 汉化：is-wuyi
 * 汉化项目地址：https://github.com/is-wuyi/zh_cn-CoreProtect-Lookup-Web-Interface
 * 许可证：MIT License
 * 原项目地址：https://github.com/chuushi/CoreProtect-Lookup-Web-Interface
 * 版本：1.0.0
 */

class Session {
    const COOKIE_DURATION = 604800; // Cookie 有效期：1 周
    const PREFIX = "coLogin_";
    const USERNAME = self::PREFIX . "username";
    const PASSWORD = self::PREFIX . "password";
    /** @var array 配置数组 */
    private $config;

    /** @var string 用户名和密码 */
    private $username = null, $password = null;
    /** @var boolean 是否有效、是否为管理员 */
    private $valid, $isAdmin;

    /**
     * 构造函数，初始化会话
     * @param array $config 配置引用
     */
    public function __construct(&$config) {
        $this->config = &$config;
        session_start();

        // 如果 session 未设置但存在 cookie，则尝试用 cookie 恢复会话
        if (!isset($_SESSION[self::USERNAME])) {
            if (isset($_COOKIE[self::USERNAME])) {
                $this->username = $_COOKIE[self::USERNAME];
                $this->password = $_COOKIE[self::PASSWORD];

                if ($this->validCredential()) {
                    // 用 cookie 恢复 session
                    $_SESSION[self::USERNAME] = $this->username;
                    $_SESSION[self::PASSWORD] = $this->password;

                    // 重置 cookie 过期时间
                    setcookie(self::USERNAME, $this->username, time() + self::COOKIE_DURATION);
                    setcookie(self::PASSWORD, $this->password, time() + self::COOKIE_DURATION);
                } else {
                    // 清除无效 cookie
                    $this->logout();
                }
            }
        } else {
            $this->username = $_SESSION[self::USERNAME];
            $this->password = $_SESSION[self::PASSWORD];
        }
    }

    /**
     * 获取当前用户名（若未登录返回 null）
     * @return string|null
     */
    public function getUsername() {
        return $this->validCredential() ? $this->username : null;
    }

    /**
     * 判断当前用户是否有查询权限
     * @return bool
     */
    public function hasLookupAccess() {
        return $this->validCredential() || $this->config['user'][1] === '';
    }

    /**
     * 校验当前用户名和密码是否有效
     * @return bool
     */
    public function validCredential() {
        if (isset($this->valid))
            return $this->valid;

        if (strcasecmp($this->username, $this->config['administrator'][0]) === 0
                && password_verify($this->config['administrator'][1], $this->password)) {
            $this->username = $this->config['administrator'][0];
            $this->valid = true;
            $this->isAdmin = true;
        } elseif (strcasecmp($this->username, $this->config['user'][0]) === 0
                && password_verify($this->config['user'][1], $this->password)) {
            $this->username = $this->config['user'][0];
            $this->valid = true;
            $this->isAdmin = false;
        } else {
            $this->valid = false;
            $this->isAdmin = false;
        }

        return $this->valid;
    }

    /**
     * 登录方法
     * @param string $username 用户名
     * @param string $password 密码
     * @param bool $remember 是否记住登录
     * @return bool
     */
    public function login($username, $password, $remember = false) {
        if ($password == '')
            return false;

        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        
        // 登录失败
        if (!$this->validCredential())
            return false;

        $_SESSION[self::USERNAME] = $this->username;
        $_SESSION[self::PASSWORD] = $this->password;

        if ($remember) {
            setcookie(self::USERNAME, $this->username, time() + self::COOKIE_DURATION);
            setcookie(self::PASSWORD, $this->password, time() + self::COOKIE_DURATION);
        }

        return true;
    }

    /**
     * 注销当前登录
     */
    public function logout() {
        if (isset($_COOKIE[self::USERNAME])) setcookie(self::USERNAME, "", time() - 3600);
        if (isset($_COOKIE[self::PASSWORD])) setcookie(self::PASSWORD, "", time() - 3600);
        unset($_SESSION[self::USERNAME]);
        unset($_SESSION[self::PASSWORD]);
        $this->username = null;
        $this->password = null;
    }
}
