<?php
include('db.php');

class MyController
{

    public $pdo;
    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->connect();
    }

    function getTimezones()
    {
        $timezones = [];
        $offsets = [];
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $now->setTimezone(new DateTimeZone($timezone));
            $offsets[] = $offset = $now->getOffset();
            $timezones[$timezone] = '(' . $this->format_GMT_offset($offset) . ') ' . $this->format_timezone_name($timezone);
        }

        array_multisort($offsets, $timezones);
        return $timezones;
    }
    function format_GMT_offset($offset)
    {
        $hours = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));
        return 'GMT' . ($offset !== false ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    function format_timezone_name($name)
    {
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);
        return $name;
    }

    function list()
    {

        try {
            $sql = "SELECT u.*, GROUP_CONCAT(h.hobbies) AS hobbies
            FROM users u
            LEFT JOIN hobbies h ON u.id = h.user_id
            GROUP BY u.id, u.first_name, u.timezone";

            $stmt = $this->pdo->query($sql);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Prepare an array to store users and their matching users
            $users_with_matches = [];
            // Step 2: For each user, find matching users with the same timezone and at least one matching hobby
            foreach ($users as &$user) {
                // Convert the hobbies string to an array
                $user_hobbies = explode(',', $user['hobbies']);

                // Prepare an SQL query to find matching users
                $sql_matching_users = "SELECT u.first_name
                               FROM users u
                               LEFT JOIN hobbies h ON u.id = h.user_id
                               WHERE u.id != :user_id
                               AND u.timezone = :timezone
                               AND h.hobbies IN ('" . implode("','", $user_hobbies) . "')
                               GROUP BY u.id, u.first_name";

                // Prepare the statement
                $stmt_matching = $this->pdo->prepare($sql_matching_users);
                $stmt_matching->execute([
                    ':user_id' => $user['id'],
                    ':timezone' => $user['timezone']
                ]);

                // Fetch matching users' details
                $matching_users = $stmt_matching->fetchAll(PDO::FETCH_ASSOC);

                // Add the matching users to the current user data
                $user['matching_users'] = $matching_users;

                // Store the user with their matching users in the final array
                $users_with_matches[] = $user;
            }

            // Output the result
            // echo "<pre>";
            return $users_with_matches;
            // echo "</pre>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
