<?php
require_once 'core/models.php';
require_once 'core/handleForms.php';

if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>View User</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<nav>
		<a href="index.php">Return to home</a>
	</nav>

	<?php $getUserByID = getUserByID($pdo, $_GET['user_id']); ?>
	<h1>Username: <?php echo $getUserByID['username']; ?></h1>
	<h1>First Name: <?php echo $getUserByID['first_name']; ?></h1>
	<h1>Last Name: <?php echo $getUserByID['last_name']; ?></h1>
	<h1>Date of Birth: <?php echo $getUserByID['date_of_birth']; ?></h1>
	<h1>Date Joined: <?php echo $getUserByID['date_added']; ?></h1>
</body>

</html>