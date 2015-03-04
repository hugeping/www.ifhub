CREATE TABLE IF NOT EXISTS `prefix_shout` 
(
  
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  
	`user_id` int(10) NOT NULL,
  
	`status` tinyint(1) NOT NULL DEFAULT '0',
  
	`text` text NOT NULL,
  
	`mod` tinyint(1) NOT NULL DEFAULT '0',
  
	`datetime` int(11) NOT NULL,
  
	PRIMARY KEY (`id`),
  
	KEY `id` (`id`)
) 

ENGINE = InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `prefix_shout_blacklist` 
(
  `uname` varchar(32) NOT NULL
) 

ENGINE = InnoDB DEFAULT CHARSET=utf8 ;

INSERT INTO `prefix_shout` (`id`, `user_id`, `status`, `text`, `mod`, `datetime`) VALUES (NULL, '1', '0', 'Hi, I hope you enjoy my shoutbox. You can customize it in the file plugins / shoutbox / config / config.php. There you can set it to the location (on the home or in the sidebar). If you want to help me to update the shoutbox, <a href="http://livestreetcms.com/profile/Hellcore/donate/">donate $15 for lemonade</a>. Donations also removed the copyright at the bottom of the shoutbox. <br> <br> Enjoy =)', '0', '1346939602');

INSERT INTO `prefix_shout` (`id`, `user_id`, `status`, `text`, `mod`, `datetime`) VALUES (NULL, '1', '0', 'Привет, надеюсь вам понравится мой чат. Вы можете настроить его в файле plugins / shoutbox / config / config.php. Там же вы можете установить его местоположение ( на главной или в cайдбаре ). Если вы хотите помочь мне в обновление чата, <a href="http://livestreetcms.com/profile/Hellcore/donate/">пожертвуйте $15 на лимонад</a>. Также пожертвования убирают копирайт внизу чата.<br><br>Наслаждайтесь =)', '0', '1346939603');