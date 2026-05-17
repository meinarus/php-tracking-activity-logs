<?php
session_start();
require_once 'dbConfig.php';
require_once 'models.php';
require_once 'validate.php'; // import the sanitizeInput() function to prevent XSS


// handle user registration form submission
if (isset($_POST['registerUserBtn'])) {

	// sanitize the inputs to prevent XSS before storing in the database
	$username = sanitizeInput($_POST['username']);
	$first_name = sanitizeInput($_POST['first_name']);
	$last_name = sanitizeInput($_POST['last_name']);
	$date_of_birth = sanitizeInput($_POST['date_of_birth']);
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($date_of_birth) && !empty($password) && !empty($confirm_password)) {

		// check if both passwords match first
		if ($password == $confirm_password) {

			// validate if the password is secure enough (minimum of 8 characters, uppercase, lowercase, and numbers)
			if (validatePassword($password)) {

				$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, $date_of_birth, password_hash($password, PASSWORD_DEFAULT));

				if ($insertQuery) {
					header("Location: ../login.php");
					exit();
				} else {
					header("Location: ../register.php");
					exit();
				}
			} else {
				$_SESSION['message'] = "Password should be a minimum of 8 characters and should contain both uppercase, lowercase, and numbers";
				header("Location: ../register.php");
				exit();
			}
		} else {
			$_SESSION['message'] = "Please check if both passwords are equal!";
			header("Location: ../register.php");
			exit();
		}
	} else {
		$_SESSION['message'] = "Please make sure the input fields are not empty for registration!";

		header("Location: ../register.php");
		exit();
	}
}

// handle user login form submission
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

// handle user logout by clearing the session and redirecting to login page
if (isset($_GET['logoutAUser'])) {
	unset($_SESSION['username']); // remove the username from session to log them out
	header('Location: ../login.php');
	exit();
}

// handle inserting a new customer
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
		// log the insert action into activity_logs table
		$fields = "first_name, last_name, username, membership_status";
		$newValues = $firstName . ", " . $lastName . ", " . $username . ", " . $membershipStatus;
		insertActivityLog($pdo, $_SESSION['username'], "INSERT", "Customer", $pdo->lastInsertId(), $fields, "-", $newValues);

		header("Location: ../index.php");
		exit();
	} else {
		echo "Insertion failed";
	}
}

// handle editing/updating a customer
if (isset($_POST['editCustomerBtn'])) {
	// sanitize all customer inputs to prevent XSS when editing
	$username = sanitizeInput($_POST['username']);
	$firstName = sanitizeInput($_POST['firstName']);
	$lastName = sanitizeInput($_POST['lastName']);
	$membershipStatus = sanitizeInput($_POST['membershipStatus']);

	// get the old customer data before updating so we can log what changed
	$oldCustomer = getCustomerByID($pdo, $_GET['customer_id']);

	$query = updateCustomer(
		$pdo,
		$username,
		$firstName,
		$lastName,
		$membershipStatus,
		$_GET['customer_id']
	);

	if ($query) {
		// compare old and new values to find which fields actually changed
		$changedFields = [];
		$oldValues = [];
		$newValues = [];

		if ($oldCustomer['first_name'] != $firstName) {
			$changedFields[] = "first_name";
			$oldValues[] = $oldCustomer['first_name'];
			$newValues[] = $firstName;
		}
		if ($oldCustomer['last_name'] != $lastName) {
			$changedFields[] = "last_name";
			$oldValues[] = $oldCustomer['last_name'];
			$newValues[] = $lastName;
		}
		if ($oldCustomer['username'] != $username) {
			$changedFields[] = "username";
			$oldValues[] = $oldCustomer['username'];
			$newValues[] = $username;
		}
		if ($oldCustomer['membership_status'] != $membershipStatus) {
			$changedFields[] = "membership_status";
			$oldValues[] = $oldCustomer['membership_status'];
			$newValues[] = $membershipStatus;
		}

		// only log if something actually changed
		if (!empty($changedFields)) {
			insertActivityLog($pdo, $_SESSION['username'], "UPDATE", "Customer", $_GET['customer_id'], implode(", ", $changedFields), implode(", ", $oldValues), implode(", ", $newValues));
		}

		header("Location: ../index.php");
		exit();
	} else {
		echo "Edit failed";
	}
}

