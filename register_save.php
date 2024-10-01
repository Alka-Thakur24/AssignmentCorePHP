<?php
header('Content-Type: application/json');

include_once 'db.php';

$db = new Database();
$pdo = $db->connect();

// echo $pdo;
function validate($data)
{
    $errors = [];

    if (empty($data['fname'])) {
        $errors['fname'] = 'First name is required.';
    }

    if (empty($data['lname'])) {
        $errors['lname'] = 'Last name is required.';
    }

    if (empty($data['timezone'])) {
        $errors['timezone'] = 'Timezone is required.';
    }

    if (empty($data['email'])) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($data['hobbies'])) {
        $errors['hobbies'] = 'Please choose atleast 1 hobbies.';
    } elseif (count($data['hobbies']) < 1) {
        $errors['hobbies'] = 'Please choose atleast 1 hobbies.';
    }

    return $errors;
}

if ($pdo) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form data
        try {

            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $email = trim($_POST['email']);
            $timezone = trim($_POST['timezone']);
            $hobbies = isset($_POST['hobbies']) ? $_POST['hobbies'] : [];

            $data = [
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'timezone' => $timezone,
                'hobbies' => $hobbies
            ];

            // Validate inputs
            $errors = validate($data);

            if (empty($errors)) {
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, timezone) VALUES (:fname, :lname, :email, :timezone)");
                $stmt->execute([
                    ':fname' => $fname,
                    ':lname' => $lname,
                    ':email' =>
                    $email,
                    ':timezone' => $timezone
                ]);


                $user_id = $pdo->lastInsertId();

                $hobby_stmt = $pdo->prepare("INSERT INTO hobbies (user_id, hobbies) VALUES (:user_id, :hobby)");

                foreach ($hobbies as $hobby) {
                    $hobby_stmt->execute([
                        ':user_id' => $user_id,
                        ':hobby' => $hobby
                    ]);
                }

                // Simulated success response
                $response = [
                    'success' => true,
                    'message' => 'Registration successful!'
                ];
            } else {
                // Return validation errors
                $response = [
                    'success' => false,
                    'errors' => $errors
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'errors' => $th->getMessage()
            ];
        }
        echo json_encode($response);
        exit;
    }
}
