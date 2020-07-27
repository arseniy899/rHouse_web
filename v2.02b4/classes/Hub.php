<?
require_once ("include.php");
class Hub
{
	static function sendPayload($payload)
	{
		global $CONFIG;
		$jsonObj = array(
			"token" => $CONFIG['HUB']['token'],
			"data" => $payload
		);
		$jsonStr = json_encode($jsonObj);
		try {
			$fp = fsockopen($CONFIG['HUB']['IP'], $CONFIG['HUB']['PORT'], $errno, $errstr, 30);
		}
		catch(Exception $ex) { //used back-slash for global namespace
			return IO::genErrMsg(2001, "$errstr ($errno)<br />\n");
		}
		
		if (!$fp /*|| empty($fp) || $fp = '' || $fp === false*/) {
			return (IO::genErrMsg(2001, "$errstr ($errno)<br />\n"));
		} else {
			//var_dump($fp);
			$fw = fwrite($fp, $jsonStr);
			flush();
			if (false===$fw) 
			{
				return IO::genErrMsg(3, error_get_last()."<br />\n");
				echo 'Failed sending command. ';
				if ( function_exists('error_get_last') )
					var_dump( error_get_last() );
			}
			$response = fread($fp, 4096);
			fclose($fp);
			$resp = json_decode($response, true);
			if($resp['errcode'] == 0 && !empty($resp['data']))
				return $resp['data'];
			else if($resp['errcode'] == 0)
				return IO::genErr(0);
			else if(array_key_exists('msg',$resp))
				return IO::genErrMsg($resp['errcode'],$resp['msg']);
			else
				return IO::genErrMsg($resp['errcode']);
			
		}
	}
	static function getPayload($request)
	{
		global $CONFIG;
		$jsonObj = array(
			"token" => $CONFIG['HUB']['token'],
			"data" => $request
		);
		$jsonStr = json_encode($jsonObj);
		try {
			$fp = fsockopen($CONFIG['HUB']['IP'], $CONFIG['HUB']['PORT'], $errno, $errstr, 30);
		}
		catch(Exception $ex) { //used back-slash for global namespace
			return IO::genErrMsg(2001, "$errstr ($errno)<br />\n");
		}
		
		if (!$fp /*|| empty($fp) || $fp = '' || $fp === false*/) {
			return (IO::genErrMsg(2001, "$errstr ($errno)<br />\n"));
		} else {
			//var_dump($fp);
			$fw = fwrite($fp, $jsonStr);
			flush();
			if (false===$fw) 
			{
				
				/*return IO::genErrMsg(3, error_get_last()."<br />\n");
				echo 'Failed sending command. ';
				if ( function_exists('error_get_last') )
					var_dump( error_get_last() );*/
			}
			$response = fread($fp, 4096);
			fclose($fp);
			$resp = json_decode($response, true);
			if($resp['errcode'] == 0 && isset($resp['data']))
				return $resp['data'];
			else if($resp['errcode'] == 0)
				return IO::genErr(0);
			else if(array_key_exists('msg',$resp))
				return IO::genErrMsg($resp['errcode'],$resp['msg']);
			else
				return IO::genErrMsg($resp['errcode']);
		}
	}
	
}