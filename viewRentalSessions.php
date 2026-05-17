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

	<!-- search form for rental sessions: search by PC number or hours rented -->
	<h3>Search Sessions</h3>
	<form action="viewRentalSessions.php" method="GET" class="search-form">
		<!-- pass customer_id as a hidden field so we stay on the same customer's page -->
		<input type="hidden" name="customer_id" value="<?php echo $_GET['customer_id']; ?>">
		<p>
			<label for="search">Search by PC number or hours rented</label>
			<input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
			<input type="submit" value="Search">
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
		<?php
		// if a search keyword is provided, use searchSessions instead of getSessionsByCustomer
		if (isset($_GET['search']) && !empty($_GET['search'])) {
			$searchKeyword = sanitizeInput($_GET['search']);
			$getSessionsByCustomer = searchSessions($pdo, $searchKeyword, $_GET['customer_id']);
		} else {
			$getSessionsByCustomer = getSessionsByCustomer($pdo, $_GET['customer_id']);
		}
		?>
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