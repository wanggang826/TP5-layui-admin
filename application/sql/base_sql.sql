
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `bs_admin` (
  `admin_id` int(10) UNSIGNED NOT NULL COMMENT '管理员ID',
  `account` varchar(50) NOT NULL COMMENT '帐号',
  `nickname` varchar(50) DEFAULT NULL COMMENT '昵称',
  `password` varchar(50) DEFAULT NULL COMMENT '密码',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机',
  `email` varchar(200) DEFAULT NULL COMMENT '邮箱',
  `headimg` varchar(255) DEFAULT NULL COMMENT '头像',
  `group` int(5) NOT NULL DEFAULT '0' COMMENT '用户组',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态：0-禁用，1-启用',
  `last_login_ip` varchar(20) DEFAULT NULL COMMENT '最后登录IP',
  `last_login_time` int(11) DEFAULT NULL COMMENT '最后登陆时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `login_status` int(1) DEFAULT '0' COMMENT '登录状态0-未登录，1-pc ，2-手机'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员';

--
-- 转存表中的数据 `bs_admin`
--

INSERT INTO `bs_admin` (`admin_id`, `account`, `nickname`, `password`, `phone`, `email`, `headimg`, `group`, `status`, `last_login_ip`, `last_login_time`, `delete_time`, `update_time`, `create_time`, `login_status`) VALUES
(1, 'admin', 'admin', '950avIvOFceTDULwSWoUzts8w8SFmN39ay1aO09Bv9Y8LSQ', '13122228888', '134121669@qq.com', 'admin/1505092277.jpg', 1, 1, '127.0.0.1', 1531365737, NULL, 1516677476, NULL, 1),
(2, 'wanggang', '一鸣', '4c27Txh/zGDqj5MN6grNLSwF/S3zweNdr6+7aWx+hx8u/p0', '13510254650', '9445928@qq.com', NULL, 2, 1, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- 表的结构 `bs_auth`
--

CREATE TABLE `bs_auth` (
  `group_id` int(11) NOT NULL COMMENT '用户组ID',
  `menu_id` int(11) NOT NULL COMMENT '菜单ID',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组权限';

-- --------------------------------------------------------

--
-- 表的结构 `bs_auth_group`
--

CREATE TABLE `bs_auth_group` (
  `group_id` int(11) UNSIGNED NOT NULL COMMENT '用户组标识',
  `group_name` varchar(255) NOT NULL COMMENT '用户组名称',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态：0-禁用，1-启用(禁用该组所有用户)',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组';

--
-- 转存表中的数据 `bs_auth_group`
--

INSERT INTO `bs_auth_group` (`group_id`, `group_name`, `status`, `delete_time`, `create_time`, `update_time`) VALUES
(1, '超级管理员', 1, NULL, NULL, NULL),
(2, '管理员', 1, NULL, NULL, 1497005303),
(12, '测试用户组', 1, 1494814734, 1494765675, 1494814734);

-- --------------------------------------------------------

--
-- 表的结构 `bs_config`
--

CREATE TABLE `bs_config` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `config_mark` varchar(255) NOT NULL DEFAULT '' COMMENT '配置唯一标识',
  `config_name` varchar(255) NOT NULL DEFAULT '' COMMENT '配置名称',
  `config_value` longtext COMMENT '配置值',
  `config_des` varchar(255) NOT NULL DEFAULT '' COMMENT '配置描述',
  `type` varchar(255) NOT NULL DEFAULT 'text' COMMENT '控件类型text,radio,checkbox,button等',
  `type_value` varchar(255) DEFAULT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'base' COMMENT '配置类型base,config,shop等',
  `sort` tinyint(3) NOT NULL DEFAULT '0' COMMENT '排序',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统配置表';

--
-- 转存表中的数据 `bs_config`
--

INSERT INTO `bs_config` (`id`, `config_mark`, `config_name`, `config_value`, `config_des`, `type`, `type_value`, `group`, `sort`, `update_time`, `delete_time`, `create_time`) VALUES
(2, 'WEB_URL', '网站网址', 'http://127.0.0.1', '当前网站域名，请谨慎修改', 'text', NULL, 'base', 1, 1498639320, NULL, NULL),
(3, 'WEB_STATUS', '站点状态', '1', '配置站点是否开启0-关闭，1-开启', 'radio', 'a:2:{i:0;a:2:{s:4:"name";s:6:"关闭";s:5:"value";s:1:"0";}i:1;a:2:{s:4:"name";s:6:"开启";s:5:"value";s:1:"1";}}', 'base', 2, 1498639320, NULL, NULL),
(4, 'WEB_ADMIN_NAME', '后台名称', ' 后台管理', '后台名称', 'text', '', 'base', 3, 1498639320, NULL, NULL),
(5, 'STATIC_URL', '资源服务器', 'http://127.0.0.1', '静态资源服务器，请谨慎修改', 'text', NULL, 'base', 4, 1498639320, NULL, NULL),
(6, 'WEB_DESC', '商城描述', '我还没有.', '888888888', 'textarea', NULL, 'seo', 5, 1498639320, NULL, NULL),
(7, 'WEB_ADMIN_DESC', '后台描述', '后台管理', '后台描述', 'text', NULL, 'seo', 6, 1498639320, NULL, NULL),
(8, 'LOGIN_TIMEOUT', '登录超时', '10000', '登录超时时间', 'text', NULL, 'login', 7, 1498639320, NULL, 1494918314),
(9, 'BEAT_TIME', '心跳时间', '3000', '后台登录超时验证时间间隔', 'text', NULL, 'login', 13, 1498639320, NULL, 1495159728),
(10, 'CHECK_SCENE', '权限认证', 'always', '后台访问权限验证方式', 'radio', 'a:2:{i:0;a:2:{s:4:"name";s:12:"实时验证";s:5:"value";s:6:"always";}i:1;a:2:{s:4:"name";s:12:"登录验证";s:5:"value";s:4:"once";}}', 'base', 14, 1498639320, NULL, 1495854795),
(11, 'DEFAULT_IMG', '默认图片', '/config/1498463598_DEFAULT_IMG1.png', '图片文件不存在时默认显示图片', 'file', NULL, 'base', 16, 1498468706, NULL, 1496893697),
(12, 'WEB_LOGO', '网站LOGO', '/config/2017/06/1496906081WEB_LOGO1.png', '网站logo', 'file', NULL, 'base', 17, 1498468706, NULL, 1496894016);

CREATE TABLE `bs_menu` (
  `menu_id` int(11) NOT NULL COMMENT '主键ID',
  `menu_name` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `menu_des` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单描述',
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '分组模块',
  `controller` varchar(255) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(255) NOT NULL DEFAULT '' COMMENT '方法',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单链接',
  `url_type` int(1) NOT NULL DEFAULT '1' COMMENT '1-内部链接，2-外部链接',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '0-隐藏，1-显示',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `menu_icon` varchar(255) NOT NULL DEFAULT 'columns' COMMENT 'fontAwesome图标，不带fa-前缀',
  `sort` int(3) UNSIGNED ZEROFILL NOT NULL DEFAULT '000' COMMENT '菜单排序',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '修改时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

--
-- 转存表中的数据 `bs_menu`
--

INSERT INTO `bs_menu` (`menu_id`, `menu_name`, `menu_des`, `module`, `controller`, `action`, `url`, `url_type`, `status`, `pid`, `menu_icon`, `sort`, `create_time`, `update_time`, `delete_time`) VALUES
(1, '添加', '', 'admin', 'menus', 'add', '/admin/menus/add.shtml', 1, 0, 2, 'reorder', 102, 0, 1494829675, NULL),
(2, '后台菜单', '', 'admin', 'menus', 'index', '/admin/menus/index.shtml', 1, 1, 17, 'reorder', 101, 0, 1494852545, NULL),
(3, '编辑', '', 'admin', 'menus', 'edit', '/admin/menus/edit.shtml', 1, 0, 2, 'reorder', 000, 0, 1494829619, NULL),
(4, '系统中心', 'hdf', 'admin', 'admins', 'defaluts', '/admin/admins/defaluts.shtml', 1, 1, 0, 'cogs', 500, 0, 1498553828, NULL),
(5, '角色', '', 'admin', 'authGroups', 'index', '/admin/auth_groups/index.shtml', 1, 1, 4, 'users', 000, 0, 1495187942, NULL),
(6, '管理员', '管理员列表', 'admin', 'admins', 'index', '/admin/admins/index.shtml', 1, 1, 4, 'user', 000, 0, 1494404873, NULL),
(7, '添加', '新增管理员', 'admin', 'admins', 'add', '/admin/admins/add.shtml', 1, 0, 6, 'user-plus', 000, 0, 1494829689, NULL),
(8, '设置用户组', '设置用户组', 'admin', 'admins', 'setgroup', '/admin/admins/setgroup.shtml', 1, 0, 6, 'wrench', 000, 0, 1495008212, NULL),
(9, '添加', '新增用户组', 'admin', 'authGroups', 'add', '/admin/auth_groups/add.shtml', 1, 0, 6, 'users', 000, 0, 1494829733, NULL),
(10, '删除', '删除管理员', 'admin', 'admins', 'del', '/admin/admins/del.shtml', 1, 0, 6, 'trash', 000, 0, 1494829706, NULL),
(11, '编辑', '编辑', 'admin', 'admins', 'edit', '/admin/admins/edit.shtml', 1, 0, 6, 'pencil-square-o', 000, 0, 1494829662, NULL),
(12, '编辑', '编辑用户组', 'admin', 'authGroups', 'edit', '/admin/auth_groups/edit.shtml', 1, 0, 5, 'pencil-square-o', 000, 0, 1494829743, NULL),
(13, '授权', '用户组权限设置', 'admin', 'authGroups', 'setAuth', '/admin/auth_groups/setauth.shtml', 1, 0, 5, 'wrench', 000, 0, 1495073657, NULL),
(14, '删除', '删除用户组', 'admin', 'authGroups', 'del', '/admin/auth_groups/del.shtml', 1, 0, 5, 'trash', 000, 0, 1495073677, NULL),
(15, '启|禁用', '启用|禁用管理员', 'admin', 'admins', 'change_status', '/admin/admins/change_status.shtml', 1, 0, 6, 'tags', 000, 0, 1494829720, NULL),
(16, '启|禁用', '启用|禁用用户组', 'admin', 'authGroups', 'change_status', '/admin/auth_groups/change_status.shtml', 1, 0, 5, 'tags', 000, 0, 1495090239, NULL),
(17, '开发者配置', '', 'admin', 'menus', 'defaults', '/admin/menus/defaults.shtml', 1, 1, 0, 'gg', 003, 0, 1514005467, NULL),
(20, '禁|启用', '', 'admin', 'menus', 'changeStatus', '/admin/menus/changestatus.shtml', 1, 0, 2, 'tags', 000, 0, 1495193847, NULL),
(21, '菜单图标', '', 'admin', 'menus', 'fontawesome', '/admin/menus/fontawesome.shtml', 1, 0, 2, 'puzzle-piece', 000, 0, 1495193875, NULL),
(22, '删除', '删除菜单', 'admin', 'menus', 'del', '/admin/menus/del.shtml', 1, 0, 2, 'trash', 000, 0, 1495683045, NULL),
(23, '后台首页', '首台首页', 'admin', 'index', 'index_main', '/admin/index/index_main.shtml', 1, 0, 0, 'calendar-plus-o', 000, 0, 1498465217, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bs_admin`
--
ALTER TABLE `bs_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bs_auth_group`
--
ALTER TABLE `bs_auth_group`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `bs_config`
--
ALTER TABLE `bs_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_menu`
--
ALTER TABLE `bs_menu`
  ADD PRIMARY KEY (`menu_id`);

ALTER TABLE `bs_admin`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员ID', AUTO_INCREMENT=3;
ALTER TABLE `bs_auth_group`
  MODIFY `group_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户组标识', AUTO_INCREMENT=24;
ALTER TABLE `bs_config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=143;
ALTER TABLE `bs_menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=287;