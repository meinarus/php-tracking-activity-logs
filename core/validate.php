<?php
// This function sanitizes the input/data given to it to prevent XSS attacks.
// It uses three PHP functions to clean the data before storing it in the database.
function sanitizeInput($data)
{
    $data = trim($data); // removes extra whitespace from the beginning and end
    $data = stripslashes($data); // removes backslashes from the data
    $data = htmlspecialchars($data); // converts special characters like < > to HTML entities so scripts won't run
    return $data;
}

// This function checks if the password is secure enough before storing it in the database.
// It checks if the password has a minimum of 8 characters and contains uppercase, lowercase, and numbers.
function validatePassword($password)
{
    if (strlen($password) >= 8) {
        $hasLower = false;
        $hasUpper = false;
        $hasNumber = false;

        for ($i = 0; $i < strlen($password); $i++) {
            if (ctype_lower($password[$i])) {
                $hasLower = true;
            } elseif (ctype_upper($password[$i])) {
                $hasUpper = true;
            } elseif (ctype_digit($password[$i])) {
                $hasNumber = true;
            }

            if ($hasLower && $hasUpper && $hasNumber) {
                return true;
            }
        }
    } else {
        return false;
    }
}
