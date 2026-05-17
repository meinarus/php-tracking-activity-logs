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
	<title>Edit Customer</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<nav>
		<a href="index.php">Return to home</a>
	</nav>

	<!-- get the customer data from the database so we can pre-fill the form -->
	<?php $getCustomerByID = getCustomerByID($pdo, $_GET['customer_id']); ?>
	<h1>Edit the customer!</h1>
	<!-- form sends data to handleForms.php with customer_id in the URL -->
	<form action="core/handleForms.php?customer_id=<?php echo $_GET['customer_id']; ?>" method="POST">
		<p>
			<label for="username">Username</label>
			<input type="text" name="username" value="<?php echo $getCustomerByID['username']; ?>">
		</p>
		<p>
			<label for="firstName">First Name</label>
			<input type="text" name="firstName" value="<?php echo $getCustomerByID['first_name']; ?>">
		</p>
		<p>
			<label for="lastName">Last Name</label>
			<input type="text" name="lastName" value="<?php echo $getCustomerByID['last_name']; ?>">
		</p>
		<p>
			<label for="membershipStatus">Membership Status</label>
			<input type="text" name="membershipStatus" value="<?php echo $getCustomerByID['membership_status']; ?>">
			<input type="submit" name="editCustomerBtn">
		</p>
	</form>
</body>

</html>