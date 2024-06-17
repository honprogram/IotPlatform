<?php
header('Content-Type: application/json');

include_once 'classes/autoload.php';
$db = new Database();

function generateToken($length = 20)
{
    return bin2hex(random_bytes($length / 2));
}

if (isset($_GET['username']) && isset($_GET['password'])) {
    $username = $_GET['username'];
    $password = $_GET['password'];

    // Validate user credentials
    $query = $db->conn->prepare("SELECT * FROM `user` WHERE `username` = ?");
    $query->bind_param('s', $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify password hash
        if (password_verify($password, $user['password'])) {
            // Password is correct, generate new token
            $token = generateToken();

            // Update user token in the database
            $update_query = $db->conn->prepare("UPDATE `user` SET `token` = ? WHERE `username` = ?");
            $update_query->bind_param('ss', $token, $username);
            $update_result = $update_query->execute();

            if ($update_result) {
                echo json_encode(
                    array(
                        'status' => 'success',
                        'username' => $username,
                        'token' => $token
                    )
                );
            } else {
                echo json_encode(array('status' => 'Failed to update token'));
            }
        } else {
            echo json_encode(array('status' => 'Invalid password'));
        }
    } else {
        echo json_encode(array('status' => 'User not found'));
    }

    $query->close();

} else {
    echo json_encode(array('status' => 'Invalid request'));
}

$db->conn->close();
