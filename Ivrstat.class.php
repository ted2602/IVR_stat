<?php
namespace FreePBX\modules;
class Ivrstat implements \BMO

{

    //функции инициализации класса модуля для Freepbx
    public function __construct($freepbx = null)
    {
        if ($freepbx == null) {
            throw new Exception("Not given a FreePBX Object");
        }
        $this->FreePBX = $freepbx;
        $this->db = $freepbx->Database;
    }

    public function install()
    {
    }

    public function uninstall()
    {
    }

    public function backup()
    {
    }

    public function restore($backup)
    {
    }

    //конец функции инициализации класса модуля для Freepbx
    public function doConfigPageInit($page)
    {
        isset($_REQUEST['action']) ? $action = $_REQUEST['action'] : $action = '';
        isset($_REQUEST['itemid']) ? $itemid = $_REQUEST['itemid'] : $itemid = '';
        switch ($action) {
            case "settings":
                $res = editsettings($_REQUEST);
                needreload();
                break;
            case "b24users":
                needreload();
                $this->user_update($this->getsettings(), $this->b24_decode($this->getsettings()));
                break;
            case "b24track":
                needreload();
                $this->reclama_edit();
                break;
            case "other":
                $ext = $this->update_def_agent($_REQUEST["extension"]);
                $event = isset($_REQUEST["active_event"]) ? $_REQUEST["active_event"] : "";
                switch ($event) {
                    case '1': // bind event handler
                        $datacase = $this->event_bind($this->getsettings(), $this->b24_decode($this->getsettings()));

                        break;
                    case '0': // bind event handler

                        $datacase = $this->event_unbind($this->getsettings(), $this->b24_decode($this->getsettings()));
                        break;
                }
                break;
                needreload();

        }
    }


//функция для построения boostrap таблиц
    public function ajaxHandler()
    {
        switch ($_REQUEST['command']) {
            case 'getJSON':
                switch ($_REQUEST['jdata']) {

                    case 'b24phonegrid':
                        return array_values($this->user_get($this->getsettings()));
                        break;
                    case 'statistic':
                        return array_values($this->statistic());
                        break;
                    default:
                        return false;
                        break;
                }
                break;

            default:
                return false;
                break;
        }


    }

    public function ajaxRequest($req, &$setting)
    {
        switch ($req) {
            case 'getJSON':
                return true;
                break;
            default:
                return false;
                break;
        }
    }


    public function statistic()
    {
        $query = $this->db->prepare("
			SELECT *
            FROM  `ivrstat_log`
            ORDER BY  `ivrstat_log`.`time` DESC
			");
        $row = $query->execute();
        $i = 0;
        //$row = $query->fetchall(\PDO::FETCH_BOTH);
        while ($row = $query->fetch(\PDO::FETCH_BOTH)) {
            $phpdate = strtotime( $row["time"]);
            $res[$i]["date"] = date( 'Y-m-d', $phpdate );
            $phpdate = strtotime( $row["time"]);
            $res[$i]["time"] = date( 'H:i:s', $phpdate );
            $res[$i]["uniqueid"] = $row["uniqueid"];
            $res[$i]["calleridnum"] = $row["calleridnum"];
            $res[$i]["agent"] = $this->get_agent($row["uniqueid"]);
            $res[$i]["ivrsel"] = $row["ivrsel"];
            if ($this->ivrname($row["ivr"])!==NULL)
            $res[$i]["ivr"] = $this->ivrname($row["ivr"]);
            else
            $res[$i]["ivr"] = $row["ivr"];
            $i++;
        }

        return ($res);
    }

    public function ivrname($ivr)
    {
    $id=substr($ivr, 4);
    $query = $this->db->prepare("
			SELECT `name`
            FROM  `ivr_details`
            WHERE  `id` LIKE $id
			");
    $row = $query->execute();
    $row = $query->fetch(\PDO::FETCH_BOTH);
    $res=$row['name'];
    return ($res);
    }

    public function get_agent($uniqueid)
    {
        $query = $this->db->prepare("
          SELECT `dst` FROM  asteriskcdrdb.cdr  WHERE  `uniqueid` =  '$uniqueid' AND `dst` LIKE '____' ORDER BY  `calldate`;
			");
        $row = $query->execute();
        $row = $query->fetch(\PDO::FETCH_BOTH);
        if ($row['dst']==NULL) $res=''; else $res=$row['dst'];
        return ($res);
    }
}

/*jj*/