<?php
if(!user::isLogged()) return this::showalert('danger', 'Nu poti accesa aceasta pagina deoarece nu esti logat.', '');

unset($_SESSION['account']);
session_destroy();
return this::redirect('');
?>
