<?php
header('Content-Type: application/json');

include_once 'classes/autoload.php';
$db = new Database();

function generateToken($length = 20)
{
    return bin2hex(random_bytes($length / 2));
}

function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

if (isset($_GET['id']) && isset($_GET['password'])) {
    $clientID = $_GET['id'];
    $password = $_GET['password'];

    // Generate a random token
    $token = generateToken(20); // Generate a longer token if needed

    $username = "user" . $clientID;

    $hashedPassword = hashPassword($password);

    $tableName = "id_" . $clientID;

    $findTable = $db->conn->query("SHOW TABLES LIKE '$tableName' ");

    if (mysqli_num_rows($findTable) > 0) {
        echo json_encode(array('status' => 'The entered ID is duplicate'));
    } else {
        $createTable = $db->conn->query("CREATE TABLE $tableName (
            ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            clientID VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            token VARCHAR(255) NOT NULL,
            ip VARCHAR(255),
            ina INT DEFAULT 0,
            inb INT DEFAULT 0,
            inc INT DEFAULT 0,
            ind INT DEFAULT 0,
            ine INT DEFAULT 0,
            inf INT DEFAULT 0,
            ing INT DEFAULT 0,
            inh INT DEFAULT 0,
            sena VARCHAR(4) DEFAULT 'off',
            senb VARCHAR(4) DEFAULT 'off',
            senc VARCHAR(4) DEFAULT 'off',
            send VARCHAR(4) DEFAULT 'off',
            sene VARCHAR(4) DEFAULT 'off',
            senf VARCHAR(4) DEFAULT 'off',
            seng VARCHAR(4) DEFAULT 'off',
            senh VARCHAR(4) DEFAULT 'off',
            timestamp INT,
            time_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        if ($createTable) {
            $createUser = $db->conn->query("INSERT INTO `user` (`clientID`, `username`, `password`, `token`) 
                                            VALUES ('$clientID', '$username', '$hashedPassword', '$token')");

            if ($createUser) {
                echo json_encode(
                    array(
                        'status' => 'Data inserted',
                        'clientID' => $clientID,
                        'token' => $token,
                        'username' => $username,
                        'date' => date("Y-m-d"),
                        'time' => date('H:i:s')
                    )
                );
            } else {
                echo json_encode(array('status' => 'User not created'));
            }
        } else {
            echo json_encode(array('status' => 'Failed to create table'));
        }
    }

} else {
    echo json_encode(array('status' => 'Invalid request'));
}

$db->conn->close();
