<?php

class this {
     public static $pdo; 

     public static $mysqli; 

     public static $instance; 

     public static $_url;

     public static $_pages = array('index', 'logout', 'mycontacts', 'action', 'phonebook'); 

     public static $_PAGE_URL = "http://localhost/php/phonebook/";
     
     public static $_PAGE_TITLE = "Shared Phonebook"; 

     public function __construct()
     {
          get::init(); 
          
          user::init(); 
     }

     public static function init()
     {
          $url = isset($_GET['page']) ? $_GET['page'] : null;

		$url = rtrim($url, '/');

		$url = filter_var($url, FILTER_SANITIZE_URL);

		self::$_url = explode('/', $url);

		if(is_null(self::$instance)) self::$instance = new self();

		return self::$instance;
     }

     public static function getContent()
     {
          require_once 'public/purifier/HTMLPurifier.auto.php'; 

          if(self::$_url[0] != 'action') include_once 'src/main/header.inc.php';

          if(in_array(self::$_url[0], self::$_pages))
          {
               include 'src/pages/' . self::$_url[0] . '.p.php';
          }
          else 
          {
               $_SESSION['page'] = '';

               include 'src/pages/index.p.php';
          }

          if(self::$_url[0] != 'action') include_once 'src/main/footer.inc.php';
     }

     public static function showalert($alert_type, $message, $redirect = '')
	{
          $_SESSION['msg'] = '
          <div class="alert alert-'.$alert_type.'" role="alert">
               '.$message.'
          </div>
          '; 
          
          return this::redirect($redirect); 
	}

     public static function redirect($page, $deelay = false)
	{
		if($deelay != false)
		{
			echo '<meta http-equiv="refresh" content="'.$deelay.';' . this::$_PAGE_URL.$page.'">';
			return;
		}

		header('Location: '.this::$_PAGE_URL.$page);
	}

     public static function protect($text)
	{
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $last_purify = $purifier->purify(self::xss_clean(self::clean($text)));
        return $last_purify;
     }

     public static function clean($text = null)
	{
		if (strpos($text, '<h1') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, '<h2') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, '<h3') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, '<h4') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, '<h5') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, '<h6') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, '<script') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, '<img') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, 'meta') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, 'document.location') !== false) return '<i><small>Unknown</small></i>';

		$regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';

		return preg_replace_callback($regex, function ($matches) {

			return '<a target="_blank" href="'.$matches[0].'">'.$matches[0].'</a>';

		}, $text);
	}

	public static function xss_clean($data)
	{
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
            $old_data = $data;

            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        return $data;
	}
}

?>