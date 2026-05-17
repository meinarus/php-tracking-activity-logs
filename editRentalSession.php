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
	<title>Edit Rental Session</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<nav>
		<a href="viewRentalSessions.php?customer_id=<?php echo $_GET['customer_id']; ?>">Return to sessions</a>
	</nav>

	<h1>Edit the session!</h1>
	<!-- get the session data from the database so we can pre-fill the form -->
	<?php $getSessionByID = getSessionByID($pdo, $_GET['session_id']); ?>
	<!-- form sends data to handleForms.php with session_id and customer_id in the URL -->
	<form action="core/handleForms.php?session_id=<?php echo $_GET['session_id']; ?>
	&customer_id=<?php echo $_GET['customer_id']; ?>" method="POST">
		<p>
			<label for="pcNumber">PC Number</label>
			<input type="text" name="pcNumber"
				value="<?php echo $getSessionByID['pc_number']; ?>">
		</p>
		<p>
			<label for="hoursRented">Hours Rented</label>
			<input type="number" name="hoursRented"
				value="<?php echo $getSessionByID['hours_rented']; ?>">
			<input type="submit" name="editSessionBtn">
		</p>
	</form>
</body>

</html>