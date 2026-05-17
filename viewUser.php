<?php
require_once 'core/models.php';
require_once 'core/handleForms.php';

// redirect to login page if the user is not logged in
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

	<!-- get the user data from the database using the user_id from the URL -->
	<?php $getUserByID = getUserByID($pdo, $_GET['user_id']); ?>
	<h1>Username: <?php echo $getUserByID['username']; ?></h1>
	<!-- display the user's details in a card -->
	<div class="card">
		<p><strong>First Name:</strong> <?php echo $getUserByID['first_name']; ?></p>
		<p><strong>Last Name:</strong> <?php echo $getUserByID['last_name']; ?></p>
		<p><strong>Date of Birth:</strong> <?php echo $getUserByID['date_of_birth']; ?></p>
		<p><strong>Date Joined:</strong> <?php echo $getUserByID['date_added']; ?></p>
	</div>

</body>

</html>