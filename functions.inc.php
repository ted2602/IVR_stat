<?php /* $Id */
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
//	Copyright 2017 Itach-soft.
//
function ivrstat_hookGet_config($engine) {
	global $ext;
	global $db;



	switch($engine) {
		case "asterisk":

			$ivr_logging = ivrstat_get_details('ivr_logging');
			if ($ivr_logging['value'] == 'true') {

				//get all ivrs
				$ivrlist = ivr_get_details(); 
				if(is_array($ivrlist)) {

					foreach($ivrlist as $item) {
						//splice into ivr to set the ivr selection var and append if already defined
						$context = 'ivr-'.$item['id'];

						//get ivr selection
						$ivrentrieslist = ivr_get_entries($item['id']);
						if (is_array($ivrentrieslist)) {

							foreach($ivrentrieslist as $selection) {
								//splice into ivr selection
								$ext->splice($context, $selection['selection'], 'ivrsel-'.$selection['selection'], new ext_mysql_connect('connid','${AMPDBHOST}','${AMPDBUSER}','${AMPDBPASS}','${AMPDBNAME}'));
								$ext->splice($context, $selection['selection'], 'ivrsel-'.$selection['selection'], new ext_mysql_query('resultid', 'connid', 'INSERT INTO `ivrstat_log`(`time`, `uniqueid`, `calleridnum`, `ivrsel`, `ivr`) VALUES (\'${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}\',\'${UNIQUEID}\',\'${CALLERID(num)}\',\'${EXTEN}\',\'${IVR_CONTEXT}\')'));
								//$ext->splice($context, $selection['selection'], 'ivrsel-'.$selection['selection'], new ext_DumpChan('3'));
								//$ext->splice($context, $selection['selection'], 'ivrsel-'.$selection['selection'], new ext_queuelog('NONE','${UNIQUEID}','NONE','INFO', 'IVRAPPEND|${IVRSELECTION}'));
								$ext->splice($context, $selection['selection'], 'ivrsel-'.$selection['selection'], new ext_mysql_disconnect('connid'));

							}
							$label='final';
							$ext->splice($context, 't', $label, new ext_mysql_connect('connid','${AMPDBHOST}','${AMPDBUSER}','${AMPDBPASS}','${AMPDBNAME}'));
							$ext->splice($context, 't', $label, new ext_mysql_query('resultid', 'connid', 'INSERT INTO `ivrstat_log`(`time`, `uniqueid`, `calleridnum`, `ivrsel`, `ivr`) VALUES (\'${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}\',\'${UNIQUEID}\',\'${CALLERID(num)}\',\'timeout\',\'${IVR_CONTEXT}\')'));
							$ext->splice($context, 't', $label, new ext_mysql_disconnect('connid'));

							$ext->splice($context, 'i', $label, new ext_mysql_connect('connid','${AMPDBHOST}','${AMPDBUSER}','${AMPDBPASS}','${AMPDBNAME}'));
							$ext->splice($context, 'i', $label, new ext_mysql_query('resultid', 'connid', 'INSERT INTO `ivrstat_log`(`time`, `uniqueid`, `calleridnum`, `ivrsel`, `ivr`) VALUES (\'${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}\',\'${UNIQUEID}\',\'${CALLERID(num)}\',\'wrong\',\'${IVR_CONTEXT}\')'));
							$ext->splice($context, 'i', $label, new ext_mysql_disconnect('connid'));
						}
					}
				}
			}
		break;
	}
}

function ivrstat_configpageinit($pagename) {
        global $currentcomponent;

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if($pagename == 'ivrstat'){
                $currentcomponent->addprocessfunc('ivrstat_configprocess');

    		return true;
        }
}

//process received arguments
function ivrstat_configprocess(){
        if (isset($_REQUEST['display']) && $_REQUEST['display'] == 'ivrstat'){
                
		//get variables 
		$get_var = array('ivr_logging');
		
		foreach($get_var as $var){
                        $vars[$var] = isset($_REQUEST[$var])    ? $_REQUEST[$var]               : '';
                }

                $action = isset($_REQUEST['action'])    ? $_REQUEST['action']   : '';

                switch ($action) {
                        case 'save':
				ivrstat_put_details($vars);
                                needreload();
                                redirect_standard_continue();
                        break;
                }
        }
}

function ivrstat_put_details($options) {
	global $db;
	
	foreach ($options as $key => $item) {
		$data[] = array($key, $item); 
	}

	$sql = $db->prepare('REPLACE INTO ivrstat_options (`keyword`, `value`) VALUES (?, ?)');
        $ret = $db->executeMultiple($sql, $data);
	
	if($db->IsError($ret)) {
        	die_freepbx($ret->getDebugInfo()."\n".$ret->getUserInfo()."\n".$db->last_query);
        }
	return TRUE;
}

function ivrstat_get_details($keyword = '') {
	global $db;

        $sql = "SELECT * FROM ivrstat_options";

	if (!empty($keyword)) {
		$sql .= " WHERE `keyword` = '" . $keyword . "'";        
	}

	$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
        if($db->IsError($res)) {
                die_freepbx($res->getDebugInfo());
        }

        return (isset($keyword) && $keyword != '') ? $res[0] : $res;	
}
?>
