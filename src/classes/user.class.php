<?php

class user
{
	public static function init() { }

	public static function isLogged()
	{
		$bLog = (isset($_SESSION['account']) ? true : false);
		return $bLog;
	}

	public static function get()
	{
		$bGet = (isset($_SESSION['account']) ? $_SESSION['account'] : false);
		return $bGet;
	}

	public static function getData($id,$data)
	{
		if(!is_array($data))
		{
			$q = get::$g_sql->prepare('SELECT `'.$data.'` FROM `users` WHERE `id` = ? LIMIT 1;');
			$q->execute(array($id));
			$fdata = $q->fetch();
			return $fdata[$data];
		}
		else
		{
			$q = '';
			foreach($data as $d) {
				if(end($data) !== $d) $q .= '`'.$d.'`,';
				else $q .= '`'.$d.'`';
			}

			$q = get::prepare('SELECT '.$q.' FROM `users` WHERE `id` = ? LIMIT 1;');
			$q->execute(array($id));

			return $q->fetch(PDO::FETCH_ASSOC);
		}
	}
}

?>
