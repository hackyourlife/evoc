CREATE TABLE IF NOT EXISTS `%{PREFIX}users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `statsorder` enum('total','ratio','username','correct','wrong') NOT NULL DEFAULT 'ratio',
  `interval` int(11) NOT NULL DEFAULT '2',
  `correct` int(11) NOT NULL DEFAULT '0',
  `wrong` int(11) NOT NULL DEFAULT '0',
  `lastname` varchar(64) NOT NULL,
  `group` enum('user','admin') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `%{PREFIX}voc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `english` varchar(1024) NOT NULL,
  `german` varchar(1024) NOT NULL,
  `creator` int(11) NOT NULL DEFAULT '0',
  `lastmodified` int(11) NOT NULL DEFAULT '0',
  `deleted` enum('no','yes') NOT NULL DEFAULT 'no',
  `deletedby` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `%{PREFIX}weights` (
  `user` int(11) NOT NULL,
  `voc` int(11) NOT NULL,
  `weight` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
