<?php

// This function inserts a new user into the users table.
// It first checks if the username already exists to avoid duplicates.
function insertNewUser($pdo, $username, $first_name, $last_name, $date_of_birth, $password)
{

	// check if the username is already taken
	$checkUserSql = "SELECT * FROM users WHERE username = ?";
	$checkUserSqlStmt = $pdo->prepare($checkUserSql);
	$checkUserSqlStmt->execute([$username]);

	if ($checkUserSqlStmt->rowCount() == 0) {

		// if username is available, insert the new user into the database
		$sql = "INSERT INTO users (username, first_name, last_name, date_of_birth, password) VALUES(?,?,?,?,?)";
		$stmt = $pdo->prepare($sql);
		$executeQuery = $stmt->execute([$username, $first_name, $last_name, $date_of_birth, $password]);

		if ($executeQuery) {
			$_SESSION['message'] = "User successfully inserted";
			return true;
		} else {
			$_SESSION['message'] = "An error occured from the query";
		}
	} else {
		$_SESSION['message'] = "User already exists";
	}
}

// This function handles user login by checking the username and verifying the password.
function loginUser($pdo, $username, $password)
{
	// find the user in the database by username
	$sql = "SELECT * FROM users WHERE username=?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$username]);

	if ($stmt->rowCount() == 1) {
		$userInfoRow = $stmt->fetch();
		$usernameFromDB = $userInfoRow['username'];
		$passwordFromDB = $userInfoRow['password'];

		// use password_verify to compare the entered password with the hashed one in the database
		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['username'] = $usernameFromDB; // store username in session so we know who is logged in
			$_SESSION['message'] = "Login successful!";
			return true;
		} else {
			$_SESSION['message'] = "Password is invalid, but user exists";
		}
	}

	if ($stmt->rowCount() == 0) {
		$_SESSION['message'] = "Username doesn't exist from the database. You may consider registration first";
	}
}

// This function gets all users from the users table.
function getAllUsers($pdo)
{
	$sql = "SELECT * FROM users";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

// This function gets a single user by their user_id.
function getUserByID($pdo, $user_id)
{
	$sql = "SELECT * FROM users WHERE user_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$user_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

// This function inserts a new customer into the customers table.
// It also records who added the customer using the session username.
function insertCustomer(
	$pdo,
	$username,
	$first_name,
	$last_name,
	$membership_status,
) {
	$current_user = $_SESSION['username']; // get the logged-in user to track who added this customer

	$sql = "INSERT INTO customers (username, first_name, last_name, 
		membership_status, added_by, updated_by) VALUES(?,?,?,?,?,?)";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([
		$username,
		$first_name,
		$last_name,
		$membership_status,
		$current_user,
		$current_user
	]);

	if ($executeQuery) {
		return true;
	}
}

// This function updates an existing customer's details in the customers table.
// It also records who made the update using the session username.
function updateCustomer(
	$pdo,
	$username,
	$first_name,
	$last_name,
	$membership_status,
	$customer_id
) {
	$updated_by = $_SESSION['username']; // track who updated this customer

	$sql = "UPDATE customers
				SET username = ?,
					first_name = ?,
					last_name = ?,
					membership_status = ?,
					updated_by = ?
				WHERE customer_id = ?
			";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([
		$username,
		$first_name,
		$last_name,
		$membership_status,
		$updated_by,
		$customer_id
	]);

	if ($executeQuery) {
		return true;
	}
}

// This function deletes a customer from the database.
// It also deletes all rental sessions linked to this customer first to avoid errors.
function deleteCustomer($pdo, $customer_id)
{
	// delete all rental sessions of this customer first (child records)
	$deleteCustomerSession = "DELETE FROM rental_sessions WHERE customer_id = ?";
	$deleteStmt = $pdo->prepare($deleteCustomerSession);
	$executeDeleteQuery = $deleteStmt->execute([$customer_id]);

	if ($executeDeleteQuery) {
		// then delete the customer itself (parent record)
		$sql = "DELETE FROM customers WHERE customer_id = ?";
		$stmt = $pdo->prepare($sql);
		$executeQuery = $stmt->execute([$customer_id]);

		if ($executeQuery) {
			return true;
		}
	}
}

