
<?php
//	Copyright 2017 Itach-soft.
//
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

global $db;

$sql = "SELECT * FROM `ivrstat_options` LIMIT 1";
$check = $db->query($sql);
if(!DB::IsError($check)) {
	out(_("ivrstat_options table already exists, exiting"));
} else {

	unset($sql);
	$sql[] = "CREATE TABLE IF NOT EXISTS `ivrstat_options` (
	                `keyword` VARCHAR(25),
	                `value` TEXT,
	                UNIQUE KEY `keyword` (`keyword`)
	                );
CREATE TABLE IF NOT EXISTS `ivrstat_log` (
  `time` datetime DEFAULT NULL,
  `uniqueid` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `calleridnum` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `ivrsel` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `ivr` varchar(80) CHARACTER SET cp852 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


	                ";
	
	foreach ($sql as $q) {
	        $result = $db->query($q);
	        if($db->IsError($result)){
	                die_freepbx($result->getDebugInfo());
	        }
	}

	outn(_("creating ivrstat_options...ok"));
}

// sysadmin migration
outn(_("checking for ivr_logging field..."));
$sql = "SELECT `keyword` FROM `ivrstat_options` where `keyword` = 'ivr_logging'";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if($db->IsError($check) || empty($check)) {
        // add new field
        $sql = "INSERT INTO `ivrstat_options` (`keyword`, value) VALUES ('ivr_logging', '0');";
        $result = $db->query($sql);
        if($db->IsError($result)) {
                die_freepbx($result->getDebugInfo());
        }
        out(_("OK"));
} else {
        out(_("already exists"));
}
?>
