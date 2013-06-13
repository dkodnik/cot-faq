<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('faq', 'any');
cot_block($usr['isadmin']);

require_once cot_incfile('forms');
require_once cot_incfile('faq', 'module');
require_once cot_langfile('faq', 'module');
require_once cot_incfile('faq', 'module', 'resources');

$filter_types = array(
	'all' => $L['All'],
	'unanswered' => $L['faq_unanswered_questions'],
	'answered' => $L['faq_answered_questions'],
	'approved' => $L['faq_approved_questions'],
	'unapproved_answered' => $L['faq_unapproved_answered_question'],
);
$sort_types = array(
	'id' => $L['Id'],
	'username' => $L['Author'],
	'added' => $L['faq_date_added'],
	'approved' => $L['faq_approved'],
	'position' => $L['faq_position'],
);
$sort_ways = array(
	'asc' => $L['Ascending'],
	'desc' => $L['Descending'],
);

$a = cot_import('a', 'G', 'ALP');
$a = empty($a) || !in_array($a, array('main', 'delete', 'manage')) ? 'main' : $a;
$id = cot_import('id', 'G', 'INT');
$s = cot_import('s', 'G', 'BOL');
$v = cot_import('v', 'G', 'ALP');
$op = cot_import('op', 'G', 'BOL');

foreach (cot_getextplugins('faq.admin.import') as $pl)
{
	include $pl;
}

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['maxrowsperpage']);

if($faq_uses_categories)
{
	$sort_types+= array('cat' => $L['Category'], 'cat_position' => $L['faq_cat_position']);
}

$filter = cot_import('filter', 'R', 'ALP');
$filter = empty($filter) || !array_key_exists($filter, $filter_types) ? 'unanswered' : $filter;
$sorttype = cot_import('sorttype', 'R', 'ALP');
$sortway = mb_strtolower(cot_import('sortway', 'R', 'ALP'));
$sortway = empty($sortway) || !array_key_exists($sortway, $sort_ways) ? 'desc' : $sortway;
$sorttype = (empty($sorttype) || !array_key_exists($sorttype, $sort_types)) ? 'id' : $sorttype;
$sqlsorttype = ($sorttype=='cat_position') ? 'question_position, question_cat' : 'question_'.$sorttype;
if($sorttype == 'cat_position')
{
	$sqlsorttype = 'question_cat, question_position';
}
else
{
	$sqlsorttype = 'question_'.$sorttype;
}

$common_params = '&d='.$durl.'&filter='.$filter.'&sorttype='.$sorttype.'&sortway='.$sortway.'&op='.$op;

foreach (cot_getextplugins('faq.admin.first') as $pl)
{
	include $pl;
}

$adminpath[] = array(cot_url('admin', 'm=extensions'), $L['Extensions']);
$adminpath[] = array(cot_url('admin', 'm=extensions&a=details&mod='.$m), $cot_modules[$m]['title']);
$adminpath[] = array(cot_url('admin', 'm='.$m.$common_params), $L['Administration']);

$t = new XTemplate(cot_tplfile('faq.admin.'.$a, 'module', true));

