<?php 
if(isset($_POST['submit_login']) && !user::islogged()) {
	$email = $_POST['_email']; 
	$pass = $_POST['_password']; 

	if(!$email || !$pass)
		return this::showalert('danger', 'You left blank fields!', '');
	else { 
		$password = hash('whirlpool', $_POST['_password']); 
		$q = get::$g_sql->prepare('SELECT `ID` FROM `users` WHERE `Email` = ? AND `Password` = ? LIMIT 1;');
		$q->execute(array($_POST['_email'], $password));
		
		if(!$q->rowCount()) return this::showalert('danger', 'Invalid email or password!', ''); 
		else {
			$row = $q->fetch(PDO::FETCH_OBJ); 

			$_SESSION['account'] = $row->ID; 
			setcookie("user",$email,time()+3600*24*60);
			setcookie("password",$pass,time()+3600*24*60);

			return this::showalert('success', 'Congratulations! You have been logged !', '');
		}
	}
}

if(isset($_POST['submit_register']) && !user::islogged())
{
	if(!$_POST['_email'] || !$_POST['_password'])
		return this::showalert('danger', 'You left blank fields !', ''); 
	else 
	{
		if(!filter_var($_POST['_email'], FILTER_VALIDATE_EMAIL)) 
			return this::showalert('danger', 'Email-ul introdus de tine nu este valid.', ''); 

		$q = get::$g_sql->prepare('SELECT `Email` FROM `users` WHERE `Email` = ? LIMIT 1;');
		$q->execute(array(this::protect($_POST['_email'])));
		
		if($q->rowCount()) return this::showalert('danger', 'This email is already taken!', ''); 

		if(strlen($_POST['_password']) < 6) 
			return this::showalert('danger', 'Password must have minimum 6 characters.', '');

		$pass = hash('whirlpool', this::protect($_POST['_password']));
		$q = get::$g_sql->prepare('INSERT INTO `users` (`Password`, `Email`) VALUES (?, ?);');
		$q->execute(array($pass, this::protect($_POST['_email'])));

		return this::showalert('success', 'Congratulations! Your account has been saved!', '');
	}
}

if(isset($_POST['submit_contact']) && user::islogged()) {
	$contact_name = this::protect($_POST['_name']);
	$contact_number = this::protect($_POST['_number']);
	$contact_adress = this::protect($_POST['_adress']);

	if(empty($contact_name) || empty($contact_number) || empty($contact_adress)) 
		return this::showalert('danger', 'You left blank fields !', ''); 
	else {
		$q = get::$g_sql->prepare('SELECT `ID` FROM `contacts` WHERE `ForAccount` = ? AND `Name` = ? OR `Number` = ? LIMIT 1;');
		$q->execute(array(user::get(), $contact_name, $contact_number));
		
		if($q->rowCount()) 
			return this::showalert('danger', 'This contact(name or number) is already in your list.', ''); 

		if(!is_numeric($contact_number)) 
			return this::showalert('danger', 'The contact number must only contain DIGITS!', ''); 
		
		if(strlen($contact_name) > 32) 
			return this::showalert('danger', 'The contact number can be up to 32 characters long.', ''); 

		$q = get::$g_sql->prepare('INSERT INTO `contacts` (`ForAccount`, `Name`, `Number`, `Adress`) VALUES (?, ?, ?, ?);');
		$q->execute(array(user::get(), $contact_name, $contact_number, $contact_adress));  

		return this::showalert('success', 'Contact successfully added!', 'mycontacts'); 
	}
}
?>

