
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


# Default UIUC leave types
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (1, 'Vacation', '12 Month Service Basis:  
Academic staff receive 24 workdays of paid vacation per appointment year at the percentage of their appointment(s)). Vacation is prorated accordingly for partial year appointments.  Vacation is arranged to accommodate the staff member but must be in the best interests of the unit. A maximum accumulation of 48 vacation days may be carried over from one appointment year to the next.', '6D92FF', false, false, true, 384, 0, 1);
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (2, 'Sick', 'Academic staff members (with the exception of medical residents and postdoctoral research associates) who are participants in the State Universities Retirement System or the Federal Retirement System, and who are appointed for at least 50 percent time to a position for which service is expected to be rendered for at least nine consecutive months, will earn sick leave of 12 work days for each appointment year, the unused portion of which shall accumulate without maximum. If these 12 days are fully utilized in any appointment year, up to 13 additional work days will be available for extended sick leave in that appointment year, no part of which 13 days shall be cumulative or eligible for payment. No additional sick leave is earned for a summer appointment. In the case of an appointment for less than a full appointment year, and in the case of a part-time appointment, the 12 days cumulative and the 13 days noncumulative leave shall be prorated.', 'FFB0B0', false, false, true, 9999, 0, 1);
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (7, 'Family and Medical', 'Eligible employees are entitled to up to 12 weeks of family and medical leave at the percentage of their appointments. FMLA is not required to be paid leave, however, employees may use paid vacation and/or sick leave, in accordance with existing University policy, for any portion of this leave. Such leaves will be granted to eligible employees for the birth or adoption of a child; for the care of a child, spouse, or parent who has a serious health condition; or when an employee is unable to perform the function of his or her position due to a serious health condition. Family and medical leave may run concurrently with workers'' compensation. For information regarding specific eligibility criteria and the University policy, employees should contact their department/unit or the Office of Academic Human Resources.

Eligibility: 12 months service to the University (not necessary to be continuous) and at least 1250 hours of service in the last 12 months. ', 'FFF894', true, true, false, 0, 0, 1);
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (8, 'Furlough', 'Furlough', '6EFF86', true, false, false, 0, 0, 1);
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (9, 'Unpaid', 'With appropriate approvals, a member of the academic staff may be granted a leave of absence without pay for a period of one year or less. Such a leave may be renewed in special circumstances, ordinarily for not more than one year.  Leave for family reasons is defined as leave without pay for such purposes as child-rearing and care of an invalid or seriously ill spouse, parent, child, or other close relative or member of the household. It is available to males or females, regardless of marital status, and is applicable to the adoption of children. Leave of Absence without Pay is not normally granted to academic staff on visiting appointments. ', '29D4FF', false, true, false, 0, 0, 1);
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (10, 'Noncumulative Sick', 'If 12 days of sick leave are fully utilized in any appointment year, up to 13 additional work days will be available for extended sick leave in that appointment year, no part of which 13 days shall be cumulative or eligible for payment. No additional sick leave is earned for a summer appointment', 'FFBABA', false, true, false, 0, 0, 1);
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (13, 'Floating Holiday', 'Each fiscal year (July 1 to June 30), 2 non-cumulative floating holidays are available to academic staff who are appointed at least 50%.  Floating holiday days are prorated to the percentage of the employee''s appointment. Floating holidays are not prorated for partial year appointments. ', 'FF8FFF', false, false, false, 0, 0, 25);
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (14, 'Parental', 'Employees with at least 6 continuous months of employment are eligible for paid leave of up to two calendar weeks per academic year immediately following the birth or adoption of the eligible academic staff member''s child.  
Holidays that fall within the two calendar week period do not extend the parental leave.  
Parental leave cannot be used intermittently. Employees who hold only hourly appointments (Academic or Grad hourly) are not eligible for this benefit.  
Leave is counted as part of the 12-week FMLA leave for FMLA-eligible employees (see Family and Medical Leave).  
Parental leave is available to both mothers and fathers.  
Additionally, if both parents are University employees and otherwise eligible, both may receive and use parental leave, but the usage must be concurrent. ', '19733A', false, false, false, 0, 0, 1);
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (15, 'Bereavement Leave', 'Paid leave of up to three workdays due to death of a member of the employee''s immediate family or household including: father, mother, sister, brother, spouse, or child of the employee.  Also included as immediate family is mother-, father-, brother-, sister-, son-, and daughter-in-law, as well as grandchildren and/or grandparents.  Paid leave of one day due to the death of a relative outside the immediate family including aunt, uncle, niece, nephew, or cousin of the employee.rnRelationships existing due to marriage terminate upon the death or divorce of the relative through whom the marriage relationship exists.  Current Illinois state law defines marital status.', '8DB4C2', false, false, false, 0, 0, 1);
INSERT INTO leave_type (`leave_type_id`, `name`, description, calendar_color, special, hidden, roll_over, `max`, default_value, year_type_id) 
	VALUES (16, 'Jury Duty', 'Release time is granted for the duration of jury duty.  The employee may also retain funds paid in compensation for jury duty.', 'ED8E2F', false, false, false, 0, 0, 1);
