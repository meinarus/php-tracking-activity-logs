<?php
require_once 'core/models.php';
require_once 'core/handleForms.php';

// redirect to login if user is not logged in
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
	<title>Activity Logs</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<a href="index.php">Return to home</a>
	<h1>Activity Logs</h1>

	<!-- display all activity logs in a read-only table -->
	<?php $getAllLogs = getAllActivityLogs($pdo); ?>
	<table>
		<tr>
			<th>Log ID</th>
			<th>Username</th>
			<th>Action</th>
			<th>Entity Type</th>
			<th>Record ID</th>
			<th>Field Changed</th>
			<th>Old Value</th>
			<th>New Value</th>
			<th>Date Logged</th>
		</tr>
		<?php foreach ($getAllLogs as $row) { ?>
			<tr>
				<td><?php echo $row['log_id']; ?></td>
				<td><?php echo $row['username']; ?></td>
				<td><?php echo $row['action_type']; ?></td>
				<td><?php echo $row['entity_type']; ?></td>
				<td><?php echo $row['record_id']; ?></td>
				<td><?php echo $row['field_changed']; ?></td>
				<td><?php echo $row['old_value']; ?></td>
				<td><?php echo $row['new_value']; ?></td>
				<td><?php echo $row['date_logged']; ?></td>
			</tr>
		<?php } ?>
	</table>


</body>

</html>
