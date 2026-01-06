<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$conn = mysqli_connect("localhost", "root", "", "ecommerce");

if (!$conn) {
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];

$sql = "SELECT * FROM accounts WHERE email = '$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 1) {

    $user = mysqli_fetch_assoc($result);

    
    if ($password === $user['password']) {

        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email'],
                "role" => $user['role_ENUM'],
                'created'=> $user['created_at'],

            ]
        ]);

    } else {
        echo json_encode([
            "success" => false,
            "message" => "Wrong password"
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Email not found"
    ]);
}
