<?

class User
{
	public $id = 0;
	public $login = '';
	public $email = '';
	public $password = '';
	public $isSetAble = False;
	public $isAdmin = False;
	function __construct($id, $login) 
	{
		$this->id = $id;
		$this->login = $login;
	}
	public static function cryptPass($password)
	{
		return md5($password."7SMv;fuNzFa`z_:");
	}
	public static function create($login, $name, $password, $repass)
	{
		global $db;
		if(strlen($login) == 0 || strlen($name) == 0 || strlen($password) == 0 || strlen($repass) == 0)
			return IO::genErr(1);
		if( $password != $repass )
			return IO::genErr(1003);
		$login =  $db->escape_string ($login);
		$password =  $db->escape_string ($password);
		$name =  $db->escape_string ($name);
		$password = User::cryptPass($password);
		if( User::checkExists($login) )
			return IO::genErr(1004);
		$sql = "INSERT INTO `users`(`login`, `name`, `password`) VALUES ('{$login}','{$name}','{$password}')";
		return (IO::executeSqlParse($sql));
	}
	public static function authLogin($login, $password)
	{
		global $db;
		if(strlen($login) == 0)// || strlen($password) == 0)
			return IO::genErr(1);
		$login =  $db->escape_string ($login);
		$password =  $db->escape_string ($password);
		$sql = "SELECT * FROM `users` WHERE `login` = '{$login}'";
		$res = mysqli_query($db,$sql);
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		else if (!IO::isQueryOK($res))
			return IO::genErr(1001);
		else
		{
			$rows = array();
			while($r = mysqli_fetch_assoc($res))
				//$rows[] = Show::utf8ize($r);
				$rows[] = $r;
			$rows = $rows[0];

			if($rows['password'] == '' || $rows['password'] == User::cryptPass($password))
			{
				USER::bindData($rows);
				
				return IO::genErr(0);
			}
			else
				return IO::genErr(1002);
		}
	}
	private static function bindData($data)
	{
		$user = new User($data['id'], $data['login']) ;
		$user->email = $data['email'];
		$user->password = $data['password'];
		$user->isSetAble = $data['isSetAble'] == "1";
		$user->isAdmin = $data['isAdmin'] == "1";
		if($user->password == '')
			Misc::addAlert(0,0,-1,"У пользователя {$user->login} не задан пароль");
		$_SESSION['user'] = $user;
		setcookie("user", base64_encode(serialize($user)) ,time() + (86400 * 30), "/");
		if($user->isAdmin == 1)
		{
			setcookie("admin", true,time() + (86400 * 30), "/");
			$_SESSION['admin'] = true;
		}
	}
	static function checkExists($login)
	{
		global $db;
		$login =  $db->escape_string ($login);
		$sql = "SELECT `id` FROM `users` WHERE `login` = '{$login}'";
		$res = mysqli_query($db,$sql);
		if(mysqli_error($db) != null)
			return IO::genErrMsg(3, "MySQL:".mysqli_error($db));
		else if (IO::isQueryOK($res))
			return true;
		else
			return false;
	}
	
	
}