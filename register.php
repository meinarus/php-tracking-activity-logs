<?php
require_once 'core/models.php';
require_once 'core/handleForms.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<h1>Register here!</h1>
	<!-- display session message if there is one -->
	<?php if (isset($_SESSION['message'])) { ?>
		<h1 style="color: red;"><?php echo $_SESSION['message']; ?></h1>
	<?php }
	unset($_SESSION['message']); ?>
	<!-- registration form: sends data to handleForms.php via POST -->
	<form action="core/handleForms.php" method="POST">
		<p>
			<label for="username">Username</label>
			<input type="text" name="username">
		</p>
		<p>
			<label for="firstName">First Name</label>
			<input type="text" name="first_name">
		</p>
		<p>
			<label for="lastName">Last Name</label>
			<input type="text" name="last_name">
		</p>
		<p>
			<label for="dateOfBirth">Date of Birth</label>
			<input type="date" name="date_of_birth">
		</p>
		<p>
			<label for="password">Password</label>
			<input type="password" name="password">
		</p>
		<p>
			<label for="confirmPassword">Confirm Password</label>
			<input type="password" name="confirm_password">
			<input type="submit" name="registerUserBtn">
		</p>
	</form>
</body>

</html>