if($a == 'main')
{

	if($filter == 'all')
	{
		$sqlwhere = "1 ";
	}
	if($filter == 'unanswered')
	{
		$sqlwhere = "question_answer IS NULL ";
	}
	if($filter == 'approved')
	{
		$sqlwhere = "question_approved=1 ";
	}
	if($filter == 'answered')
	{
		$sqlwhere = "question_answer IS NOT NULL ";
	}
	if($filter == 'unapproved_answered')
	{
		$sqlwhere = 'question_approved=0 AND question_answer IS NOT NULL ';
	}

	if($op)
	{
		$limit = "";
	}
	else
	{
		$limit = "LIMIT $d, ".$cfg['maxrowsperpage'];
	}

	$rows = $db->query("SELECT * FROM $db_faq_questions WHERE $sqlwhere ".
		"ORDER BY $sqlsorttype $sortway $limit")->fetchAll();
	$update_position = array();

	foreach (cot_getextplugins('faq.admin.main.first') as $pl)
	{
		include $pl;
	}

	if($rows)
	{
		$items_on_page = 0;
		foreach($rows as $row)
		{
			$question_position = ($row['question_position'] == 0 || $row['question_position'] == 999) ? '' : $row['question_position'];
			$update_position[$row['question_id']] = $row['question_position'];
			$t->assign(array(
				'ADMIN_FAQ_ID' => (int)$row['question_id'],
				'ADMIN_FAQ_QUESTION_TEXT' => htmlspecialchars($row['question_text']),
				'ADMIN_FAQ_QUESTION_CAT' => $structure['faq'][$row['question_cat']]['title'],
				'ADMIN_FAQ_QUESTION_DELETE_URL' => cot_confirm_url(cot_url('admin', 'm=faq&a=delete&id='.(int)$row['question_id'].$common_params.'&'.cot_xg()), 'faq', 'faq_confirm_delete'), 
				'ADMIN_FAQ_QUESTION_MANAGE_URL' => cot_url('admin', 'm=faq&a=manage&id='.$row['question_id'].$common_params),
				'ADMIN_FAQ_QUESTION_POSITION' => cot_inputbox('text', 'rquestionpositions['.$row['question_id'].']', $question_position, array('style' => 'width: 50px;')),
				'ADMIN_FAQ_QUESTION_ANSWER' => htmlspecialchars(strip_tags($row['question_answer'])),
			));
			$t->parse('MAIN.ADMIN_FAQ_QUESTIONS');
			$items_on_page++;
		}
	}
	else
	{
		$t->parse('MAIN.ADMIN_FAQ_NO_QUESTIONS');
	}

	if($v == 'update')
	{
		cot_check_xp();
		$changed = 0;
		$rquestionpositions = cot_import('rquestionpositions', 'P', 'ARR');

		foreach (cot_getextplugins('faq.admin.main.update') as $pl)
		{
			include $pl;
		}

		if(is_array($rquestionpositions) && count($rquestionpositions) > 0)
		{
			foreach($rquestionpositions as $qid => $qpos)
			{
				// Only update position if changed
				$qpos = (empty($qpos)) ? 999 : $qpos;
				if($update_position[$qid]!=$qpos)
				{
					$changed++;
					$db->query("UPDATE $db_faq_questions SET question_position=? WHERE question_id=?", array($qpos, $qid));
				}
			}
		}
		if($changed>0) 
		{
			cot_redirect(cot_url('admin', 'm=faq'.$common_params, '', true));
		}
	}

	$totalitems = $db->query("SELECT COUNT(*) FROM $db_faq_questions WHERE ".$sqlwhere)->fetchColumn();
	$pagenav = cot_pagenav('admin','m=faq'.$common_params, $d, $totalitems, $cfg['maxrowsperpage'], 'd');
	
	foreach (cot_getextplugins('faq.admin.main.main') as $pl)
	{
		include $pl;
	}

	$t->assign(array(
		'ADMIN_FAQ_TOTALITEMS' => $totalitems,
		'ADMIN_FAQ_FORM_UPDATE_URL' => cot_url('admin', 'm=faq&a=main&v=update'.$common_params),
		'ADMIN_FAQ_FORM_FILTER_URL' => cot_url('admin', 'm=faq&a=main'.$common_params),
		'ADMIN_FAQ_ONE_PAGE' => cot_checkbox($op, 'op', $L['faq_admin_onepage'], '', '1', 'faq_admin_onepage'),
		'ADMIN_FAQ_CONFIG_URL' => cot_url('admin', 'm=config&n=edit&o=module&p=faq'),
		'ADMIN_FAQ_CATEGORIES_URL' => cot_url('admin', 'm=structure&n=faq'),
		'ADMIN_FAQ_FILTER' => cot_selectbox($filter, 'filter', array_keys($filter_types), array_values($filter_types), false),
		'ADMIN_FAQ_ORDER' => cot_selectbox($sorttype, 'sorttype', array_keys($sort_types), array_values($sort_types), false), 
		'ADMIN_FAQ_WAY' => cot_selectbox($sortway, 'sortway', array_keys($sort_ways), array_values($sort_ways), false),
		'ADMIN_FAQ_PAGENAV_MAIN' => $pagenav['main'],
		'ADMIN_FAQ_PAGENAV_NEXT' => $pagenav['next'],
		'ADMIN_FAQ_PAGENAV_PREV' => $pagenav['prev'],
		'ADMIN_FAQ_ON_PAGE' => $items_on_page,
	));
}
if($a == 'delete')
{
	cot_check_xg();
	if(faq_question_delete($id))
	{
		cot_message('faq_question_delete_success');
	}
	else
	{
		cot_error('faq_question_delete_fail');
	}
	cot_redirect(cot_url('admin', 'm=faq'.$common_params, '', true));
}
if($a == 'manage')
{

	if($v == 'update')
	{
		cot_check_xp();
		$rquestion = array();
		$rquestion['question_answer'] = cot_import('rquestionanswer', 'P', 'HTM');
		$rquestion['question_id'] = !empty($id) ? $id : '';
		$rquestion['question_cat'] = cot_import('rquestioncat', 'P', 'TXT');
		$rquestion['question_approved'] = cot_import('rquestionapproved', 'P', 'BOL');
		$rquestion['question_text'] = cot_import('rquestiontext', 'P', 'TXT');
		$rquestion['question_username'] = cot_import('rquestionname', 'P', 'TXT', 100);
		$rquestion['question_useremail'] = cot_import('rquestionemail', 'P', 'TXT', 64);
		$rquestion['question_position'] = cot_import('rquestionposition', 'P', 'TXT', 5);

		foreach (cot_getextplugins('faq.admin.manage.update.first') as $pl)
		{
			include $pl;
		}
		if(empty($rquestion['question_answer']) && $rquestion['question_approved'] == 1)
		{
			$rquestion['question_approved'] = 0;
			cot_error('faq_empty_answer_approved');
		}

		cot_check(mb_strlen($rquestion['question_text']) < 2, 'faq_question_tooshort', 'rquestiontext');
		cot_check(mb_strlen($rquestion['question_text']) > 255, 'faq_question_toolong', 'rquestiontext');
		
		foreach (cot_getextplugins('faq.admin.update.error') as $pl)
		{
			include $pl;
		}

		if(!cot_error_found())
		{
			if(!empty($rquestion['question_id']))
			{
				faq_question_update($rquestion);
				foreach (cot_getextplugins('faq.admin.manage.update.done') as $pl)
				{
					include $pl;
				}
			}
			else
			{
				$rquestion['question_added'] = $sys['now'];
				$rquestion['question_userid'] = $usr['id'];
				faq_question_add($rquestion);
				foreach (cot_getextplugins('faq.admin.manage.add.done') as $pl)
				{
					include $pl;
				}
			}

			cot_message('faq_question_successfully_updated');
			cot_redirect(cot_url('admin', 'm=faq'.$common_params, '', true));
		}
	}

	$id = ($id > 0) ? $id : '';
	$adminpath[] = array(cot_url('admin', 'm=faq&a=manage&id='.$id.$common_params), $L['faq_manage']);
	$row = $db->query("SELECT q.*,u.user_name,u.user_id,u.user_email FROM $db_faq_questions AS q ".
		"LEFT JOIN $db_users AS u ON q.question_userid=u.user_id ".
		"WHERE q.question_id=? LIMIT 1", $id)->fetch();

	if($row['user_id'] > 0 || empty($id))
	{
		$question_username = empty($id) ? $usr['name'] : $row['user_name'];
		$question_useremail = empty($id) ? $usr['profile']['user_email'] : $row['user_email'];
		$question_username = htmlspecialchars($question_username);
		$question_useremail = htmlspecialchars($question_useremail); 
	}
	else
	{
		$question_username = cot_inputbox('text', 'rquestionname', $row['question_username'], array('size' => '64', 'maxlength' => '100'));
		$question_useremail = cot_inputbox('text', 'rquestionemail', $row['question_useremail'], array('size' => '64', 'maxlength' => '100'));
	}

	foreach (cot_getextplugins('faq.admin.manage.main') as $pl)
	{
		include $pl;
	}

	$t->assign(array(
		'ADMIN_FAQ_MANAGE_QUESTION_ID' => $id,
		'ADMIN_FAQ_MANAGE_QUESTION_POSITION' => cot_inputbox('text', 'rquestionposition', $row['question_position']==999 ? '' : $row['question_position'], array('style' => 'width: 50px;')),
		'ADMIN_FAQ_MANAGE_QUESTION_CAT' => cot_selectbox_structure('faq', $row['question_cat'], 'rquestioncat', '', false, false), 
		'ADMIN_FAQ_MANAGE_QUESTION_USEREMAIL' => $question_useremail,
		'ADMIN_FAQ_MANAGE_QUESTION_USERNAME' => $question_username,
		'ADMIN_FAQ_MANAGE_QUESTION_TEXT' => cot_textarea('rquestiontext', $row['question_text'], 8, 120, ''),
		'ADMIN_FAQ_MANAGE_QUESTION_APPROVED' => cot_checkbox($row['question_approved'], 'rquestionapproved', '', ''),
		'ADMIN_FAQ_MANAGE_ANSWER_TEXT' => cot_textarea('rquestionanswer', $row['question_answer'], 8, 120, '', 'input_textarea_editor'),
		'ADMIN_FAQ_MANAGE_FORM_URL' => cot_url('admin', 'm=faq&a=manage&v=update&id='.$id.$common_params),
		'ADMIN_FAQ_MANAGE_FORM_DELETE_URL' => cot_url('admin', 'm=faq&a=delete&id='.$id.$common_params.'&'.cot_xg()),
		'ADMIN_FAQ_MANAGE_FORM_UPDATE_URL' => cot_url('admin', 'm=faq&a=manage&v=update&id='.$id.$common_params),
	));
	foreach (cot_getextplugins('faq.admin.manage.tags') as $pl)
	{
		include $pl;
	}
}

$t->assign(array(
	'ADMIN_FAQ_QUESTION_ADD_URL' => cot_url('admin', 'm=faq&a=manage'.$common_params),
));

cot_display_messages($t);

foreach (cot_getextplugins('faq.admin.tags') as $pl)
{
	include $pl;
}
$t->parse('MAIN');
$adminmain = $t->text();

