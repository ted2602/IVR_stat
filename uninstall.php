<?php
//	Copyright 2017 Itach-soft.
//
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

global $db;

outn(_("dropping table ivrstat.."));
sql('DROP TABLE IF EXISTS `ivrstat_options`');
sql('DROP TABLE IF EXISTS `ivrstat_log`');
out(_("done"));

