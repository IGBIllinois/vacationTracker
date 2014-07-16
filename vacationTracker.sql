-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: vacationTracker
-- ------------------------------------------------------
-- Server version	5.1.73-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `added_hours`
--

DROP TABLE IF EXISTS `added_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9632 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `added_hours`
--

LOCK TABLES `added_hours` WRITE;
/*!40000 ALTER TABLE `added_hours` DISABLE KEYS */;
/*!40000 ALTER TABLE `added_hours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authen_key`
--

DROP TABLE IF EXISTS `authen_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authen_key` (
  `confirm_key` varchar(32) NOT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `leave_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `cookie_created` tinyint(1) NOT NULL,
  `cookie` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authen_key`
--

LOCK TABLES `authen_key` WRITE;
/*!40000 ALTER TABLE `authen_key` DISABLE KEYS */;
/*!40000 ALTER TABLE `authen_key` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_special_days`
--

DROP TABLE IF EXISTS `calendar_special_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar_special_days`
--

LOCK TABLES `calendar_special_days` WRITE;
/*!40000 ALTER TABLE `calendar_special_days` DISABLE KEYS */;
INSERT INTO `calendar_special_days` VALUES (5,'															End of month pay cycle.							','69AFFF',0,0,15,0,1,0,'Pay Period End',0),(7,'Start of month pay cycle','70B3FF',0,0,16,0,1,0,'Pay Period Start',0),(8,'									','A6CFFF',1,0,0,0,1,7,'Weekend',13),(9,'																											','A6CFFF',1,0,0,0,1,1,'Weekend',13);
/*!40000 ALTER TABLE `calendar_special_days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_info`
--

DROP TABLE IF EXISTS `leave_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `delete_date` datetime NOT NULL,
  `year_info_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`leave_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2710 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_info`
--

LOCK TABLES `leave_info` WRITE;
/*!40000 ALTER TABLE `leave_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_type`
--

DROP TABLE IF EXISTS `leave_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_type`
--

LOCK TABLES `leave_type` WRITE;
/*!40000 ALTER TABLE `leave_type` DISABLE KEYS */;
INSERT INTO `leave_type` VALUES (1,'Vacation','12 Month Service Basis:  \r\nAcademic staff receive 24 workdays of paid vacation per appointment year at the percentage of their appointment(s)). Vacation is prorated accordingly for partial year appointments.  Vacation is arranged to accommodate the staff member but must be in the best interests of the unit. A maximum accumulation of 48 vacation days may be carried over from one appointment year to the next.','6D92FF',0,0,1,384,0,1),(2,'Sick','Academic staff members (with the exception of medical residents and postdoctoral research associates) who are participants in the State Universities Retirement System or the Federal Retirement System, and who are appointed for at least 50 percent time to a position for which service is expected to be rendered for at least nine consecutive months, will earn sick leave of 12 work days for each appointment year, the unused portion of which shall accumulate without maximum. If these 12 days are fully utilized in any appointment year, up to 13 additional work days will be available for extended sick leave in that appointment year, no part of which 13 days shall be cumulative or eligible for payment. No additional sick leave is earned for a summer appointment. In the case of an appointment for less than a full appointment year, and in the case of a part-time appointment, the 12 days cumulative and the 13 days noncumulative leave shall be prorated.','FFB0B0',0,0,1,9999,0,1),(7,'Family and Medical','Eligible employees are entitled to up to 12 weeks of family and medical leave at the percentage of their appointments. FMLA is not required to be paid leave, however, employees may use paid vacation and/or sick leave, in accordance with existing University policy, for any portion of this leave. Such leaves will be granted to eligible employees for the birth or adoption of a child; for the care of a child, spouse, or parent who has a serious health condition; or when an employee is unable to perform the function of his or her position due to a serious health condition. Family and medical leave may run concurrently with workers\' compensation. For information regarding specific eligibility criteria and the University policy, employees should contact their department/unit or the Office of Academic Human Resources.\r\n\r\nEligibility: 12 months service to the University (not necessary to be continuous) and at least 1250 hours of service in the last 12 months. ','FFF894',1,0,0,0,0,1),(8,'Furlough','Furlough','6EFF86',1,0,0,0,0,1),(9,'Unpaid','With appropriate approvals, a member of the academic staff may be granted a leave of absence without pay for a period of one year or less. Such a leave may be renewed in special circumstances, ordinarily for not more than one year.  Leave for family reasons is defined as leave without pay for such purposes as child-rearing and care of an invalid or seriously ill spouse, parent, child, or other close relative or member of the household. It is available to males or females, regardless of marital status, and is applicable to the adoption of children. Leave of Absence without Pay is not normally granted to academic staff on visiting appointments. ','29D4FF',0,1,0,0,0,1),(10,'Noncumulative Sick','If 12 days of sick leave are fully utilized in any appointment year, up to 13 additional work days will be available for extended sick leave in that appointment year, no part of which 13 days shall be cumulative or eligible for payment. No additional sick leave is earned for a summer appointment','FFBABA',0,1,0,0,0,1),(13,'Floating Holiday','Each fiscal year (July 1 to June 30), 2 non-cumulative floating holidays are available to academic staff who are appointed at least 50%.  Floating holiday days are prorated to the percentage of the employee\'s appointment. Floating holidays are not prorated for partial year appointments. ','FF8FFF',0,0,0,0,0,25),(14,'Parental','Employees with at least 6 continuous months of employment are eligible for paid leave of up to two calendar weeks per academic year immediately following the birth or adoption of the eligible academic staff member\'s child.  \r\nHolidays that fall within the two calendar week period do not extend the parental leave.  \r\nParental leave cannot be used intermittently. Employees who hold only hourly appointments (Academic or Grad hourly) are not eligible for this benefit.  \r\nLeave is counted as part of the 12-week FMLA leave for FMLA-eligible employees (see Family and Medical Leave).  \r\nParental leave is available to both mothers and fathers.  \r\nAdditionally, if both parents are University employees and otherwise eligible, both may receive and use parental leave, but the usage must be concurrent. ','19733A',0,0,0,0,0,1),(15,'Bereavement Leave','Paid leave of up to three workdays due to death of a member of the employee\'s immediate family or household including: father, mother, sister, brother, spouse, or child of the employee.  Also included as immediate family is mother-, father-, brother-, sister-, son-, and daughter-in-law, as well as grandchildren and/or grandparents.  Paid leave of one day due to the death of a relative outside the immediate family including aunt, uncle, niece, nephew, or cousin of the employee.rnRelationships existing due to marriage terminate upon the death or divorce of the relative through whom the marriage relationship exists.  Current Illinois state law defines marital status.','8DB4C2',0,0,0,0,0,1),(16,'Jury Duty','Release time is granted for the duration of jury duty.  The employee may also retain funds paid in compensation for jury duty.','ED8E2F',0,0,0,0,0,1);
/*!40000 ALTER TABLE `leave_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_user_info`
--

DROP TABLE IF EXISTS `leave_user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3755 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_user_info`
--

LOCK TABLES `leave_user_info` WRITE;
/*!40000 ALTER TABLE `leave_user_info` DISABLE KEYS */;
INSERT INTO `leave_user_info` VALUES (13,1,0,0,0,0,24,299),(13,2,0,0,0,0,24,300),(13,7,0,1,0,0,24,302),(13,8,0,1,0,0,24,303),(13,9,0,0,0,0,24,304),(13,10,0,1,0,0,24,305),(13,1,0,0,0,0,25,418),(13,2,0,0,0,0,25,419),(13,7,0,1,0,0,25,421),(13,8,0,1,0,0,25,422),(13,9,0,0,0,0,25,423),(13,10,0,1,0,0,25,424),(13,13,0,0,0,0,64,1082),(13,13,0,0,0,0,65,1083),(13,1,0,0,0,0,72,1728),(13,2,0,0,0,0,72,1729),(13,7,0,1,0,0,72,1730),(13,8,0,1,0,0,72,1731),(13,9,0,0,0,0,72,1732),(13,10,0,1,0,0,72,1733),(13,1,0,0,0,0,73,1830),(13,2,0,0,0,0,73,1831),(13,7,0,1,0,0,73,1832),(13,8,0,1,0,0,73,1833),(13,9,0,0,0,0,73,1834),(13,10,0,1,0,0,73,1835),(13,14,0,1,0,0,24,1932),(13,14,0,1,0,0,25,1933),(13,14,0,1,0,0,72,1934),(13,14,0,1,0,0,73,1935),(13,15,0,1,0,0,24,2000),(13,15,0,1,0,0,25,2001),(13,15,0,1,0,0,72,2002),(13,15,0,1,0,0,73,2003),(13,16,0,1,0,0,24,2068),(13,16,0,1,0,0,25,2069),(13,16,0,1,0,0,72,2070),(13,16,0,1,0,0,73,2071),(13,1,0,0,0,0,74,2174),(13,2,0,0,0,0,74,2175),(13,7,0,1,0,0,74,2176),(13,8,0,1,0,0,74,2177),(13,9,0,1,0,0,74,2178),(13,10,0,1,0,0,74,2179),(13,14,0,1,0,0,74,2180),(13,15,0,0,0,0,74,2181),(13,16,0,0,0,0,74,2182),(13,13,0,0,0,0,75,2994),(13,13,0,0,0,0,76,3261),(13,1,0,0,0,0,77,3293),(13,2,0,0,0,0,77,3294),(13,7,0,0,0,0,77,3295),(13,8,0,0,0,0,77,3296),(13,9,0,1,0,0,77,3297),(13,10,0,1,0,0,77,3298),(13,14,0,0,0,0,77,3299),(13,15,0,0,0,0,77,3300),(13,16,0,0,0,0,77,3301);
/*!40000 ALTER TABLE `leave_user_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_period`
--

DROP TABLE IF EXISTS `pay_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_period` (
  `pay_period_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `year_info_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pay_period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=381 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_period`
--

LOCK TABLES `pay_period` WRITE;
/*!40000 ALTER TABLE `pay_period` DISABLE KEYS */;
INSERT INTO `pay_period` VALUES (39,'2010-08-16','2010-09-15',24),(40,'2010-09-16','2010-10-15',24),(41,'2010-10-16','2010-11-15',24),(42,'2010-11-16','2010-12-15',24),(43,'2010-12-16','2011-01-15',24),(44,'2011-01-16','2011-02-15',24),(45,'2011-02-16','2011-03-15',24),(46,'2011-03-16','2011-04-15',24),(47,'2011-04-16','2011-05-15',24),(48,'2011-05-16','2011-06-15',24),(49,'2011-06-16','2011-07-15',24),(50,'2011-07-16','2011-08-15',24),(51,'2011-08-16','2011-09-15',25),(52,'2011-09-16','2011-10-15',25),(53,'2011-10-16','2011-11-15',25),(54,'2011-11-16','2011-12-15',25),(55,'2011-12-16','2012-01-15',25),(56,'2012-01-16','2012-02-15',25),(57,'2012-02-16','2012-03-15',25),(58,'2012-03-16','2012-04-15',25),(59,'2012-04-16','2012-05-15',25),(60,'2012-05-16','2012-06-15',25),(61,'2012-06-16','2012-07-15',25),(62,'2012-07-16','2012-08-15',25),(281,'2010-07-01','2011-06-30',64),(282,'2011-07-01','2012-06-30',65),(283,'2010-08-16','2010-09-15',68),(284,'2010-09-16','2010-10-15',68),(285,'2010-10-16','2010-11-15',68),(286,'2010-11-16','2010-12-15',68),(287,'2010-12-16','2011-01-15',68),(288,'2011-01-16','2011-02-15',68),(289,'2011-02-16','2011-03-15',68),(290,'2011-03-16','2011-04-15',68),(291,'2011-04-16','2011-05-15',68),(292,'2011-05-16','2011-06-15',68),(293,'2011-06-16','2011-07-15',68),(294,'2011-07-16','2011-08-15',68),(295,'2010-08-16','2010-09-15',69),(296,'2010-09-16','2010-10-15',69),(297,'2010-10-16','2010-11-15',69),(298,'2010-11-16','2010-12-15',69),(299,'2010-12-16','2011-01-15',69),(300,'2011-01-16','2011-02-15',69),(301,'2011-02-16','2011-03-15',69),(302,'2011-03-16','2011-04-15',69),(303,'2011-04-16','2011-05-15',69),(304,'2011-05-16','2011-06-15',69),(305,'2011-06-16','2011-07-15',69),(306,'2011-07-16','2011-08-15',69),(307,'2010-08-16','2010-09-15',70),(308,'2010-09-16','2010-10-15',70),(309,'2010-10-16','2010-11-15',70),(310,'2010-11-16','2010-12-15',70),(311,'2010-12-16','2011-01-15',70),(312,'2011-01-16','2011-02-15',70),(313,'2011-02-16','2011-03-15',70),(314,'2011-03-16','2011-04-15',70),(315,'2011-04-16','2011-05-15',70),(316,'2011-05-16','2011-06-15',70),(317,'2011-06-16','2011-07-15',70),(318,'2011-07-16','2011-08-15',70),(319,'2010-08-16','2010-09-15',71),(320,'2010-09-16','2010-10-15',71),(321,'2010-10-16','2010-11-15',71),(322,'2010-11-16','2010-12-15',71),(323,'2010-12-16','2011-01-15',71),(324,'2011-01-16','2011-02-15',71),(325,'2011-02-16','2011-03-15',71),(326,'2011-03-16','2011-04-15',71),(327,'2011-04-16','2011-05-15',71),(328,'2011-05-16','2011-06-15',71),(329,'2011-06-16','2011-07-15',71),(330,'2011-07-16','2011-08-15',71),(331,'2009-08-16','2009-09-15',72),(332,'2009-09-16','2009-10-15',72),(333,'2009-10-16','2009-11-15',72),(334,'2009-11-16','2009-12-15',72),(335,'2009-12-16','2010-01-15',72),(336,'2010-01-16','2010-02-15',72),(337,'2010-02-16','2010-03-15',72),(338,'2010-03-16','2010-04-15',72),(339,'2010-04-16','2010-05-15',72),(340,'2010-05-16','2010-06-15',72),(341,'2010-06-16','2010-07-15',72),(342,'2010-07-16','2010-08-15',72),(343,'2012-08-16','2012-09-15',73),(344,'2012-09-16','2012-10-15',73),(345,'2012-10-16','2012-11-15',73),(346,'2012-11-16','2012-12-15',73),(347,'2012-12-16','2013-01-15',73),(348,'2013-01-16','2013-02-15',73),(349,'2013-02-16','2013-03-15',73),(350,'2013-03-16','2013-04-15',73),(351,'2013-04-16','2013-05-15',73),(352,'2013-05-16','2013-06-15',73),(353,'2013-06-16','2013-07-15',73),(354,'2013-07-16','2013-08-15',73),(355,'2013-08-16','2013-09-15',74),(356,'2013-09-16','2013-10-15',74),(357,'2013-10-16','2013-11-15',74),(358,'2013-11-16','2013-12-15',74),(359,'2013-12-16','2014-01-15',74),(360,'2014-01-16','2014-02-15',74),(361,'2014-02-16','2014-03-15',74),(362,'2014-03-16','2014-04-15',74),(363,'2014-04-16','2014-05-15',74),(364,'2014-05-16','2014-06-15',74),(365,'2014-06-16','2014-07-15',74),(366,'2014-07-16','2014-08-15',74),(367,'2012-07-01','2013-06-30',75),(368,'2013-07-01','2014-06-30',76),(369,'2014-08-16','2014-09-15',77),(370,'2014-09-16','2014-10-15',77),(371,'2014-10-16','2014-11-15',77),(372,'2014-11-16','2014-12-15',77),(373,'2014-12-16','2015-01-15',77),(374,'2015-01-16','2015-02-15',77),(375,'2015-02-16','2015-03-15',77),(376,'2015-03-16','2015-04-15',77),(377,'2015-04-16','2015-05-15',77),(378,'2015-05-16','2015-06-15',77),(379,'2015-06-16','2015-07-15',77),(380,'2015-07-16','2015-08-15',77);
/*!40000 ALTER TABLE `pay_period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shared_calendars`
--

DROP TABLE IF EXISTS `shared_calendars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shared_calendars` (
  `owner_id` int(10) unsigned NOT NULL,
  `viewer_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`owner_id`,`viewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shared_calendars`
--

LOCK TABLES `shared_calendars` WRITE;
/*!40000 ALTER TABLE `shared_calendars` DISABLE KEYS */;
INSERT INTO `shared_calendars` VALUES (13,21),(13,22),(13,23),(13,24),(13,45),(13,46),(13,51),(13,56),(13,57),(18,43),(18,49),(18,52),(22,13),(22,24),(22,25),(22,45),(22,51),(22,53),(22,56),(22,57),(23,42),(24,13),(24,22),(24,23),(24,25),(24,45),(24,46),(24,51),(25,13),(25,22),(25,23),(25,24),(25,45),(25,51),(39,50),(42,18),(42,30),(42,43),(42,47),(42,49),(42,54),(42,55),(43,47),(45,13),(45,22),(45,25),(46,13),(46,22),(46,23),(46,24),(46,45),(46,51),(47,19),(47,30),(47,42),(47,54),(47,58),(51,13),(51,22),(51,24),(51,25),(51,45),(51,53),(54,42),(54,47),(54,49),(57,13),(57,25),(58,19),(58,47);
/*!40000 ALTER TABLE `shared_calendars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status` (
  `status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (1,'New','New leave created by user'),(2,'Approved','Leave was approved by a supervisor'),(3,'Waiting Approval','Waiting on supervisor to approve this elave'),(4,'Deleted','Leaves Deleted'),(5,'Not Approved','Leave was not approved by supervisor');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_computer`
--

DROP TABLE IF EXISTS `user_computer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_computer` (
  `computer_id` int(11) NOT NULL AUTO_INCREMENT,
  `computer_ip` varchar(15) NOT NULL,
  `last_login` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`computer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_computer`
--

LOCK TABLES `user_computer` WRITE;
/*!40000 ALTER TABLE `user_computer` DISABLE KEYS */;
INSERT INTO `user_computer` VALUES (2,'127.0.0.1','2011-02-22 11:32:24',13);
/*!40000 ALTER TABLE `user_computer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_perm`
--

DROP TABLE IF EXISTS `user_perm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_perm` (
  `user_perm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(45) NOT NULL,
  PRIMARY KEY (`user_perm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_perm`
--

LOCK TABLES `user_perm` WRITE;
/*!40000 ALTER TABLE `user_perm` DISABLE KEYS */;
INSERT INTO `user_perm` VALUES (1,'Admin','Administrator User'),(2,'User','Simple User'),(3,'Viewer','View only user');
/*!40000 ALTER TABLE `user_perm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_type`
--

DROP TABLE IF EXISTS `user_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_type` (
  `user_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(45) NOT NULL,
  `hours_block` int(11) NOT NULL,
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_type`
--

LOCK TABLES `user_type` WRITE;
/*!40000 ALTER TABLE `user_type` DISABLE KEYS */;
INSERT INTO `user_type` VALUES (1,'AP','Academic Profesional',4);
/*!40000 ALTER TABLE `user_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `netid` varchar(45) NOT NULL,
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
  `auth_key` varchar(45) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (13,'nevoband','Nevo','Band','','1','1','nevoband@igb.illinois.edu',22,0,100,0,'0','0000-00-00',1,'9a5dfe016de764811bea01757f3d75d4');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `year_info`
--

DROP TABLE IF EXISTS `year_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `year_info` (
  `year_info_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `year_type_id` int(10) unsigned NOT NULL,
  `prev_year_id` int(10) unsigned NOT NULL,
  `next_year_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`year_info_id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `year_info`
--

LOCK TABLES `year_info` WRITE;
/*!40000 ALTER TABLE `year_info` DISABLE KEYS */;
INSERT INTO `year_info` VALUES (24,'2010-08-16','2011-08-15',0,1,72,25),(25,'2011-08-16','2012-08-15',0,1,24,73),(64,'2010-07-01','2011-06-30',0,25,0,65),(65,'2011-07-01','2012-06-30',0,25,64,75),(72,'2009-08-16','2010-08-15',0,1,0,24),(73,'2012-08-16','2013-08-15',0,1,25,74),(74,'2013-08-16','2014-08-15',0,1,73,77),(75,'2012-07-01','2013-06-30',0,25,65,76),(76,'2013-07-01','2014-06-30',0,25,75,0),(77,'2014-08-16','2015-08-15',0,1,74,0);
/*!40000 ALTER TABLE `year_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `year_type`
--

DROP TABLE IF EXISTS `year_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `year_type` (
  `year_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `num_periods` int(11) NOT NULL,
  PRIMARY KEY (`year_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `year_type`
--

LOCK TABLES `year_type` WRITE;
/*!40000 ALTER TABLE `year_type` DISABLE KEYS */;
INSERT INTO `year_type` VALUES (1,'Appointment Year','Appointment Year','2010-08-16','2011-08-15',12),(25,'Fiscal Year','Fiscal Year 12 month period','2010-07-01','2011-06-30',1);
/*!40000 ALTER TABLE `year_type` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-07-16 14:40:09
