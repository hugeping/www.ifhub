CREATE TABLE IF NOT EXISTS `prefix_openid` (
  `user_id` int(11) unsigned NOT NULL,
  `openid` varchar(250) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`openid`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_openid_tmp` (
  `key` varchar(32) NOT NULL,
  `openid` varchar(250) NOT NULL,
  `date` datetime NOT NULL,
  `confirm_mail_key` varchar(32) NOT NULL,
  `confirm_mail` varchar(100) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `prefix_openid`
  ADD CONSTRAINT `prefix_openid_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
ALTER TABLE  `prefix_user` CHANGE  `user_mail`  `user_mail` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;