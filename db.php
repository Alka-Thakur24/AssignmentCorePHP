<?php
class Database
{
    private $host = 'localhost'; // Your database host
    private $db_name = 'assignment'; // Your database name
    private $username = 'root'; // Your database username
    private $password = ''; // Your database password
    private $conn;

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log the error to a file
            $this->logError($e->getMessage());
            echo "Database connection failed. Check the log for details.";
        }

        return $this->conn;
    }

    // Function to log errors
    private function logError($errorMessage)
    {
        $logFile = 'db_error_log.txt'; // Path to the log file
        $currentDateTime = date('Y-m-d H:i:s'); // Get the current date and time

        // Prepare the error message
        $logMessage = "[{$currentDateTime}] Database Error: {$errorMessage}\n";

        // Append the error message to the log file
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
