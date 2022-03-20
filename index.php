<?php
if(!isset($_SESSION)) session_start();

ob_start();

spl_autoload_register(function ($class) {
    include 'src/classes/' . $class . '.class.php';
});

this::init()->getContent();

?>
