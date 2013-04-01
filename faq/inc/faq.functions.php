<?php

define('FAQ_EMPTY_CAT_CODE', '__none__');
define('FAQ_CACHE_STRUCTURE_PREFIX', 'faq_structure_');
define('FAQ_CACHE_STRUCTURE_REALM', 'faq');

$db_faq_questions = (isset($db_faq_questions)) ? $db_faq_questions : $db_x.'faq_questions';

$faq_empty_category = array(
	FAQ_EMPTY_CAT_CODE => 
      array(
      'path' => '',
      'tpath' =>'',
      'rpath' =>'9999999',
      'id' => '9999999',
      'tpl' => '__no_category__',
      'title' => '__No Category__',
      'desc' => '',
      'icon' => '',
      'locked' =>'0',
      'count' => '0',
));
$faq_uses_categories = count($structure['faq'])>0 ? true : false;
$structure['faq'] = !empty($structure['faq']) ? $structure['faq'] : array();
$structure['faq'] = array_merge($faq_empty_category, $structure['faq']);

function faq_structure_toplevel()
{
	global $structure;
	$top = array();

	foreach($structure['faq'] as $catname => $data)
	{
		$pathes = explode('.', $data['path']);
		$depth = count($pathes);
		if($depth==1 && $catname!=FAQ_EMPTY_CAT_CODE)
		{
			$top[] = $catname;
		}
	}
	return $top;
}

function faq_structure_cache_remove($cat)
{
	global $cache;
	$cat = ($cat == FAQ_EMPTY_CAT_CODE) ? '' : $cat;
	$cache && $cache->db->remove(FAQ_CACHE_STRUCTURE_PREFIX.$cat, FAQ_CACHE_STRUCTURE_REALM);
}

function faq_structure_cache_remove_all()
{
	global $cache;
	$cache && $cache->db->clear(FAQ_CACHE_STRUCTURE_REALM);
}

function faq_structure_adjust_count($catcode, $approved_status)
{
	global $db_structure, $db;
	if($approved_status == 0)
	{
		$count = "-1";
	}
	if($approved_status == 1)
	{
		$count = "+1";
	}
	$db->query("UPDATE $db_structure SET structure_count=structure_count{$count} WHERE structure_area=? AND structure_code=?", 
		array('faq', $catcode));
}

function faq_config_order()
{
	global $L;
	$order_options = array(
		'recent' => $L['faq_config_recent'],
		'alpha' => $L['faq_config_alpha'],
		'chron' => $L['faq_config_chron'],
	);
	$L['cfg_order_params'] = array_values($order_options);
	return array_keys($order_options);
}

function faq_question_add($rquestion)
{
	global $db_faq_questions, $cache, $db, $db_structure;
	if(empty($rquestion['question_answer']))
	{
		unset($rquestion['question_answer']);
	}
	$rquestion['question_position'] = (empty($rquestion['question_position'])) ? 999 : $rquestion['question_position'];
	$rquestion['question_cat'] = $rquestion['question_cat'] == FAQ_EMPTY_CAT_CODE ? '' : $rquestion['question_cat'];
	foreach (cot_getextplugins('faq.add.add.query') as $pl)
	{
		include $pl;
	}
	if($db->insert($db_faq_questions, $rquestion))
	{
		foreach (cot_getextplugins('faq.add.add.done') as $pl)
		{
			include $pl;
		}
		if($rquestion['question_approved'] == 1)
		{
			faq_structure_cache_remove($rquestion['question_cat']);
			faq_structure_adjust_count($rquestion['question_cat'], 1);
			$cache && $cache->db->remove('structure', 'system');			
		}
		return $db->lastInsertID();
	}	
	else
	{
		FALSE;
	}
}

function faq_question_update($rquestion)
{
	global $db, $db_faq_questions, $cache;
	if((int)$rquestion['question_id'] == 0)
	{
		return FALSE;
	}
	$structure_updated = FALSE;
	$rquestion['question_cat'] = $rquestion['question_cat'] == FAQ_EMPTY_CAT_CODE ? '' : $rquestion['question_cat'];
	$rquestion['question_answer'] = empty($rquestion['question_answer']) ? null : $rquestion['question_answer'];
	$row = $db->query("SELECT question_approved,question_cat,question_position FROM $db_faq_questions WHERE question_id=? LIMIT 1", $rquestion['question_id'])->fetch();
	$rquestion['question_position'] = ($row['question_position'] != $rquestion['question_position'] && isset($rquestion['question_position'])) ? $rquestion['question_position'] : $row['question_position'];
	$rquestion['question_position'] = ($rquestion['question_position'] == 0) ? 999 : $rquestion['question_position'];
	
	foreach (cot_getextplugins('faq.update.first') as $pl)
	{
		include $pl;
	}

	if($row['question_approved'] != $rquestion['question_approved'] && $row['question_cat'] == $rquestion['question_cat'])
	{
		faq_structure_adjust_count($row['question_cat'], $rquestion['question_approved']);
		$structure_updated = TRUE;
	}
	if($row['question_cat'] != $rquestion['question_cat'])
	{
		if($rquestion['question_approved'] == 1)
		{
			faq_structure_adjust_count($rquestion['question_cat'], 1);
			$structure_updated = TRUE;
		}
		if($row['question_approved'] == 1)
		{
			faq_structure_cache_remove($row['question_cat']);
			faq_structure_adjust_count($row['question_cat'], 0);
			$structure_updated = TRUE;
		}
	}

	foreach (cot_getextplugins('faq.update.query') as $pl)
	{
		include $pl;
	}

	if($structure_updated)
	{
		$cache && $cache->db->remove('structure', 'system');
	}
	faq_structure_cache_remove($rquestion['question_cat']);

	return $db->update($db_faq_questions, $rquestion, 'question_id=?', $rquestion['question_id'], true);
}

function faq_question_delete($id, $rquestion = array())
{
	global $db_faq_questions, $db, $cache;
	$id = (int)$id;
	if($id == 0)
	{
		return FALSE;
	}
	if(count($rquestion) == 0)
	{
		$rquestion = $db->query("SELECT question_id,question_cat FROM $db_faq_questions WHERE question_id=? LIMIT 1", $id)->fetch();
		if(!$rquestion)
		{
			return FALSE;
		}
	}
	$structure_updated = FALSE;
	$deleted = $db->delete($db_faq_questions, 'question_id=?', array($id));
	foreach (cot_getextplugins('faq.delete.done') as $pl)
	{
		include $pl;
	}
	if($deleted)
	{
		$structure_updated = TRUE;
		faq_structure_cache_remove($rquestion['question_cat']);
		faq_structure_adjust_count($rquestion['question_cat'], 0);
	}
	if($structure_updated)
	{
		$cache && $cache->db->remove('structure', 'system');
	}
	return $deleted;
}

function cot_faq_sync($cat)
{
	global $db, $db_structure, $db_faq_questions;
	return (int)$db->query("SELECT COUNT(*) FROM $db_faq_questions WHERE question_cat=? AND question_approved=1", $cat)->fetchColumn();
}