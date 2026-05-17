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
	<title>Internet Cafe Management System</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<h1>Welcome To Internet Cafe Management System.</h1>

	<?php if (isset($_SESSION['message'])) { ?>
		<h1 style="color: red;"><?php echo $_SESSION['message']; ?></h1>
	<?php }
	unset($_SESSION['message']); ?>

	<h2>Hello there, <?php echo $_SESSION['username']; ?>!</h2>
	<nav>
		<a href="core/handleForms.php?logoutAUser=1">Logout</a>
		<!-- link to the activity logs page so users can view all recorded actions -->
		<a href="viewActivityLogs.php">View Activity Logs</a>
	</nav>

	<h3>Users List</h3>
	<ul>
		<?php $getAllUsers = getAllUsers($pdo); ?>
		<?php foreach ($getAllUsers as $row) { ?>
			<li>
				<a href="viewUser.php?user_id=<?php echo $row['user_id']; ?>"><?php echo $row['username']; ?></a>
			</li>
		<?php } ?>
	</ul>

	<h2>Add new Customers!</h2>
	<form action="core/handleForms.php" method="POST">
		<p>
			<label for="username">Username</label>
			<input type="text" name="username">
		</p>
		<p>
			<label for="firstName">First Name</label>
			<input type="text" name="firstName">
		</p>
		<p>
			<label for="lastName">Last Name</label>
			<input type="text" name="lastName">
		</p>
		<p>
			<label for="membershipStatus">Membership Status</label>
			<input type="text" name="membershipStatus">
			<input type="submit" name="insertCustomerBtn">
		</p>
	</form>

	<!-- search form: searches both customers and rental sessions -->
	<h3>Search Customers & Rental Sessions</h3>
	<form action="index.php" method="GET" class="search-form">
		<p>
			<label for="search">Search by username, name, status, PC number, or hours</label>
			<input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
			<input type="submit" value="Search">
		</p>
	</form>

	<?php
	// if a search keyword is provided, use searchCustomers instead of getAllCustomers
	if (isset($_GET['search']) && !empty($_GET['search'])) {
		$searchKeyword = sanitizeInput($_GET['search']);
		$getAllCustomers = searchCustomers($pdo, $searchKeyword);
	} else {
		$getAllCustomers = getAllCustomers($pdo);
	}
	?>
	<?php if (isset($_GET['search']) && !empty($_GET['search'])) { ?>
		<h3>Customers Results</h3>
	<?php } ?>
	<table>
		<tr>
			<th>Username</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Membership Status</th>
			<th>Added By</th>
			<th>Date Added</th>
			<th>Updated By</th>
			<th>Last Updated</th>
			<th>Action</th>
		</tr>
		<?php foreach ($getAllCustomers as $row) { ?>
			<tr>
				<td><?php echo $row['username']; ?></td>
				<td><?php echo $row['first_name']; ?></td>
				<td><?php echo $row['last_name']; ?></td>
				<td><?php echo $row['membership_status']; ?></td>
				<td><?php echo $row['added_by']; ?></td>
				<td><?php echo $row['date_added']; ?></td>
				<td><?php echo $row['updated_by']; ?></td>
				<td><?php echo $row['last_updated']; ?></td>
				<td>
					<a href="viewRentalSessions.php?customer_id=<?php echo $row['customer_id']; ?>">View Sessions</a>
					<a href="editCustomer.php?customer_id=<?php echo $row['customer_id']; ?>">Edit</a>
					<a href="deleteCustomer.php?customer_id=<?php echo $row['customer_id']; ?>">Delete</a>
				</td>
			</tr>
		<?php } ?>
	</table>

	<?php
	// if a search keyword is provided, also show matching rental sessions
	if (isset($_GET['search']) && !empty($_GET['search'])) {
		$searchedSessions = searchSessions($pdo, $searchKeyword);
	?>
		<h3 class="session-results">Rental Sessions Results</h3>
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
			<?php foreach ($searchedSessions as $session) { ?>
				<tr>
					<td><?php echo $session['session_id']; ?></td>
					<td><?php echo $session['pc_number']; ?></td>
					<td><?php echo $session['hours_rented']; ?></td>
					<td><?php echo $session['customer']; ?></td>
					<td><?php echo $session['added_by']; ?></td>
					<td><?php echo $session['date_added']; ?></td>
					<td><?php echo $session['updated_by']; ?></td>
					<td><?php echo $session['last_updated']; ?></td>
					<td>
						<a href="editRentalSession.php?session_id=<?php echo $session['session_id']; ?>&customer_id=<?php echo $session['customer_id']; ?>">Edit</a>
						<a href="deleteRentalSession.php?session_id=<?php echo $session['session_id']; ?>&customer_id=<?php echo $session['customer_id']; ?>">Delete</a>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
</body>

</html>