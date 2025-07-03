<?php
/**
 * PDO 封装类
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

class PDOWrapper {
    /** @var array 错误信息和数据库配置 */
    private $error, $dbinfo;

    /**
     * 构造函数，初始化数据库配置
     * @param array $dbinfo 数据库连接配置
     */
    public function __construct($dbinfo) {
        if (isset($dbinfo["type"])) {
            if (($dbinfo["type"] == "mysql"
                && isset($dbinfo["host"])
                && isset($dbinfo["database"])
                && isset($dbinfo["username"])
                && isset($dbinfo["password"])
            ) || ($dbinfo["type"] == "sqlite"
                && isset($dbinfo["path"])
            )) {
                $this->dbinfo = $dbinfo;
                return;
            }
        }
        $this->error = [1, "数据库配置无效"];
    }

    /**
     * 初始化 PDO 连接
     * @return PDO|boolean 初始化后的 PDO 实例或 false
     */
    public function initPDO() {
        if (!isset($this->dbinfo)) {
            $this->error = [1, "数据库配置无效"];
            return false;
        }

        try {
            $pdo = ($this->dbinfo["type"] === "mysql")
                ? new PDO("mysql:charset=utf8;host="
                    . $this->dbinfo["host"]
                    . ";dbname="
                    . $this->dbinfo["database"]
                    . $this->dbinfo["flags"],
                    $this->dbinfo["username"],
                    $this->dbinfo["password"],
                    [PDO::ATTR_PERSISTENT => true]
                )
                : new PDO("sqlite:"
                    .$this->dbinfo["path"]
                );
            // 防止数字被当作字符串处理（MySQL 下会出错）
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
            return $pdo;
        } catch(PDOException $ex) {
            $this->error = [$ex->getCode(), $ex->getMessage()];
            return false;
        }
    }

    /**
     * 获取错误信息
     * @return array|null
     */
    public function error() {
        return isset($this->error) ? $this->error : null;
    }
}
