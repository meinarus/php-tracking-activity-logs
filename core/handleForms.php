<?php
session_start();
require_once 'dbConfig.php';
require_once 'models.php';


if (isset($_POST['registerUserBtn'])) {

	$username = $_POST['username'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$date_of_birth = $_POST['date_of_birth'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

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

	$username = $_POST['username'];
	$password = $_POST['password'];

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

	$query = insertCustomer(
		$pdo,
		$_POST['username'],
		$_POST['firstName'],
		$_POST['lastName'],
		$_POST['membershipStatus'],
	);

	if ($query) {
		header("Location: ../index.php");
		exit();
	} else {
		echo "Insertion failed";
	}
}

if (isset($_POST['editCustomerBtn'])) {
	$query = updateCustomer(
		$pdo,
		$_POST['username'],
		$_POST['firstName'],
		$_POST['lastName'],
		$_POST['membershipStatus'],
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
	$query = insertSession(
		$pdo,
		$_POST['pcNumber'],
		$_POST['hoursRented'],
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
	$query = updateSession($pdo, $_POST['pcNumber'], $_POST['hoursRented'], $_GET['session_id']);

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

