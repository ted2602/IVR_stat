<?php
//	Copyright 2017 Itach-soft.
//
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
$request = $_REQUEST;

echo ('<a href="?display=ivrstat&amp;view=stat" class="btn btn-default"></i>&nbsp;'._("Statistics").' </a>');
echo ('<a href="?display=ivrstat&amp;view=settings" class="btn btn-default"></i>&nbsp;'._("Settings").' </a>');

echo ('<br><br>');
if (isset($_REQUEST['view']))
{
	switch ($request['view']) {
		case 'settings':
			require(dirname(__FILE__).'/view/settings.php');
			break;
		case 'stat':
			require(dirname(__FILE__) . '/view/stat.php');
			break;
		case 'test':
			require(dirname(__FILE__) . '/test.php');
			break;
		default:
			break;
	}
}
else
{
	require(dirname(__FILE__).'/view/stat.php');


}
echo("<h6 align='center'>"._("IVR Statistic module for FreePBX. <a target=\"_blank\" href=http://www.itach.by>Itach-soft LLC</a>. Minsk ".date(Y))."</h6>");


