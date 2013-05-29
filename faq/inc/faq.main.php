<?php

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');
require_once cot_langfile('faq', 'module');
require_once cot_incfile('faq', 'module', 'resources');

$c = cot_import('c', 'G', 'TXT');

$faq_structure = $structure['faq'];
if(!array_key_exists($c, $faq_structure) && !empty($c))
{
	cot_die_message(404);
}

if(empty($c))
{
	$c = '';
	$out['subtitle'] = $L['FAQ'];
}
else
{
	$out['subtitle'] = cot_title('{CATEGORY} - {FAQ}', array('CATEGORY' => $faq_structure[$c]['title'], 'FAQ' => $L['FAQ']));
}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('faq', (!empty($c) ? $c : 'a'));
cot_block($usr['auth_read']);

$faq_order_default = $cfg['faq']['order'];
if($faq_order_default=='recent' || empty($faq_order_default))
{
	$faq_order = "q.question_position ASC, q.question_added DESC";
}
elseif($faq_order_default == 'chron')
{
	$faq_order = "q.question_position ASC, q.question_added ASC";
}
elseif($faq_order_default == 'alpha')
{
	$faq_order = "q.question_position ASC, q.question_text ASC";
}

foreach (cot_getextplugins('faq.main.import') as $pl)
{
	include $pl;
}

if($cache)
{
	$rows = $cache->db->get(FAQ_CACHE_STRUCTURE_PREFIX.$c, FAQ_CACHE_STRUCTURE_REALM);
	if($rows['order'] != $faq_order_default)
	{
		unset($rows);
	}
	else
	{
		unset($rows['order']);
	}
}
if(is_null($rows))
{
	$rows = $db->query("SELECT q.*,u.user_id,u.user_name,u.user_email FROM $db_faq_questions AS q ".
		"LEFT JOIN $db_users AS u ON q.question_userid=u.user_id ".
		"WHERE q.question_cat=? AND q.question_approved=1 ORDER BY $faq_order", $c)->fetchAll();
	$cache && $cache->db->store(FAQ_CACHE_STRUCTURE_PREFIX.$c, $rows + array('order' => $faq_order_default), FAQ_CACHE_STRUCTURE_REALM);
}	
$rowscount = count($rows);
$faq_has_questions = $rowscount > 0 ? true : false;
$faq_structure_toplevel = faq_structure_toplevel();
$subcats = empty($c) ? $faq_structure_toplevel : cot_structure_children('faq', $c);
$subcats_count = empty($c) ? $faq_structure_toplevel : cot_structure_children('faq', $c, false, false);
$faq_has_subcategories = count($subcats_count) > 0 ? true : false; 

foreach (cot_getextplugins('faq.main.first') as $pl)
{
	include $pl;
}

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(cot_tplfile(array('faq', $faq_structure[$c]['tpl'])));

