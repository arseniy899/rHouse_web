<?
require_once ("include.php");
class Unit
{
	public $id	 		= 0;
	public $unid 		= '';
	public $setid	 	= '';
	public $lastValue 	= '';
	public $lastTime 	= '';
	public $name 		= '';
	public $alive		= 0;
	public $sectId		= 0;
	//public $sectName	= '';
	public $desc		= '';
	public $units		= '';
	public $valueType	= '';
	public $direction	= '';
	public $timeout		= '';
	public $icon		= '';
	public $uiShow		= true;
	public $color		= '';
	public $values		= array();
	
	function __construct($unid, $setid)
	{
		$this->unid = $unid;
		$this->setid = $setid;
		//$this->getInfoFRDB();
	}
	static function getSelectSQL()
	{
		return "
		SELECT 	units_run.lastValue,
			units_run.id,
			units_run.unid,
			units_run.setid,
			units_run.color,
			units_run.lastTime,
			units_run.name,
			units_run.iconCust,
			units_run.uiShow,
			units_run.sectId,
			units_def.description,
			units_def.units,
			units_def.timeout,
			units_def.direction,
			units_def.icon,
			units_def.possValues,
			units_def.valueType
			/*units_sections.name as sectName,
			units_sections.isDefHidden*/
		FROM   `units_run`
		   LEFT OUTER JOIN units_def ON units_def.unid = units_run.unid 
		   /*LEFT OUTER JOIN units_sections ON units_sections.id = units_run.sectId */
		";
	}
	function getInfoFRDB()
	{
		global $db;
		if($this->id == 0 && $this->unid == '' && $this->setid == '' )
			return;
		$sql = Unit::getSelectSQL();
		if($this->id != 0)
			$sql .="WHERE  units_run.id = '{$this->id}'";
		else
			$sql .="WHERE  units_run.setid = '{$this->setid}' AND units_run.unid = '{$this->unid}'";
		$res = mysqli_query($db,$sql);
		
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		else if (IO::isQueryOK($res))
		{
			$rows = array();
			$data = mysqli_fetch_assoc($res);
			
			Unit::bindDBdata($this,$data);
		}
		else
			return false;
	}
	static function bindDBdata($obj, $data)
	{
		$obj->id = $data['id'];
		$obj->unid = $data['unid'];
		$obj->setid = $data['setid'];
		$obj->lastTime = $data['lastTime'];
		$obj->lastValue = $data['lastValue'];
		$obj->name = $data['name'];
		$obj->desc = $data['description'];
		$obj->units = $data['units'];
		$obj->valueType = $data['valueType'];
		$obj->possValues = $data['possValues'];
		$obj->timeout = $data['timeout'];
		$obj->direction = $data['direction'];
		$obj->color = $data['color'];
		$obj->uiShow =( $data['uiShow'] == '1');
		//$obj->sectName =$data['sectName'];
		$obj->sectId =$data['sectId'];
		if(strlen($data['iconCust'])>0)
			$obj->icon = $data['iconCust'];
		else if(strlen($data['icon'])>0)
			$obj->icon = $data['icon'];
		else
			$obj->icon = '/sensor.png';
		$obj->timeout = $data['timeout'];
		return $obj;
	}
	static function selectRecentUnits()
	{
		global $db;
		$sql = Unit::getSelectSQL()."
			WHERE units_run.lastTime !='' AND (units_run.lastTime >= DATE_FORMAT(NOW() - INTERVAL 1 MINUTE,'%Y.%m.%d %H:%i:%s'))";
		
		$res = mysqli_query($db,$sql);
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		else if (IO::isQueryOK($res))
		{
			$rows = array();
			$objs = array();
			
			
			while($data = mysqli_fetch_assoc($res))
			{
				$rows[] = $data;
				$obj = new Unit($data['unid'],$data['setid']);
				Unit::bindDBdata($obj,$data);
				$objs[] = $obj;
			}
			return $objs;
		}
		else
			return false;
	}
	static function getUnitsDefs()
	{
		global $db;
		$sql = "SELECT 	* FROM `units_def`";
		
		$res = mysqli_query($db,$sql);
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		else if (IO::isQueryOK($res))
		{
			$rows = array();
			
			
			while($data = mysqli_fetch_assoc($res))
				$rows[$data['unid']] = $data;
			return $rows;
		}
		else
			return false;
	}
	static function createNew($unid, $intf, $color,  $uiShow, $sectId, $icon = '')
	{
		global $db;
		$sql = "SELECT units_def.description  FROM `units_def` WHERE unid = '{$unid}'";
		$resSel = mysqli_query($db,$sql);
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		$res = mysqli_query($db,"SELECT units_run.setid  FROM `units_run` WHERE units_run.unid = '{$unid}' ORDER BY `id` DESC LIMIT 1 ");
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		#print("SELECT `id` FROM `units_run` WHERE `unid` = '%s' ORDER BY `id` DESC LIMIT 1 " % unid)
		$nextSetdId = 0;
		if(empty($color))
			$color = '27AE60';
		
		if(!$resSel || @mysqli_num_rows($resSel) == 0)
		{
			//print('Unknown produced device connected')
			
			Misc.addAlert($unid, $nextSetdId,1004);
			return IO::genErr(3001);
		}
		else
		{
			$data = mysqli_fetch_assoc($res);
			$resData = mysqli_fetch_assoc($resSel);
			if(isset($data) && mysqli_num_rows($res) >0 )
			{
				//print("Last id: " % res[0]['setid'])
				$nextSetdId = sprintf('%04X', hexdec ($data['setid'])+1);
			}
			else
				$nextSetdId = '0001';
			if($uiShow == 'true' || $uiShow == '1' || $uiShow == 'on' || $uiShow == 't')
				$uiShow = 1;
			else
				$uiShow = 0;
			//print("Registering new device with id: ".unid."|".nextSetdId);
			$sql = "
				INSERT INTO `units_run`
				(
				 `unid`,
				 `setid`,
				 `lastvalue`,
				 `lasttime`,
				 `timeAdded`,
				 `interface`,
				 `name`,
				 `color`,
				 `uiShow`,
				 `sectId`,
				 `iconCust`,
				 `alive`
				)
				VALUES
				(
				 '{$unid}',
				 '{$nextSetdId}',
				 '',
				 '',
				 '".date("Y.m.d H:i:s")."',
				 '{$intf}',
				 '{$resData['description']}',
				 '{$color}',
				 '{$uiShow}',
				 '{$sectId}',
				 '{$icon}',
				 '-1'
				); 
			";
			
			$res = mysqli_query($db,$sql);
			$unit = new Unit($unid, $nextSetdId);
			$unit->id = $db->insert_id;
			$unit->getInfoFRDB();
			Misc::registerEvent($unid,$nextSetdId,'SocketWrap','Registered new device!' );
			$payload = Hub::sendPayload(array(
				"cmd" => "device.reg.notif",
				"unitId" => $unit->unid,
				"setId"=> $unit->setid
			));
			return $payload;
			//return $unit;
		}
	}
	function setValue($value)
	{
		if($this->id == 0 && $this->unid == 0 && $this->setid == 0)
			$this->setInfoToDB();
		if($this->unid != "" && $this->setid != "")
		{
			return Hub::getPayload(array(
				"cmd" => "device.value.set",
				"unitId" => $this->unid,
				"setId"=> $this->setid,
				"value"=>$value
			));
		}
		else
			return IO::genErr(1);
	}
	function scheduleValueSet($value, $date)
	{
		if($this->id == 0 && $this->unid == 0 && $this->setid == 0)
			$this->setInfoToDB();
		if($this->unid != "" && $this->setid != "")
		{
			return Hub::getPayload(array(
				"cmd" => "device.value.schedule",
				"unitId" => $this->unid,
				"setId"=> $this->setid,
				"date"=>$date,
				"value"=>$value
			));
		}
		else
			return IO::genErr(1);
		/*if($this->unid != "" && $this->setid != "")
		{
			
			$content = <<<EOF

newVal = '{$value}'
unit    = Device.findDeviceById('{$this->unid}','{$this->setid}')
smsStr = 'sms,all,*'
if unit != None:
	unit.setValue(newVal)
	smsStr = smsStr + 'Value {$value} set ok'
else:
	smsStr = smsStr + 'Error setting value {$value}'

modems = Device.findDeviceById('0078','')
if len(modems)>0:
	for key, modem in modems.items():
		modem.setValue(smsStr)
EOF;
			
			$filePath = Misc::writeTempUserScript($content);
			
			$sql = "
				INSERT INTO `schedule`
				(
					`type`, 
					`time`, 
					`lastTimeRun`, 
					`nextTimeRun`, 
					`script_name`, 
					`active`
				)
				VALUES
				(
					'1',
					'{$date}',
					'',
					'',
					'{$filePath}',
					'1'
				); 
			";
			global $db;
			$res = mysqli_query($db,$sql);
			if(mysqli_error($db) != null)
				return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
			else if (IO::isQueryOK($res))
			{
				$ret = Hub::sendPayload(array(
					"cmd"		=> "schedule.update"
				));
				if(is_string($ret))
					return $ret;
				else
				 
					return IO::genErrMsg(0, "scheduled");
				
			}
			else
				return IO::genErr(2);
			
			/
			return IO::genErr(0);
		}
		else
			return IO::genErr(1);*/
	}
	function setInfoToDB()
	{
		global $db;
		if($this->id == 0 && $this->unid == '' && $this->setid == '' )
			return;
		$uiShow = $this->uiShow?1:0;
		$sql = "
		UPDATE `units_run` 
		SET  `name` = '{$this->name}', `color` = '{$this->color}', `iconCust` = '{$this->icon}',`sectId` = '{$this->sectId}',`uiShow` = '{$uiShow}' ";
		if($this->id != 0)
			$sql .="WHERE  units_run.id = '{$this->id}'";
		else
			$sql .="WHERE  units_run.setid = '{$this->setid}' AND units_run.unid = '{$this->unid}'";
		$res = mysqli_query($db,$sql);
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		else if (IO::isQueryOK($res))
		{
			return $this;
		}
		else
			return false;
	}
	function deleteDB()
	{
		global $db;
		if($this->id == 0 && $this->unid == '' && $this->setid == '' )
			return;
		$sql = " DELETE FROM `units_run` ";
		if($this->id != 0)
			$sql .="WHERE  units_run.id = '{$this->id}'";
		else
			$sql .="WHERE  units_run.setid = '{$this->setid}' AND units_run.unid = '{$this->unid}'";
		$res = mysqli_query($db,$sql);
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		else if (IO::isQueryOK($res))
		{
			return $this;
		}
		else
			return false;
	}
	static function getAllUnitsSql()
	{
		global $db;
		$sql = Unit::getSelectSQL()."ORDER BY `units_run`.`sectId` ASC";
		$res = mysqli_query($db,$sql);
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		else if (IO::isQueryOK($res))
		{
			$rows = array();
			$objs = array();
			
			
			while($data = mysqli_fetch_assoc($res))
			{
				$rows[] = $data;
				
				
				$obj = new Unit($data['unid'],$data['setid']);
				Unit::bindDBdata($obj,$data);
				$objs[] = $obj;
			}
			return $objs;
		}
		else
			return false;
	}
	function getUnitVals($from, $to, $maxVals = 500)
	{
		global $db;
		$sql = "
			SELECT * FROM `units_values` 
			WHERE 
				`unrId` = '{$this->id}' AND 
				`timeStamp` >=str_to_date('{$from}','%Y.%m.%d') AND `timeStamp` <= str_to_date('{$to} 23:59:59','%Y.%m.%d %H:%i:%s')
			ORDER BY `timeStamp`";
		$res = mysqli_query($db,$sql);
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		else if (IO::isQueryOK($res))
		{
			while($data = mysqli_fetch_assoc($res))
			{
				$this->values[] = $data;
			}
			if(count($this->values) > $maxVals)
			{
				$step = floor(count($this->values)/$maxVals);
				
				$this->values = array_filter($this->values,function ($key)  use ($step)
				{
					return $key%$step == 0;
				},ARRAY_FILTER_USE_KEY );
				$this->values = array_values($this->values);
			}
			return $this->values;
		}
		else
			return false;
	}
}