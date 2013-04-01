
DROP TABLE IF EXISTS `cot_faq_questions`;
DELETE FROM `cot_structure` WHERE structure_area='faq';
DELETE FROM `cot_cache` WHERE c_realm='faq';