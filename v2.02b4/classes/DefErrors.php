<?php
//exit(DefErrors::genErr());
class DefErrors
{
    public static $errorsRU = array(
        
        -1   => "Прочая ошибка сервера",
        1   => "Одно или несколько полей пусто",
		2   => "Ошибка подключения к базе",
		3	=> "Ошибка записи в базу",
		4	=> "Ошибка чтения из базы",
		5	=> "Ошибка работы с базой",
		6	=> "Ошибка доступа",
        //Users
		1000   => "Нет авторизации",
		1001   => "Такого пользователя не существует",
		1002   => "Пароль неверен",
		1003   => "Пароли не совпадают",
		1004   => "Такой email уже занят",
		//TCP-Hub
		2001	=> "Ошибка соединения с HUB",
		2003	=> "Неверный токен",
		2004	=> "Ошибка доставки сообщения устройству",
		2005	=> "Комманда не распознана",
		2006	=> "Ошибка выполнения команды",
		//Units
		3001	=> "Подключено устройство неизвестного производства",
		3002	=> "Данное устройство необнаружено",
		3003	=> "Устройство не в сети",
		//Admin
		4005 	=> "Исходного файла не существует или новое название уже занято"
    );
	public static function utf8ize($d) {
		if (is_array($d)) {
			foreach ($d as $k => $v) {
				$d[$k] = DefErrors::utf8ize($v);
			}
		} else if (is_string ($d)) {
			if(mb_detect_encoding($d) == 'Windows-1251')
				$d = mb_convert_encoding($d, 'UTF-8', 'Windows-1251');
			else if(mb_detect_encoding($d) == 'UTF-8')
				$d = mb_convert_encoding($d, 'UTF-8', 'UTF-8');
			//else
				//$d = $d;
			//return mb_detect_encoding($d);
		}
		return $d;
	}
    public static function genErr($id)
    {
		return IO::genErr($id);
        /*if(array_key_exists(strval($id),DefErrors::$errorsRU))
            $desc = DefErrors::$errorsRU[$id];
        else
            $desc="";
		
        $res = array();
		$res['error'] = $id;
		$res['desc'] = DefErrors::utf8ize($desc);
		$json = json_encode($res,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		return $json;*/
        
    }
	
	public static function showJSres($arr)
    {
		return IO::showJSres($arr);
		/*$res = array();
		$res['error'] = 0;
		$res['data'] = DefErrors::utf8ize($arr);
		$json = json_encode($res,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		return $json;*/
    }
	public static function genErrMsg($id, $mess)
    {
		return IO::genErrMsg($id, $mess);
        /*if(array_key_exists(strval($id),DefErrors::$errorsRU))
            $desc = DefErrors::$errorsRU[$id];
        else
            $desc="";
		$res = array();
		$res['error'] = $id;
		$res['message'] = DefErrors::utf8ize($mess);
		$res['desc'] = DefErrors::utf8ize($desc);
		$json = json_encode($res,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		return $json;
        */
    }
}