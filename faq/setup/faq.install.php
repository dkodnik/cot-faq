<?php

$db_faq_questions = (isset($db_faq_questions)) ? $db_faq_questions : $db_x.'faq_questions';

$db->query("

CREATE TABLE IF NOT EXISTS `".$db_faq_questions."` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_userid` int(11) NOT NULL DEFAULT '0',
  `question_useremail` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `question_username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `question_added` int(11) NOT NULL,
  `question_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `question_answer` text COLLATE utf8_unicode_ci,
  `question_approved` tinyint(1) NOT NULL DEFAULT '0',
  `question_position` smallint(5) NOT NULL DEFAULT '999',
  `question_cat` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`question_id`),
  KEY `question_cat` (`question_cat`),
  KEY `question_approved` (`question_approved`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

");