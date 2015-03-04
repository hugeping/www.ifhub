CREATE TABLE `prefix_topic_view` (
  `topic_id` int(11) unsigned NOT NULL,
  `topic_count_read` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`),
  KEY `topic_count_read` (`topic_count_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
AS SELECT `topic_id`, `topic_count_read` FROM `prefix_topic`;
