<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */


defined('COT_CODE') or die('Wrong URL.');

$env['location'] = 'faq';
require_once cot_incfile('faq', 'module');

if (!in_array($m, array('add')))
{
	$m = 'main';
}

include cot_incfile('faq', 'module', $m);
