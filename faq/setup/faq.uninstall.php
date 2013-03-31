<?php

$db_faq_questions = (isset($db_faq_questions)) ? $db_faq_questions : $db_x.'faq_questions';

$db->query("DROP TABLE IF EXISTS `".$db_faq_questions."`");
$db->query("DELETE FROM `cot_structure` WHERE structure_area='faq'");
$db->query("DELETE FROM `cot_cache` WHERE c_realm='faq'");