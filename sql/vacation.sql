
CREATE TABLE `added_hours` (
  `added_hours_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hours` float NOT NULL,
  `user_type_id` int(10) unsigned NOT NULL,
  `pay_period_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `year_info_id` int(10) unsigned NOT NULL,
  `begining_of_pay_period` tinyint(1) NOT NULL,
  PRIMARY KEY (`added_hours_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `authen_key` (
  `confirm_key` varchar(32) NOT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `leave_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `cookie_created` tinyint(1) DEFAULT '0',
  `cookie` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `calendar_special_days` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(45) NOT NULL,
  `color` varchar(45) NOT NULL,
  `blocked` tinyint(1) NOT NULL,
  `month` int(10) unsigned NOT NULL,
  `day` int(10) unsigned NOT NULL,
  `year` int(10) unsigned NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  `week_day` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `leave_info` (
  `leave_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `submit_date` datetime NOT NULL,
  `leave_type_id_special` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `leave_hours` float NOT NULL,
  `delete_date` datetime DEFAULT NULL,
  `year_info_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`leave_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `leave_type` (
  `leave_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `calendar_color` varchar(45) NOT NULL,
  `special` tinyint(1) unsigned zerofill NOT NULL,
  `hidden` tinyint(1) unsigned zerofill NOT NULL,
  `roll_over` tinyint(1) NOT NULL,
  `max` int(10) unsigned NOT NULL,
  `default_value` int(10) unsigned NOT NULL,
  `year_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`leave_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `leave_user_info` (
  `user_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `used_hours` float NOT NULL,
  `hidden` tinyint(1) unsigned zerofill NOT NULL,
  `initial_hours` float NOT NULL,
  `added_hours` float NOT NULL,
  `year_info_id` int(10) unsigned NOT NULL,
  `leave_user_info_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`leave_user_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `pay_period` (
  `pay_period_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `year_info_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pay_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `shared_calendars` (
  `owner_id` int(10) unsigned NOT NULL,
  `viewer_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`owner_id`,`viewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `status` (
  `status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `user_computer` (
  `computer_id` int(11) NOT NULL AUTO_INCREMENT,
  `computer_ip` varchar(15) NOT NULL,
  `last_login` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`computer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `user_perm` (
  `user_perm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(45) NOT NULL,
  PRIMARY KEY (`user_perm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `user_type` (
  `user_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(45) NOT NULL,
  `hours_block` int(11) NOT NULL,
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `netid` varchar(45) NOT NULL,
  `uin` varchar(9) DEFAULT NULL,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `group_id` varchar(45) NOT NULL,
  `user_type_id` varchar(45) NOT NULL,
  `user_perm_id` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `supervisor_id` int(10) unsigned NOT NULL,
  `auto_approve` tinyint(1) NOT NULL,
  `percent` int(10) unsigned NOT NULL,
  `calendar_format` int(10) unsigned NOT NULL,
  `block_days` text NOT NULL,
  `start_date` date NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `banner_include` tinyint(1) DEFAULT '1',
  `auth_key` varchar(45) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `year_info` (
  `year_info_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `year_type_id` int(10) unsigned NOT NULL,
  `prev_year_id` int(10) unsigned NOT NULL,
  `next_year_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`year_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `year_type` (
  `year_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `num_periods` int(11) NOT NULL,
  PRIMARY KEY (`year_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