// handle deleting a customer
if (isset($_POST['deleteCustomerBtn'])) {
	// get customer details before deleting so we can log it properly
	$customerToDelete = getCustomerByID($pdo, $_GET['customer_id']);

	$query = deleteCustomer($pdo, $_GET['customer_id']);

	if ($query) {
		// log the delete action into activity_logs table
		$fields = "first_name, last_name, username";
		$oldValues = $customerToDelete['first_name'] . ", " . $customerToDelete['last_name'] . ", " . $customerToDelete['username'];
		insertActivityLog($pdo, $_SESSION['username'], "DELETE", "Customer", $_GET['customer_id'], $fields, $oldValues, "-");

		header("Location: ../index.php");
		exit();
	} else {
		echo "Deletion failed";
	}
}

// handle inserting a new rental session for a customer
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
		// get the new session id right away before any other query resets it
		$newSessionId = $pdo->lastInsertId();

		// get customer name to include in the activity log
		$customer = getCustomerByID($pdo, $_GET['customer_id']);
		$fields = "pc_number, hours_rented, customer";
		$newValues = $pcNumber . ", " . $hoursRented . ", " . $customer['first_name'] . " " . $customer['last_name'];
		insertActivityLog($pdo, $_SESSION['username'], "INSERT", "Rental Session", $newSessionId, $fields, "-", $newValues);

		header("Location: ../viewRentalSessions.php?customer_id=" . $_GET['customer_id']);
		exit();
	} else {
		echo "Insertion failed";
	}
}

// handle editing/updating a rental session
if (isset($_POST['editSessionBtn'])) {
	// sanitize session inputs to prevent XSS when editing
	$pcNumber = sanitizeInput($_POST['pcNumber']);
	$hoursRented = sanitizeInput($_POST['hoursRented']);

	// get the old session data before updating so we can log what changed
	$oldSession = getSessionByID($pdo, $_GET['session_id']);

	$query = updateSession($pdo, $pcNumber, $hoursRented, $_GET['session_id']);

	if ($query) {
		// compare old and new values to find which fields actually changed
		$changedFields = [];
		$oldValues = [];
		$newValues = [];

		if ($oldSession['pc_number'] != $pcNumber) {
			$changedFields[] = "pc_number";
			$oldValues[] = $oldSession['pc_number'];
			$newValues[] = $pcNumber;
		}
		if ($oldSession['hours_rented'] != $hoursRented) {
			$changedFields[] = "hours_rented";
			$oldValues[] = $oldSession['hours_rented'];
			$newValues[] = $hoursRented;
		}

		// only log if something actually changed
		if (!empty($changedFields)) {
			insertActivityLog($pdo, $_SESSION['username'], "UPDATE", "Rental Session", $_GET['session_id'], implode(", ", $changedFields), implode(", ", $oldValues), implode(", ", $newValues));
		}

		header("Location: ../viewRentalSessions.php?customer_id=" . $_GET['customer_id']);
		exit();
	} else {
		echo "Update failed";
	}
}

// handle deleting a rental session
if (isset($_POST['deleteSessionBtn'])) {
	// get session details before deleting so we can log it properly
	$sessionToDelete = getSessionByID($pdo, $_GET['session_id']);

	$query = deleteSession($pdo, $_GET['session_id']);

	if ($query) {
		// log the delete action into activity_logs table
		$fields = "pc_number, hours_rented, customer";
		$oldValues = $sessionToDelete['pc_number'] . ", " . $sessionToDelete['hours_rented'] . ", " . $sessionToDelete['customer'];
		insertActivityLog($pdo, $_SESSION['username'], "DELETE", "Rental Session", $_GET['session_id'], $fields, $oldValues, "-");

		header("Location: ../viewRentalSessions.php?customer_id=" . $_GET['customer_id']);
		exit();
	} else {
		echo "Deletion failed";
	}
}
