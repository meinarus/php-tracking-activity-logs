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
	<title>View Rental Sessions</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<a href="index.php">Return to home</a>
	<?php $getAllInfoByCustomerID = getCustomerByID($pdo, $_GET['customer_id']); ?>
	<h1>Username: <?php echo $getAllInfoByCustomerID['username']; ?></h1>
	<h1>Add New Session</h1>
	<form action="core/handleForms.php?customer_id=<?php echo $_GET['customer_id']; ?>" method="POST">
		<p>
			<label for="pcNumber">PC Number</label>
			<input type="text" name="pcNumber">
		</p>
		<p>
			<label for="hoursRented">Hours Rented</label>
			<input type="number" name="hoursRented">
			<input type="submit" name="insertNewSessionBtn">
		</p>
	</form>

	<table>
		<tr>
			<th>Session ID</th>
			<th>PC Number</th>
			<th>Hours Rented</th>
			<th>Customer</th>
			<th>Added By</th>
			<th>Date Added</th>
			<th>Updated By</th>
			<th>Last Updated</th>
			<th>Action</th>
		</tr>
		<?php $getSessionsByCustomer = getSessionsByCustomer($pdo, $_GET['customer_id']); ?>
		<?php foreach ($getSessionsByCustomer as $row) { ?>
			<tr>
				<td><?php echo $row['session_id']; ?></td>
				<td><?php echo $row['pc_number']; ?></td>
				<td><?php echo $row['hours_rented']; ?></td>
				<td><?php echo $row['customer']; ?></td>
				<td><?php echo $row['added_by']; ?></td>
				<td><?php echo $row['date_added']; ?></td>
				<td><?php echo $row['updated_by']; ?></td>
				<td><?php echo $row['last_updated']; ?></td>
				<td>
					<a href="editRentalSession.php?session_id=<?php echo $row['session_id']; ?>&customer_id=<?php echo $_GET['customer_id']; ?>">Edit</a>
					<a href="deleteRentalSession.php?session_id=<?php echo $row['session_id']; ?>&customer_id=<?php echo $_GET['customer_id']; ?>">Delete</a>
				</td>
			</tr>
		<?php } ?>
	</table>


</body>

</html>