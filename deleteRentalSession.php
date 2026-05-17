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
	<title>Delete Rental Session</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<nav>
		<a href="viewRentalSessions.php?customer_id=<?php echo $_GET['customer_id']; ?>">Return to sessions</a>
	</nav>

	<!-- get the session data to display its details before confirming deletion -->
	<?php $getSessionByID = getSessionByID($pdo, $_GET['session_id']); ?>
	<!-- show confirmation message before deleting -->
	<h1>Are you sure you want to delete this session?</h1>
	<!-- display the session info in a card so the user knows what they are deleting -->
	<div class="card">
		<p><strong>PC Number:</strong> <?php echo $getSessionByID['pc_number']; ?></p>
		<p><strong>Hours Rented:</strong> <?php echo $getSessionByID['hours_rented']; ?></p>
		<p><strong>Customer:</strong> <?php echo $getSessionByID['customer']; ?></p>
		<p><strong>Added By:</strong> <?php echo $getSessionByID['added_by']; ?></p>
		<p><strong>Date Added:</strong> <?php echo $getSessionByID['date_added']; ?></p>
		<p><strong>Updated By:</strong> <?php echo $getSessionByID['updated_by']; ?></p>
		<p><strong>Last Updated:</strong> <?php echo $getSessionByID['last_updated']; ?></p>

		<!-- form that sends the delete request to handleForms.php -->
		<form action="core/handleForms.php?session_id=<?php echo $_GET['session_id']; ?>&customer_id=<?php echo $_GET['customer_id']; ?>" method="POST">
			<input type="submit" name="deleteSessionBtn" value="Delete" style="background-color: #ef4444; color: white;">
		</form>
	</div>
</body>

</html>