// This function gets all customers from the customers table.
function getAllCustomers($pdo)
{
	$sql = "SELECT * FROM customers";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

// This function gets a single customer by their customer_id.
function getCustomerByID($pdo, $customer_id)
{
	$sql = "SELECT * FROM customers WHERE customer_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$customer_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

// This function gets all rental sessions for a specific customer.
// It uses a JOIN to also get the customer's full name from the customers table.
function getSessionsByCustomer($pdo, $customer_id)
{
	$sql = "SELECT 
				rental_sessions.session_id AS session_id,
				rental_sessions.pc_number AS pc_number,
				rental_sessions.hours_rented AS hours_rented,
				rental_sessions.added_by AS added_by,
				rental_sessions.date_added AS date_added,
				rental_sessions.updated_by AS updated_by,
				rental_sessions.last_updated AS last_updated,
				CONCAT(customers.first_name,' ',customers.last_name) AS customer
			FROM rental_sessions
			JOIN customers ON rental_sessions.customer_id = customers.customer_id
			WHERE rental_sessions.customer_id = ? 
			";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$customer_id]);
	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

// This function inserts a new rental session into the rental_sessions table.
// It also records who added the session using the session username.
function insertSession($pdo, $pc_number, $hours_rented, $customer_id)
{
	$current_user = $_SESSION['username']; // get the logged-in user to track who added this session
	$sql = "INSERT INTO rental_sessions (pc_number, hours_rented, added_by, updated_by, customer_id) VALUES (?,?,?,?,?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$pc_number, $hours_rented, $current_user, $current_user, $customer_id]);

	if ($executeQuery) {
		return true;
	}
}

// This function gets a single rental session by its session_id.
// It uses a JOIN to also get the customer's full name.
function getSessionByID($pdo, $session_id)
{
	$sql = "SELECT 
				rental_sessions.session_id AS session_id,
				rental_sessions.pc_number AS pc_number,
				rental_sessions.hours_rented AS hours_rented,
				rental_sessions.added_by AS added_by,
				rental_sessions.date_added AS date_added,
				rental_sessions.updated_by AS updated_by,
				rental_sessions.last_updated AS last_updated,
				CONCAT(customers.first_name,' ',customers.last_name) AS customer
			FROM rental_sessions
			JOIN customers ON rental_sessions.customer_id = customers.customer_id
			WHERE rental_sessions.session_id  = ?";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$session_id]);
	if ($executeQuery) {
		return $stmt->fetch();
	}
}

// This function updates an existing rental session's details.
// It also records who made the update using the session username.
function updateSession($pdo, $pc_number, $hours_rented, $session_id)
{
	$updated_by = $_SESSION['username']; // track who updated this session

	$sql = "UPDATE rental_sessions
			SET pc_number = ?,
				hours_rented = ?,
				updated_by = ?
			WHERE session_id = ?
			";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$pc_number, $hours_rented, $updated_by, $session_id]);

	if ($executeQuery) {
		return true;
	}
}

// This function deletes a rental session from the database by its session_id.
function deleteSession($pdo, $session_id)
{
	$sql = "DELETE FROM rental_sessions WHERE session_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$session_id]);

	if ($executeQuery) {
		return true;
	}
}

// Search for customers by matching the keyword against username, first name, or last name
function searchCustomers($pdo, $keyword)
{
	$sql = "SELECT * FROM customers 
			WHERE username LIKE ? 
			OR first_name LIKE ? 
			OR last_name LIKE ? 
			OR membership_status LIKE ?";
	$stmt = $pdo->prepare($sql);
	// wrap the keyword with % so it matches any part of the text
	$searchTerm = "%" . $keyword . "%";
	$executeQuery = $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

// Search for rental sessions within a specific customer by matching against pc number or hours rented
function searchCustomerSessions($pdo, $keyword, $customer_id)
{
	$sql = "SELECT 
				rental_sessions.session_id AS session_id,
				rental_sessions.pc_number AS pc_number,
				rental_sessions.hours_rented AS hours_rented,
				rental_sessions.added_by AS added_by,
				rental_sessions.date_added AS date_added,
				rental_sessions.updated_by AS updated_by,
				rental_sessions.last_updated AS last_updated,
				CONCAT(customers.first_name,' ',customers.last_name) AS customer
			FROM rental_sessions
			JOIN customers ON rental_sessions.customer_id = customers.customer_id
			WHERE rental_sessions.customer_id = ?
			AND (rental_sessions.pc_number LIKE ? 
			OR rental_sessions.hours_rented LIKE ?)";
	$stmt = $pdo->prepare($sql);
	// wrap the keyword with % so it matches any part of the text
	$searchTerm = "%" . $keyword . "%";
	$executeQuery = $stmt->execute([$customer_id, $searchTerm, $searchTerm]);

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

// Search all rental sessions across all customers by matching against pc number, hours, customer name, or username
function searchSessions($pdo, $keyword)
{
	$sql = "SELECT 
				rental_sessions.session_id AS session_id,
				rental_sessions.pc_number AS pc_number,
				rental_sessions.hours_rented AS hours_rented,
				rental_sessions.customer_id AS customer_id,
				rental_sessions.added_by AS added_by,
				rental_sessions.date_added AS date_added,
				rental_sessions.updated_by AS updated_by,
				rental_sessions.last_updated AS last_updated,
				CONCAT(customers.first_name,' ',customers.last_name) AS customer
			FROM rental_sessions
			JOIN customers ON rental_sessions.customer_id = customers.customer_id
			WHERE rental_sessions.pc_number LIKE ? 
			OR rental_sessions.hours_rented LIKE ?
			OR customers.first_name LIKE ?
			OR customers.last_name LIKE ?
			OR customers.username LIKE ?";
	$stmt = $pdo->prepare($sql);
	$searchTerm = "%" . $keyword . "%";
	$executeQuery = $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

// Insert a new record into the activity_logs table to track user actions
function insertActivityLog($pdo, $username, $action_type, $entity_type, $record_id, $field_changed, $old_value, $new_value)
{
	$sql = "INSERT INTO activity_logs (username, action_type, entity_type, record_id, field_changed, old_value, new_value) VALUES (?,?,?,?,?,?,?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$username, $action_type, $entity_type, $record_id, $field_changed, $old_value, $new_value]);

	if ($executeQuery) {
		return true;
	}
}

// Get all activity logs from the database ordered by most recent first
function getAllActivityLogs($pdo)
{
	$sql = "SELECT * FROM activity_logs ORDER BY date_logged DESC";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

