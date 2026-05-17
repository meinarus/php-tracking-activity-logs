<?php

function insertNewUser($pdo, $username, $first_name, $last_name, $date_of_birth, $password)
{

	$checkUserSql = "SELECT * FROM users WHERE username = ?";
	$checkUserSqlStmt = $pdo->prepare($checkUserSql);
	$checkUserSqlStmt->execute([$username]);

	if ($checkUserSqlStmt->rowCount() == 0) {

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

function loginUser($pdo, $username, $password)
{
	$sql = "SELECT * FROM users WHERE username=?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$username]);

	if ($stmt->rowCount() == 1) {
		$userInfoRow = $stmt->fetch();
		$usernameFromDB = $userInfoRow['username'];
		$passwordFromDB = $userInfoRow['password'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['username'] = $usernameFromDB;
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

function getAllUsers($pdo)
{
	$sql = "SELECT * FROM users";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getUserByID($pdo, $user_id)
{
	$sql = "SELECT * FROM users WHERE user_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$user_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function insertCustomer(
	$pdo,
	$username,
	$first_name,
	$last_name,
	$membership_status,
) {
	$current_user = $_SESSION['username'];

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

function updateCustomer(
	$pdo,
	$username,
	$first_name,
	$last_name,
	$membership_status,
	$customer_id
) {
	$updated_by = $_SESSION['username'];

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

function deleteCustomer($pdo, $customer_id)
{
	$deleteCustomerSession = "DELETE FROM rental_sessions WHERE customer_id = ?";
	$deleteStmt = $pdo->prepare($deleteCustomerSession);
	$executeDeleteQuery = $deleteStmt->execute([$customer_id]);

	if ($executeDeleteQuery) {
		$sql = "DELETE FROM customers WHERE customer_id = ?";
		$stmt = $pdo->prepare($sql);
		$executeQuery = $stmt->execute([$customer_id]);

		if ($executeQuery) {
			return true;
		}
	}
}

function getAllCustomers($pdo)
{
	$sql = "SELECT * FROM customers";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getCustomerByID($pdo, $customer_id)
{
	$sql = "SELECT * FROM customers WHERE customer_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$customer_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

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

function insertSession($pdo, $pc_number, $hours_rented, $customer_id)
{
	$current_user = $_SESSION['username'];
	$sql = "INSERT INTO rental_sessions (pc_number, hours_rented, added_by, updated_by, customer_id) VALUES (?,?,?,?,?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$pc_number, $hours_rented, $current_user, $current_user, $customer_id]);

	if ($executeQuery) {
		return true;
	}
}

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

function updateSession($pdo, $pc_number, $hours_rented, $session_id)
{
	$updated_by = $_SESSION['username'];

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

// Search for rental sessions by matching the keyword against pc number, hours rented, or customer name
function searchSessions($pdo, $keyword, $customer_id)
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
