SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `lme_config`;
CREATE TABLE `lme_phpauth_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting` varchar(100) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1


INSERT INTO `lme_phpauth_config`
(`id`, `setting`, `value`) VALUES
(1,'site_name','Wintersky LiveMap Engine'),
('2','site_url','http://livemap.wintersky.ru'),
('3','site_email','livemap_noreply@wintersky.ru'),
('4','cookie_name','kw_livemap_session_hash'),
('5','cookie_path','/'),
('6','cookie_domain',NULL),
('7','cookie_secure','0'),
('8','cookie_http','0'),
('9','site_key','fghuior.)/!/jdUkd8s2!7HVHG7777ghg'),
('10','cookie_remember','+1 month'),
('11','cookie_forget','+30 minutes'),
('12','bcrypt_cost','10'),
('13','table_attempts','lme_phpauth_attempts'),
('14','table_requests','lme_phpauth_requests'),
('15','table_sessions','lme_phpauth_sessions'),
('16','table_users','lme_phpauth_users'),
('17','site_timezone','Europe/Moscow'),
('18','site_activation_page','activateaccount'),
('19','site_password_reset_page','resetpassword'),
('20','smtp','0'),
('21','smtp_host','smtp.example.com'),
('22','smtp_auth','1'),
('23','smtp_username','email@example.com'),
('24','smtp_password','password'),
('25','smtp_port','25'),
('26','smtp_security',NULL),
('27','register_password_min_length','3'),
('28','register_password_max_length','150'),
('29','register_password_strong_regidity','1'),
('30','register_email_min_length','5'),
('31','register_email_max_length','100'),
('32','register_email_use_banlist','1');


DROP TABLE IF EXISTS `lme_attempts`;
CREATE TABLE `attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(39) NOT NULL,
  `count` int(11) NOT NULL,
  `expiredate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lme_requests`;
CREATE TABLE `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `rkey` varchar(20) NOT NULL,
  `expire` datetime NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lme_sessions`;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `expiredate` datetime NOT NULL,
  `ip` varchar(39) NOT NULL,
  `agent` varchar(200) NOT NULL,
  `cookie_crc` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lme_users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
