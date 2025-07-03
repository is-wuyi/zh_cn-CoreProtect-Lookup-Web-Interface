<?php
/**
 * 汉化：is-wuyi
 * 汉化项目地址：https://github.com/is-wuyi/zh_cn-CoreProtect-Lookup-Web-Interface
 *
 * Lookup JSON
 *
 * Returns query results as a JSON file
 *
 * CoreProtect Lookup Web Interface
 * @author Simon Chuu
 * @copyright 2015-2020 Simon Chuu
 * @license MIT License
 * @link https://github.com/chuushi/CoreProtect-Lookup-Web-Interface
 * @since 1.0.0
 */

require_once 'res/php/StatementPreparer.class.php';
require_once 'res/php/PDOWrapper.class.php';
require_once 'res/php/Session.class.php';
$config = require_once 'config.php';

$return = [["time" => microtime(true)]];

/**
 * @param PDO $pdo
 */
function pdoError($pdo) {
    $return[0]["status"] = 2;
    $return[0]["code"] = $pdo->errorInfo()[0];
    $return[0]["driverCode"] = $pdo->errorInfo()[1];
    $return[0]["reason"] = $pdo->errorInfo()[2];
    exit();
}

register_shutdown_function(function () {
    global $return;

    // Set type to application/json
    header('Content-type:application/json;charset=utf-8');

    if(!isset($return[0]["status"]))
        $return[0]["status"] = -1;
    $return[0]["duration"] = microtime(true) - $return[0]["time"];
    echo json_encode($return);
});

$checkInputQuery = !isset($_REQUEST['offset']);
$session = new Session($config);

// Allow for immediate login through request headers
if (array_key_exists('username', $_REQUEST) && array_key_exists('password', $_REQUEST)) {
    if (!$session->login($_REQUEST['username'], $_REQUEST['password'])) {
        http_response_code(401);
        $return[0]["status"] = 1;
        $return[0]["code"] = "访问被拒绝";
        $return[0]["reason"] = "登录凭据不正确。";
        exit();
    }
}

if (!$session->hasLookupAccess()) {
    http_response_code(401);
    $return[0]["status"] = 1;
    $return[0]["code"] = "访问被拒绝";
    $return[0]["reason"] = "您必须先登录才能访问数据。";
    exit();
}

$serverName = $_REQUEST['server'];
if ($serverName == null) {
    $return[0]["status"] = 1;
    $return[0]["code"] = "请求错误";
    $return[0]["reason"] = "未指定服务器。";
    exit();
}

$server = $config['database'][$serverName];
if (!isset($server)) {
    $return[0]["status"] = 1;
    $return[0]["code"] = "配置错误";
    $return[0]["reason"] = "指定的服务器 '$serverName' 未配置。";
    exit();
}

$flags = isset($_REQUEST['flags']) ? $_REQUEST['flags'] : ($server['preBlockName'] ? 1 : 0);
$wrapper = new PDOWrapper($server);
$pdo = $wrapper->initPDO();

if (!$pdo) {
    $return[0]["status"] = 1;
    $return[0]["code"] = $wrapper->error()[0];
    $return[0]["reason"] = $wrapper->error()[1];
    exit();
}

if (($flags & StatementPreparer::FLAG_USE_BLOCKDATA_TABLE_DEFINED) === 0) {
    try {
        if ($pdo->query("SELECT 1 FROM " . $server['prefix'] . "blockdata_map LIMIT 1") === false)
            $flags |= StatementPreparer::FLAG_USE_BLOCKDATA_TABLE_NO;
        else
            $flags |= StatementPreparer::FLAG_USE_BLOCKDATA_TABLE_YES;
    } catch (Exception $e) {
        $flags |= StatementPreparer::FLAG_USE_BLOCKDATA_TABLE_NO;
    }

    $return[0]['flags'] = $flags;
}

