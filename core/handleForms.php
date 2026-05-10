<?php
session_start();
require_once 'dbConfig.php';
require_once 'models.php';
require_once 'validate.php'; // import the sanitizeInput() function to prevent XSS


if (isset($_POST['registerUserBtn'])) {

	// sanitize the inputs to prevent XSS before storing in the database
	$username = sanitizeInput($_POST['username']);
	$first_name = sanitizeInput($_POST['first_name']);
	$last_name = sanitizeInput($_POST['last_name']);
	$date_of_birth = sanitizeInput($_POST['date_of_birth']);
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // password is not sanitized because it gets hashed

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($date_of_birth) && !empty($password)) {

		$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, $date_of_birth, $password);

		if ($insertQuery) {
			header("Location: ../login.php");
			exit();
		} else {
			header("Location: ../register.php");
			exit();
		}
	} else {
		$_SESSION['message'] = "Please make sure the input fields 
		are not empty for registration!";

		header("Location: ../register.php");
		exit();
	}
}

if (isset($_POST['loginUserBtn'])) {

	// sanitize the username input to prevent XSS
	$username = sanitizeInput($_POST['username']);
	$password = $_POST['password']; // password is not sanitized because it needs to match the hashed version

	if (!empty($username) && !empty($password)) {

		$loginQuery = loginUser($pdo, $username, $password);

		if ($loginQuery) {
			header("Location: ../index.php");
			exit();
		} else {
			header("Location: ../login.php");
			exit();
		}
	} else {
		$_SESSION['message'] = "Please make sure the input fields 
		are not empty for the login!";
		header("Location: ../login.php");
		exit();
	}
}

if (isset($_GET['logoutAUser'])) {
	unset($_SESSION['username']);
	header('Location: ../login.php');
	exit();
}

if (isset($_POST['insertCustomerBtn'])) {

	// sanitize all customer inputs to prevent XSS
	$username = sanitizeInput($_POST['username']);
	$firstName = sanitizeInput($_POST['firstName']);
	$lastName = sanitizeInput($_POST['lastName']);
	$membershipStatus = sanitizeInput($_POST['membershipStatus']);

	$query = insertCustomer(
		$pdo,
		$username,
		$firstName,
		$lastName,
		$membershipStatus,
	);

	if ($query) {
		header("Location: ../index.php");
		exit();
	} else {
		echo "Insertion failed";
	}
}

if (isset($_POST['editCustomerBtn'])) {
	// sanitize all customer inputs to prevent XSS when editing
	$username = sanitizeInput($_POST['username']);
	$firstName = sanitizeInput($_POST['firstName']);
	$lastName = sanitizeInput($_POST['lastName']);
	$membershipStatus = sanitizeInput($_POST['membershipStatus']);

	$query = updateCustomer(
		$pdo,
		$username,
		$firstName,
		$lastName,
		$membershipStatus,
		$_GET['customer_id']
	);

	if ($query) {
		header("Location: ../index.php");
		exit();
	} else {
		echo "Edit failed";
	}
}

if (isset($_POST['deleteCustomerBtn'])) {
	$query = deleteCustomer($pdo, $_GET['customer_id']);

	if ($query) {
		header("Location: ../index.php");
		exit();
	} else {
		echo "Deletion failed";
	}
}

if (isset($_POST['insertNewSessionBtn'])) {
	// sanitize session inputs to prevent XSS
	$pcNumber = sanitizeInput($_POST['pcNumber']);
	$hoursRented = sanitizeInput($_POST['hoursRented']);

	$query = insertSession(
		$pdo,
		$pcNumber,
		$hoursRented,
		$_GET['customer_id']
	);

	if ($query) {
		header("Location: ../viewRentalSessions.php?customer_id=" . $_GET['customer_id']);
		exit();
	} else {
		echo "Insertion failed";
	}
}

if (isset($_POST['editSessionBtn'])) {
	// sanitize session inputs to prevent XSS when editing
	$pcNumber = sanitizeInput($_POST['pcNumber']);
	$hoursRented = sanitizeInput($_POST['hoursRented']);

	$query = updateSession($pdo, $pcNumber, $hoursRented, $_GET['session_id']);

	if ($query) {
		header("Location: ../viewRentalSessions.php?customer_id=" . $_GET['customer_id']);
		exit();
	} else {
		echo "Update failed";
	}
}

if (isset($_POST['deleteSessionBtn'])) {
	$query = deleteSession($pdo, $_GET['session_id']);

	if ($query) {
		header("Location: ../viewRentalSessions.php?customer_id=" . $_GET['customer_id']);
		exit();
	} else {
		echo "Deletion failed";
	}
}
