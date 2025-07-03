# 本项目已由社区全面汉化

本仓库在原版基础上进行了如下修改：
- 所有界面、交互、配置、文档均已翻译为中文
- 所有 PHP/JS 代码注释已翻译为中文，且为无注释的函数、变量等补充了中文注释
- readme、changelog、contributing 等文档已全部汉化
- 适当本地化了部分术语和界面风格

---

[CoreProtect 查询 Web 界面 (CoLWI)](https://github.com/chuushi/CoreProtect-Lookup-Web-Interface)
===============================================================================
*CoreProtect 2 的灵活查询 Web 界面。*

![Imgur](https://i.imgur.com/gre6LpC.png)

**版本：** [v1.0.0-pre2](https://github.com/chuushi/CoreProtect-Lookup-Web-Interface/releases/latest)

*[更新日志](changelog.md) | [贡献指南](contributing.md)*

这是一个功能丰富的 Web 应用程序，让你可以高效地查询 CoreProtect 能记录的所有内容。
[CoreProtect 是一个 Minecraft 插件](https://www.spigotmc.org/resources/8631/)，由 Intellii 开发。

本 Web 应用支持像在游戏内一样查询日志数据。
部分过滤器已移植到本插件，例如：

* 按操作类型查询
* 按用户名查询
* 按方块名查询
* 按时间查询

此外，本插件还支持：

* 按坐标和世界查询数据
* 每页显示超过四条结果
* 过滤已回滚的数据
* ~~查看告示牌内容~~（待开发）
* 按关键词搜索

# 部署

## 前置条件

- 一台安装有 **PHP 5.6** 或更高版本的 Web 服务器
    - 需要扩展：PDO，PDO-SQLITE 或 PDO-MYSQL
- 一份由 **CoreProtect 2.12** 或更高版本生成的数据库。
    - 若使用 SQLite 实时查询，Web 服务器需与 Minecraft 服务器在同一台机器上。

## 下载

- **方式一：** `git clone`
    - 便于后续更新 Web 应用。
    - 在 Web 服务器上运行如下命令：
```sh
git clone https://github.com/chuushi/CoreProtect-Lookup-Web-Interface.git
```

- **方式二：** 直接下载
    - 下载[最新发布的 `.zip` 文件](https://github.com/chuushi/CoreProtect-Lookup-Web-Interface/releases/latest)。
    - 将 .zip 文件解压到 Web 服务器目录。

## 配置

编辑 `config.php` 文件中的所有必要配置。所有字段在配置文件中均有注释说明。

## 更新

如果你使用了**方式一**下载 Web 应用，可以运行：
```sh
git stash
git pull
git stash pop
```

- `git stash` 用于暂存未提交的更改
- `git pull` 拉取并更新仓库到最新版本
- `git stash pop` 将暂存的更改应用回仓库

如果在执行 `git stash pop` 后看到如下提示：
```
CONFLICT (content): Merge conflict in config.php
```

则需要手动编辑该文件（查找 `<<<<<<<`、`=======` 和 `>>>>>>>`），然后运行：
```sh
git add config.php
```

如果你使用了**方式二**，则需要重新下载 `.zip` 文件，并手动迁移 `config.php` 文件。

# 插件链接

* [BukkitDev](//dev.bukkit.org/bukkit-plugins/coreprotect-lwi/)
* [Spigot](//www.spigotmc.org/resources/coreprotect-lookup-web-interface.28033/)

~Chuu
