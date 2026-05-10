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