<!doctype html>
<html lang="en" class="h-100">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Digital PhoneBook">
		<meta name="author" content="GlobalScripts.Ro">
		<title><?php echo this::$_PAGE_TITLE; ?></title>
		<link rel="canonical" href="https://globalscripts.ro/">

		<!-- Bootstrap v5 -->
		<link href="<?php echo this::$_PAGE_URL; ?>public/assets/css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom CSS -->
		<link href="<?php echo this::$_PAGE_URL; ?>public/assets/css/main.css" rel="stylesheet">

		<!-- Fontawesome --> 
		<script src="https://kit.fontawesome.com/1a732d18f8.js" crossorigin="anonymous"></script>

		<!-- jQuery & DataTables --> 
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	
	</head>

	<body class="d-flex flex-column h-100">
		<div class="modal" tabindex="-1" id="loginModal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><i class="fa fa-sign-in"></i> Login</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<form method="POST">
						<div class="modal-body">
							<div class="input-group mb-3">
								<span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope"></i> </span>
								<input type="text" class="form-control" name="_email" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
							</div>

							<div class="input-group mb-3">
								<span class="input-group-text" id="basic-addon1"><i class="fa fa-key"></i> </span>
								<input type="password" name="_password" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1">
							</div>

							<div class="d-flex justify-content-center">
								<button type="submit" class="btn btn-outline-success" name="submit_login"><i class="fa fa-check-circle"></i> Login</button>
							</div>
							<center><a href="#" class="registerShow" data-bs-toggle="modal" data-bs-target="#registerModal">Don't have an account ? Register</a></center>
						</div>
					</form> 
				</div>
			</div>
		</div>

		<div class="modal" tabindex="-1" id="registerModal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><i class="fa fa-plus"></i> Register</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<form method="POST">
						<div class="modal-body">
							<div class="input-group mb-3">
								<span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope"></i> </span>
								<input type="text" class="form-control" name="_email" placeholder="Email" aria-label="Email" aria-describedby="basic-addon1">
							</div>

							<div class="input-group mb-3">
								<span class="input-group-text" id="basic-addon1"><i class="fa fa-key"></i> </span>
								<input type="password" name="_password" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1">
							</div>

							<div class="d-flex justify-content-center">
								<button type="submit" class="btn btn-outline-success" name="submit_register"><i class="fa fa-check-circle"></i> REGISTER</button>
							</div>
							
						</div>
					</form> 
				</div>
			</div>
		</div>

		<div class="modal" tabindex="-1" id="contactModal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><i class="fa fa-phone"></i> Add a contact in your phonebook</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<form method="POST">
						<div class="modal-body">
							<div class="input-group mb-3">
								<span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i> </span>
								<input type="text" class="form-control" name="_name" placeholder="Contact Name" aria-label="Contact Name" aria-describedby="basic-addon1">
							</div>

							<div class="input-group mb-3">
								<span class="input-group-text" id="basic-addon1"><i class="fa fa-phone"></i> </span>
								<input type="text" class="form-control" name="_number" placeholder="Contact Number" aria-label="Contact Number" aria-describedby="basic-addon1">
							</div>

							<div class="input-group mb-3">
								<span class="input-group-text" id="basic-addon1"><i class="fa fa-address-card"></i> </span>
								<input type="text" class="form-control" name="_adress" placeholder="Contact Adress" aria-label="Contact Adress" aria-describedby="basic-addon1">
							</div>

							<div class="d-flex justify-content-center">
								<button type="submit" class="btn btn-outline-warning" name="submit_contact"><i class="fa fa-plus-circle"></i> Add contact</button>
							</div>
							
						</div>
					</form> 
				</div>
			</div>
		</div>

		<header>
			<!-- Fixed navbar -->
			<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
				<div class="container-fluid">
					<a class="navbar-brand" href="#">Phonebook</a>
					
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>

					<div class="collapse navbar-collapse" id="navbarCollapse">
						<ul class="navbar-nav me-auto mb-2 mb-md-0">
							<li class="nav-item">
								<a class="nav-link active" aria-current="page" href="<?php echo this::$_PAGE_URL; ?>"><i class="fa fa-home"></i> Home</a>
							</li>
							<?php 
								if(user::islogged()) {

									echo '
									<li class="nav-item">
										<a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#contactModal"><i class="fa fa-phone"></i> Add new contact</a>
									</li>

									<li class="nav-item">
										<a href="'.this::$_PAGE_URL.'mycontacts" class="nav-link"><i class="fa fa-address-book"></i> My contacts</a>
									</li>
									';
								}
							?>
						</ul>
						
						<?php 
						if(!user::islogged()) {
							echo '
							<button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fa fa-sign-in"></i> LOGIN</button>
							<li></li>
							<button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#registerModal"><i class="fa fa-plus"></i> REGISTER</button>
							'; 
						}
						else {
							echo '
							<div class="dropdown">
								<a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
									'.user::getdata($_SESSION['account'], 'Email').'
								</a>

								<ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
									<li><a class="dropdown-item" href="logout"><i class="fa fa-sign-out"></i> Logout</a></li>
								</ul>
							</div>
							';
						}
						?>
						
					</div>

				</div>
			</nav>
		</header>

		<!-- Begin page content -->
		<main class="flex-shrink-0">
			<div class="container">
				<?php if(isset($_SESSION['msg'])) { echo $_SESSION['msg']; $_SESSION['msg'] = ''; } ?>
				<br>

<script>
$('.registerShow').on('click', function() {
	const loginModal = document.querySelector('#loginModal');
    const modal = bootstrap.Modal.getInstance(loginModal);    
    modal.hide(); 
});
</script>