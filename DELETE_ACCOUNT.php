<?php
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'delete') {
    $email = $_SESSION['email']; // Assuming the email is stored in the session
  
  $servername = "localhost"; 
    $username = "root"; 
    $password = ""; // Change this to your database password
    $dbname = "eduaxis"; 

    // PDO for database connection and error handling
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL to delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        // Check if the deletion was successful
        if ($stmt->rowCount() > 0) {
            // Logout sequence
            $_SESSION = array(); // Clear the session array
            session_destroy(); // Destroy the session
            header('Location: HOME PAGE.php'); // Redirect to login page
            exit;
        } else {
            echo "Failed to delete the account.";
        }
    } catch (PDOException $e) {
        die("Could not connect to the database $dbname :" . $e->getMessage());
    }
} else {
    // Not a POST request or the action is not delete
    header('Location: error_page.php'); // Redirect to an error page or back to home
    exit;
}
?>