foreach($subcats as $cat)
{	
	$category_itemcount_total = 0;
	$category_children = cot_structure_children('faq', $cat);
	foreach($category_children as $cat_child)
	{
		$category_itemcount_total += (int)$faq_structure[$cat_child]['count'];
	}

	if($cat == $c) 
	{
		continue;
	}

	$show_category = (isset($cfg['faq']['cat_'.$cat]['show_category'])) ? (bool)$cfg['faq']['cat_'.$cat]['show_category'] : (bool)$cfg['faq']['cat___default']['show_category'];
	$show_category = isset($cfg['faq']['cat___default']['show_category']) ? $show_category : TRUE;

	if($show_category)
	{
		$t->assign(array(
			'FAQ_CATEGORY_URL' => cot_url('faq', 'c='.$cat),
			'FAQ_CATEGORY_ID' => (int)$faq_structure[$cat]['id'],
			'FAQ_CATEGORY_DESC' => htmlspecialchars($faq_structure[$cat]['desc']),
			'FAQ_CATEGORY_TITLE' => htmlspecialchars($faq_structure[$cat]['title']),
			'FAQ_CATEGORY_ICON_URL' => $faq_structure[$cat]['icon'],
			'FAQ_CATEGORY_CODE' => htmlspecialchars($cat),
			'FAQ_CATEGORY_QUESTION_COUNT' => (int)$faq_structure[$cat]['count'],
			'FAQ_CATEGORY_QUESTION_COUNT_TOTAL' => (int)$category_itemcount_total,
		));
		$t->parse('MAIN.FAQ_CATEGORIES');
	}
}
if($rowscount>0)
{
	$qorder = 1;
	foreach($rows as $row)
	{
		$question_url = cot_url('faq', 'c='.$c, '#q'.$qorder);
		$question_text = htmlspecialchars($row['question_text']);
		$question_username = (!empty($row['user_name'])) ? $row['user_name'] : $row['question_username']; 
		$question_useremail = (!empty($row['user_email'])) ? $row['user_email'] : $row['question_email'];
		$question_username = htmlspecialchars($question_username);
		$question_useremail = htmlspecialchars($question_useremail);
		$question_cat = htmlspecialchars($row['question_cat']);
		$question_cat_title = htmlspecialchars($faq_structure[$question_cat]['title']);
		$qid = 'q'.$qorder;

		$t->assign(array(
			'FAQ_LIST_QUESTION_ID' => (int)$row['question_id'],
			'FAQ_LIST_QUESTION_ORDER' => $qorder,
			'FAQ_LIST_QUESTION_TEXT' => $question_text,
			'FAQ_LIST_QUESTION_URL' => $question_url,
			'FAQ_LIST_QUESTION_USER_NAME' => $question_username,
			'FAQ_LIST_QUESTION_USER_EMAIL' => $question_useremail,
			'FAQ_LIST_QUESTION_ADDED_DATE' => cot_date('datetime_medium', $row['question_added']),
			'FAQ_LIST_QUESTION_ADDED_TIMESTAMP' => (int)$row['question_added'],
			'FAQ_LIST_QUESTION_CATEGORY_TITLE' => !empty($question_cat) ? $question_cat_title : '',
			'FAQ_LIST_QUESTION_CATEGORY_CODE' => $question_cat,
			'FAQ_LIST_QUESTION_LINK' => cot_rc('faq_list_question_link', 
				array(
					'url' => $question_url, 
					'question' => $question_text,
					'order' => $qorder,
				)),
			'FAQ_LIST_QUESTION_LINK_WITHORDER' => cot_rc('faq_list_question_link_withorder', 
				array(
					'url' => $question_url, 
					'question' => $question_text,
					'order' => $qorder,
				)),
		));
		$t->parse('MAIN.FAQ_LIST_QUESTIONS');

		$t->assign(array(
			'FAQ_QAA_QUESTION_ID' => (int)$row['question_id'],
			'FAQ_QAA_QUESTION_ORDER' => $qorder,
 			'FAQ_QAA_QUESTION_URL' => $question_url,
			'FAQ_QAA_QUESTION_TEXT_RAW' => $question_text,
			'FAQ_QAA_QUESTION_USER_NAME' => $question_username,
			'FAQ_QAA_QUESTION_USER_EMAIL' => $question_useremail,
			'FAQ_QAA_QUESTION_ANSWER_TEXT' => cot_parse($row['question_answer']),
			'FAQ_QAA_QUESTION_ADDED_DATE' => cot_date('datetime_medium', $row['question_added']),
			'FAQ_QAA_QUESTION_ADDED_TIMESTAMP' => (int)$row['question_added'],
			'FAQ_QAA_QUESTION_CATEGORY_TITLE' => !empty($question_cat) ? $question_cat_title : '',
			'FAQ_QAA_QUESTION_CATEGORY_CODE' => $question_cat,
			'FAQ_QAA_QUESTION_TEXT_LINK' => cot_rc('faq_qaa_question_text_link', 
				array(
					'url' => $question_url,
					'question' => $question_text,
					'id' => $qid,
					'order' => $qorder,

				)),
			'FAQ_QAA_QUESTION_TEXT_LINK_WITHORDER' => cot_rc('faq_qaa_question_text_link_withorder', 
				array(
					'url' => $question_url,
					'question' => $question_text,
					'id' => $qid,
					'order' => $qorder,
				)),
			'FAQ_QAA_QUESTION_TEXT' => cot_rc('faq_qaa_question_text', 
				array(
					'question' => $question_text,
					'id' => $qid,
					'order' => $qorder,
				)),
		));
		if($usr['isadmin'])
		{
			$question_delete_url = cot_confirm_url(cot_url('admin', 'm=faq&a=delete&id='.(int)$row['question_id'].'&'.cot_xg()), 'faq', 'faq_confirm_delete');
			$question_update_url = cot_url('admin', 'm=faq&a=manage&id='.(int)$row['question_id']);
			$t->assign(array(
				'FAQ_QAA_QUESTION_DELETE_URL' => $question_delete_url,
				'FAQ_QAA_QUESTION_DELETE_LINK' => cot_rc('faq_admin_delete_link', 
					array(
						'url' => $question_delete_url,
					)),
				'FAQ_QAA_QUESTION_EDIT_LINK' => cot_rc('faq_admin_update_link', 
					array(
						'url' => $question_update_url,
					)),
				'FAQ_QAA_QUESTION_EDIT_URL' => $question_update_url,
			));
			$t->parse('MAIN.FAQ_QUESTIONS_AND_ANSWERS.IS_ADMIN');
		}
		$t->parse('MAIN.FAQ_QUESTIONS_AND_ANSWERS');
		$qorder++;
	}
}
else
{
	$t->parse('MAIN.FAQ_NO_QUESTIONS');
}

foreach (cot_getextplugins('faq.main.main') as $pl)
{
	include $pl;
}

if($usr['auth_write'] && !$faq_structure[$c]['locked'])
{
	$t->assign(array(
		'FAQ_QUESTION_ADD_FORM_SEND' => cot_url('faq', 'm=add&c='.$c),
		'FAQ_QUESTION_ADD_TEXT' => cot_textarea('rquestiontext', '', 8, 120),		
	));
	if($usr['id'] == 0)
	{
		if(!empty($cot_captcha))
		{
			$t->assign(array(
				'FAQ_QUESTION_ADD_VERIFY' => cot_inputbox('text', 'rverify', '', 'size="10" maxlength="20"'),
				'FAQ_QUESTION_ADD_VERIFYIMG' => cot_captcha_generate(),

			));
		}
		$t->assign(array(
			'FAQ_QUESTION_ADD_GUEST_USERNAME' => cot_inputbox('text', 'rquestionname', '', array('size' => '64', 'maxlength' => '100')),
			'FAQ_QUESTION_ADD_GUEST_EMAIL' => cot_inputbox('text', 'rquestionemail', '', array('size' => '64', 'maxlength' => '64')),
		));
		$t->parse('MAIN.FAQ_QUESTION_ADD.GUEST');
	}
	$t->parse('MAIN.FAQ_QUESTION_ADD');
}

$faq_structure_fullpath = array_merge(array(array(cot_url('faq'), $L['FAQ'])), cot_structure_buildpath('faq', $c));
$t->assign(array(
	'FAQ_PATH' => (empty($c)) ? $L['FAQ'] : cot_breadcrumbs($faq_structure_fullpath, false),
	'FAQ_TITLE' => $L['FAQ'],
));

foreach (cot_getextplugins('faq.main.tags') as $pl)
{
	include $pl;
}

cot_display_messages($t);
$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';

