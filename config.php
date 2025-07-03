<?php return [
/**
 * 配置文件
 *
 * CoreProtect 查询 Web 界面配置
 * @since 1.0.0
 */

########################
# 账户配置
# 仅有两种访问账户：管理员和普通用户。
# 数组格式如下：[ 用户名, 密码 ]

# 管理员账户可通过网页管理配置。
# 设置密码以启用管理员访问（目前无特殊权限）。
'administrator' => ['管理员', ''],

# 普通用户账户用于查询。
# 设置密码后，查询功能需登录。
'user' => ['用户', ''],


################################
# 数据库/服务器配置
# 如有多个数据库，在下方复制 'server' 数组并重命名。
#   type         = 'mysql' 或 'sqlite'，全部小写
#   path         = SQLite 数据库路径
#   host         = MySQL 数据库主机[:端口]
#   database     = MySQL 数据库名
#   username     = MySQL 用户名
#   password     = MySQL 密码
#   flags        = MySQL 连接 URI 末尾参数（如无需求请勿更改）
#   prefix       = CoreProtect 表前缀
#   preBlockName = 若方块名无冒号(:)时，是否自动加 "minecraft:"
#                  （如数据库含有 MC 1.7 数据，建议关闭）
#   mapLink      = 地图查看链接（如 Dynmap、Overviewer 等）。
#                  {world} 表示世界名，{x} {y} {z} 表示坐标
'database' => [
    'server' => [
        'type'        => 'mysql',
        'path'        => 'path/to/database.db',
        'host'        => 'localhost:3306',
        'database'    => 'minecraft',
        'username'    => 'username',
        'password'    => 'password',
        'flags'       => '',
        'prefix'      => 'co_',
        'preBlockName'=> true,
        'mapLink'     => 'https://localhost:8123/?worldname={world}&mapname=surface&zoom=3&x={x}&y={y}&z={z}'
    ],
],

########################
# 网站配置

# 表单配置
#   count           = 默认查询条数
#   moreCount       = 默认“加载更多”条数
#   max             = 单次查询最大条数
#   pageInterval    = 分页间隔
#   timeDivider     = 表格每隔多少条显示时间分隔
#   locale          = 日期本地化（网站本地化功能即将上线）
#   dateTimeFormat  = 日期格式（参考 https://momentjs.com/docs/#/parsing/string-format/）
#                     （如 'YYYY.MM.DD hh:mm:ss a'）
'form' => [
    'count'         => 30,
    'moreCount'     => 10,
    'max'           => 300,
    'pageInterval'  => 25,
    'timeDivider'   => 300,
    'dateTimeFormat'=> 'LL LTS'
],

# 页面名称与样式配置
#   bootstrap = bootstrap 主题链接，可本地或 CDN。
#               若用 CDN，建议使用带 integrity 和 crossorigin 的 <link> 标签
#               （可在 https://www.bootstrapcdn.com/bootswatch/ 获取主题链接）
#   darkInput = 是否为深色主题（影响输入框颜色）
#   name      = 页面名称
#   href      = 页面名称跳转链接
'page' => [
    'bootstrap' => '<link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/slate/bootstrap.min.css" rel="stylesheet" integrity="sha384-G9YbB4o4U6WS4wCthMOpAeweY4gQJyyx0P3nZbEBHyz+AtNoeasfRChmek1C2iqV" crossorigin="anonymous">',
    'darkInput' => true,
    'name'      => 'CoreProtect 查询 Web 界面',
    'href'      => '/',
    'copyright' => 'Awesome Server, 2020'
],

# 导航栏自定义
#   可在下方添加更多链接
'navbar' => [
    '首页' => 'index.php',
    #'BanManager' => '/banmanager/',
    #'Dynmap' => 'http://127.0.0.1:8123/',
]
];
