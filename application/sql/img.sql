DROP TABLE IF EXISTS `bs_img`;
CREATE TABLE IF NOT EXISTS `bs_img`(
  `id` INT unsigned auto_increment,
  `type` INT(1) NOT null DEFAULT 0 COMMENT '类型 0 其他图片 1 首页banner',
  `status` INT(1) NOT NULL DEFAULT 1 COMMENT '状态 0 禁用 1启用',
  `img` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '图片',
  `url` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '链接',
  `title` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '标题',
  `des` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '简要描述',
  `create_time` INT(11) NOT NULL DEFAULT 0 COMMENT '生成时间',
  `update_time` INT(11) DEFAULT 0 COMMENT '更新时间',
  `delete_time` INT(11) COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT ='图片表';

