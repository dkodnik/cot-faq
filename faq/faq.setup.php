<?php
/* ====================
[BEGIN_COT_EXT]
Name=FAQ
Description=A general purpose FAQ system
Version=1.0
Category=forms-feedback
Date=2012-03-30
Author=tyler@xaez.org
Copyright=
Notes=BSD License
Auth_guests=R
Lock_guests=
Auth_members=RW
Lock_members=
Recommends_modules=users
Recommends_plugins=
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
order=01:callback:faq_config_order():chron:
maxrowsperpage=02:string::50:
animate_scroll=03:radio::1
[END_COT_EXT_CONFIG]

[BEGIN_COT_EXT_CONFIG_STRUCTURE]
show_category=01:radio::1:
[END_COT_EXT_CONFIG_STRUCTURE]
==================== */

defined('COT_CODE') or die('Wrong URL.');
