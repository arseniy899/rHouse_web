<?require_once ("include.php");
class Misc
{
	static $startDir = "";
	static $updateUrl = SERVICE_IP.'/service/update/';
	static function getDirFiles($dir,$fullPath = false, &$results = array(), $inner = false)
	{
		if(!$fullPath)
			$dir = LOCAL_VER_ROOT.$dir;
		$dir = str_replace("//", "/", $dir);
		$dir = rtrim($dir,"/");
		$files = scandir($dir);
		//var_dump($files);
		if(!$inner)
			Misc::$startDir = $dir;
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			//
			$path = str_replace("\\", "/", $path);
			if(!is_dir($path)) {
				//$path = str_replace(LOCAL_VER_ROOT, "", $path);
				$path =substr($path,strlen(Misc::$startDir));
				//$path = "//".REMOTE_ROOT."/".$path;
				$path = str_replace("\\", "/", $path);
				//$path =substr($path,strlen(Misc::$startDir));
				$path = str_replace(Misc::$startDir, "", $path);
				//$path = str_replace(LOCAL_VER_ROOT, "", $path);
				//$path = ltrim($path,"/");
				if(strstr($path,".") && !strstr($path,"__init__"))
					$results[] = $path;

			} 
			else if($value != "." && $value != "..")
			{
				//$path = str_replace(REMOTE_ROOT, "", $path);
				$path = str_replace("\\", "/", $path);
				Misc::getDirFiles($path, $fullPath, $results, true);
				//$results[] = $path;
			}
		}
		//echo LOCAL_VER_ROOT."\n";
		//var_dump($results);
		return $results;
	}
	private static $userScriptsBlackList = array(/*"SYS",*/"__init__.py");
	static function getUserScriptsBlackList()
	{
		global $_SESSION;
		if(isset($_SESSION['SYS_SHOW']) && $_SESSION['SYS_SHOW'] == true)
			return array(/*"SYS",*/"__init__.py");
		else
			array("SYS","__init__.py");
	}
	static function strstr_array( $haystack, $needle ) 
	{
		if ( !is_array( $needle ) ) {
			return false;
		}
		foreach ( $needle as $element ) {
			if(strstr($haystack, $element)) return true;
		}
		return false;
	}
	public static function deleteDir($dirPath) 
	{
		if (! is_dir($dirPath)) {
			throw new InvalidArgumentException("$dirPath must be a directory");
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}
	public static function chmod_R($path, $filemode) 
	{
		if ( !is_dir($path) ) 
		{
			return chmod($path, $filemode);
		}
		$dh = opendir($path);
		while ( $file = readdir($dh) ) 
		{
			if ( $file != '.' && $file != '..' ) 
			{
				$fullpath = $path.'/'.$file;
				if( !is_dir($fullpath) ) 
				{
					if ( !chmod($fullpath, $filemode) )
					{
						return false;
					}
				} 
				else 
				{
					if ( !Misc::chmod_R($fullpath, $filemode) ) 
					{
						return false;
					}
				}
			}
		}
     
		closedir($dh);
     
		if ( chmod($path, $filemode) ) 
		{
			return true;
		} 
		else 
		{
		return false;
		}
    }
	static function denormalizeUserHubPath( $path ) 
	{
		global $CONFIG;
		$path = str_replace("..","",$path);
		$path = str_replace("root","",$path);
		$path = str_replace("//","",$path);
		$path = $CONFIG['HUB']['uScriptsPath']."/".$path;
		$path = str_replace("\\\\","\\",$path);
		$path = str_replace("//","/",$path);
		return $path;

	}
	static function getDirRecur($dir)
	{
		if (! is_dir($dir)) 
		{
			// If the user supplies a wrong path we inform him.
			return null;
		}
		
		// Our PHP representation of the filesystem
		// for the supplied directory and its descendant.
		$data = [];

		foreach (new DirectoryIterator($dir) as $f) 
		{
			if ($f->isDot()) 
			{
				// Dot files like '.' and '..' must be skipped.
				continue;
			}

			$path = $f->getPathname();
			$name = $f->getFilename();
			if(!Misc::strstr_array($name,Misc::getUserScriptsBlackList()))
			{
				if ($f->isFile()) 
				{
					$data[] = [ 'text' => $name,'icon' => "jstree-file", "type"=> "file" ];
				} else {
					// Process the content of the directory.
					$files = Misc::getDirRecur($path);

					$data[] = [ 'children'  => $files,'icon'=>'folder','id' => basename($name), 'text' => $name, "type"=> "default" ];
					// A directory has a 'name' attribute
					// to be able to retrieve its name.
					// In case it is not needed, just delete it.
				}
			}
		}

		// Sorts files and directories if they are not on your system.
		usort($data, function($a, $b) 
		{
			if(isset($a['children']) && !isset($b['children']))
				return -1;
			else if(!isset($a['children']) && isset($b['children']))
				return 1;
			
			$aa = $a['text'];
			$bb = $b['text'];

			return \strcmp($aa, $bb);
		});
		
		return $data;
	}
	static function checkUpdate()
	{
		global $CONFIG;	
		$ch = curl_init();
		
		$urlCheck = Misc::$updateUrl.'update.check.php';
		// Set query data here with the URL
		curl_setopt($ch, CURLOPT_URL, $urlCheck); 

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		$content = trim(curl_exec($ch));
		curl_close($ch);
		
		$json = json_decode($content);
		if($json)
		{
			$json = $json->responce;
			if($json->error == 0)
			{
				$getWebVer = $json->data->webVer;
				$curWebVer = str_ireplace("v","",$CONFIG['version']);
				if ($curWebVer == $getWebVer ) return false;
				else if(strcasecmp($getWebVer,$curWebVer) > 0)
					return $getWebVer;
				else
					return false;
					
			}
		}
		else
			Misc::addAlert('','','','Error checking update');
		return false;
	}
	static function addAlert($unid,$setid,$id,$msg='')
	{
		global $db;
		$text = '';
		if(array_key_exists(strval($id),DefErrors::$errorsRU))
			$text = DefErrors::$errorsRU[$id];
		
		mysqli_query($db,"INSERT INTO `alerts` (`unid`, `setid`, `code`, `text`, `time`) VALUES ('{$unid}', '{$setid}', '{$id}', '{$text}. {$msg}', '".date("Y.m.d H:i:s")."')");
	}
	static function registerEvent($unid,$setid,$level,$event)
	{
		global $db;
		$text = '';
		//if(array_key_exists(strval($id),DefErrors::$errorsRU))
		//    $text = DefErrors::$errorsRU[$id];
		//INSERT INTO `events` (`unid`, `setid`, `level`,`event`, `time`) VALUES ('%s', '%s','%s', '%s', '%s')
		mysqli_query($db,"INSERT INTO `events` (`unid`, `setid`, `level`,`event`, `time`) VALUES ('{$unid}', '{$setid}', '{$level}', '{$event}', '".date("Y.m.d H:i:s")."')");
	}
	static function writeConfigArrToFile()
	{
		global $CONFIG;
		$confStr = var_export($CONFIG, TRUE);
		$confStr = "<?
// !! DON'T LEAVE COMMENTS, THEY WILL BE DELETED
\$CONFIG = {$confStr};
		";
		file_put_contents(LOCAL_ROOT.'config.php', $confStr);
	}
}