$prep = new StatementPreparer($server['prefix'], $_REQUEST, $config['form']['count'], $config['form']['moreCount'], $flags);


// Check if where parameters exist
if ($checkInputQuery) {
    $checkStr = $prep->prepareCheck();

    if ($checkStr !== '') {
        $check = $pdo->prepare($checkStr);
        if (!$check) {
            pdoError($pdo);
        }

        $w = $prep->getW();
        $u = $prep->getU();
        $m = $prep->getB();
        $e = $prep->getE();

        if ($check->execute($prep->getParams())) {
            while($r = $check->fetch(PDO::FETCH_ASSOC)) {
                switch ($r['table']) {
                    case 'world':
                        $params = &$w;
                        $isUser = false;
                        break;
                    case 'user':
                        $params = &$u;
                        $isUser = true;
                        break;
                    case 'material':
                        $params = &$m;
                        $isUser = false;
                        break;
                    case 'entity':
                        $params = &$e;
                        $isUser = false;
                        break;
                    default:
                        continue;
                }

                if ($params !== null && (
                    ($key = array_search($r['name'], $params)) !== false
                    || $r['uuid'] !== null && ($key = array_search($r['uuid'], $params)) !== false)
                )
                    unset($params[$key]);
                elseif ($isUser && $e !== null && ($key = array_search($r['name'], $e)) !== false)
                    unset($e[$key]);
            }
        }

        $wSize = is_array($w) && sizeof($w) !== 0;
        $uSize = is_array($u) && sizeof($u) !== 0;
        $mSize = is_array($m) && sizeof($m) !== 0;
        $eSize = is_array($e) && sizeof($e) !== 0;

        if ($wSize || $uSize || $mSize || $eSize) {
            $return[0]["status"] = 1;
            $return[0]["code"] = "未知内容于"
                . ($wSize ? " '世界'" : '')
                . ($uSize ? " '用户'" : '')
                . ($mSize ? " '方块/物品'" : '')
                . ($eSize ? " '实体'" : '');
            $return[0]["reason"]
                = ($wSize ? "世界: '" . join("', '", $w) . "';" : '')
                . ($uSize ? "用户: '" . join("', '", $u) . "';" : '')
                . ($mSize ? "方块/物品: '" . join("', '", $m) . "';" : '')
                . ($eSize ? "实体: '" . join("', '", $e) . "';" : '');
            exit();
        }
    }
}

// Lookup
$lookup = $pdo->prepare($prep->prepareStatementData());

if (!$lookup) {
    pdoError($pdo);
}

if ($lookup->execute($prep->getParams())) {
    $return[0]["status"] = 0;
    if (!isset($_REQUEST['offset']) && $server['mapLink']) $return[0]["mapHref"] = $server['mapLink'];

    $return[1] = [];
    while($r = $lookup->fetch(PDO::FETCH_ASSOC)) {
        // Treat numbers as integers
        if (isset($r["rowid"])) {
            $r["id"] = intval($r["rowid"]);
            unset($r["rowid"]);
        } else {
            $r["id"] = intval($r["id"]);
        }
        $r["time"] = intval($r["time"]);
        if ($r["action"] !== null) {
            $r["action"] = intval($r["action"]);
        }
        if ($r["world"] !== null) {
            $r["x"] = intval($r["x"]);
            $r["y"] = intval($r["y"]);
            $r["z"] = intval($r["z"]);
        }
        if ($r["amount"] !== null) {
            $r["amount"] = intval($r["amount"]);
        }
        if ($r["rolled_back"] !== null) {
            $r["rolled_back"] = intval($r["rolled_back"]);
        }
        $return[1][] = $r;
    }
} else {
    $return[0]["status"] = 2;
    $return[0]["code"] = $lookup->errorInfo()[0];
    $return[0]["driverCode"] = $lookup->errorInfo()[1];
    $return[0]["reason"] = $lookup->errorInfo()[2];
    exit();
}
