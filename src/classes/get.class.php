<?php

class get
{
	public static $g_sql;

	public static function init()
	{
		$database['mysql'] = array(
			'host' 			=>	'localhost',
			'username' 		=> 	'root',
			'dbname' 		=> 	'phonebook',
			'password' 		=> 	''
		);

		try
		{
			self::$g_sql = new PDO('mysql:host='.$database['mysql']['host'].';dbname='.$database['mysql']['dbname'].';charset=utf8',$database['mysql']['username'],$database['mysql']['password']);

			self::$g_sql->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			self::$g_sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			@file_put_contents('error_log',@file_get_contents('error_log') . $e->getMessage() . "\n");

			die('Database error!');
		}
	}
}

?>
