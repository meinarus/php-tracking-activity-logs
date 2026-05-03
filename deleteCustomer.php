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
	<title>Delete Customer</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<a href="index.php">Return to home</a>
	<h1>Are you sure you want to delete this customer?</h1>
	<?php $getCustomerByID = getCustomerByID($pdo, $_GET['customer_id']); ?>
	<div class="card">
		<p><strong>Username:</strong> <?php echo $getCustomerByID['username']; ?></p>
		<p><strong>First Name:</strong> <?php echo $getCustomerByID['first_name']; ?></p>
		<p><strong>Last Name:</strong> <?php echo $getCustomerByID['last_name']; ?></p>
		<p><strong>Membership Status:</strong> <?php echo $getCustomerByID['membership_status']; ?></p>
		<p><strong>Added By:</strong> <?php echo $getCustomerByID['added_by']; ?></p>
		<p><strong>Date Added:</strong> <?php echo $getCustomerByID['date_added']; ?></p>
		<p><strong>Updated By:</strong> <?php echo $getCustomerByID['updated_by']; ?></p>
		<p><strong>Last Updated:</strong> <?php echo $getCustomerByID['last_updated']; ?></p>

		<form action="core/handleForms.php?customer_id=<?php echo $_GET['customer_id']; ?>" method="POST">
			<input type="submit" name="deleteCustomerBtn" value="Delete" style="background-color: #ef4444; color: white;">
		</form>
	</div>
</body>

</html>