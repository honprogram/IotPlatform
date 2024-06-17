<?php
header('Content-Type: application/json');

include_once 'classes/autoload.php';
$db = new Database();

if (isset($_GET['tkn']) && !empty($_GET['tkn'])) {
    $token = $_GET['tkn'];

    // Query to check if token exists in the database
    $check_token_query = $db->conn->prepare("SELECT * FROM `user` WHERE `token` = ?");
    $check_token_query->bind_param('s', $token);
    $check_token_query->execute();
    $result = $check_token_query->get_result();

    if ($result->num_rows == 1) {
        $update_query = $db->conn->prepare("UPDATE `user` SET `active` = 1 WHERE `token` = ?");
        $update_query->bind_param('s', $token);
        $update_result = $update_query->execute();

        if ($update_result) {
            echo json_encode(array('status' => 'success', 'message' => 'All IDs activated'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to update active status'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Invalid token'));
    }

    $check_token_query->close();
    $db->conn->close();
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Token not provided'));
}
