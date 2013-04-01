<?php

defined('COT_CODE') or die('Wrong URL.');

$c = cot_import('c', 'G', 'TXT');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('faq', (!empty($c) ? $c : 'a'));
cot_block($usr['auth_write']);

if(!empty($c) && $structure['faq'][$c]['locked'])
{
	cot_die_message(404, TRUE);	
}

require_once cot_langfile('faq', 'module');

cot_check_xp();
cot_shield_protect();

$rquestion['question_userid'] = $usr['id'];
$rquestion['question_useremail'] = ($usr['id']==0) ? cot_import('rquestionemail', 'P', 'TXT', 64) : '';
$rquestion['question_username'] = ($usr['id']==0) ? cot_import('rquestionname', 'P', 'TXT', 100) : '';
$rquestion['question_added'] = $sys['now'];
$rquestion['question_text'] = cot_import('rquestiontext', 'P', 'TXT');
$rquestion['question_approved'] = 0;
$rquestion['question_cat'] = $c;

foreach (cot_getextplugins('faq.add.import') as $pl)
{
	include $pl;
}

if($usr['id'] == 0 && !empty($cot_captcha))
{
	$rverify = cot_captcha_validate(cot_import('rverify', 'P', 'TXT'));
	if(!$rverify)
	{
		cot_error('captcha_verification_failed', 'rverify');
	}
}

cot_check(mb_strlen($rquestion['question_text']) > 255, 'faq_question_toolong', 'rquestiontext');
cot_check(mb_strlen($rquestion['question_text']) < 2, 'faq_question_tooshort', 'rquestiontext');

if($usr['id'] == 0)
{
	if(!cot_check_email($rquestion['question_useremail'])) 
	{
		cot_error('aut_emailtooshort', 'rquestionemail');
	}
	cot_check(mb_strlen($rquestion['question_username']) < 2, 'aut_usernametooshort', 'rquestionname');
}

foreach (cot_getextplugins('faq.add.error') as $pl)
{
	include $pl;
}

if(!cot_error_found())
{
	faq_question_add($rquestion);
	foreach (cot_getextplugins('faq.add.done') as $pl)
	{
		include $pl;
	}
	cot_shield_update(15, "New question");
	cot_message('faq_question_successfully_added');
	cot_redirect(cot_url('faq', 'c='.$c, '', true));
}
else
{
	cot_redirect(cot_url('faq'));
}



