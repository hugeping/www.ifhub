CREATE TABLE IF NOT EXISTS `prefix_stickytopics_sticky_topic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `target_type` enum('blog','personal','index') NOT NULL DEFAULT 'blog',
  `target_id` int(11) unsigned NOT NULL,
  `topic_id` int(11) unsigned NOT NULL,
  `topic_order` int(10) unsigned NOT NULL DEFAULT '0',
  `show_feed` tinyint(4) NOT NULL DEFAULT '0',
  `metadata` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`),
  KEY `target` (`target_id`,`target_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `prefix_stickytopics_sticky_topic`
  ADD CONSTRAINT `prefix_stickytopics_sticky_topic_fk0` FOREIGN KEY (`topic_id`) REFERENCES `prefix_topic` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE;

