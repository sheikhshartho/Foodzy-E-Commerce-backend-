<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$conn = mysqli_connect("localhost", "root", "", "ecommerce");

if (!$conn) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

/* ---------- GET (Read) ---------- */
if ($method === "GET") {
    $sql = "SELECT * FROM `accounts`";
    $result = mysqli_query($conn, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data);
}


/* ---------- POST  ---------- */
if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $name  = $data['name'];
    $email = $data['email'];
    $password = $data['password'];
    // $email = $data['email'];

    

    $sql = "INSERT INTO `accounts` (name, email, password, `role_ENUM`, `created_at`) VALUES ( '$name' , '$email' , '$password', 'customer' ,NOW() )";


    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Your account is created successfully "]);
    }else{
        echo json_encode(["success"=> false, "message"=> "Account creating faild"]);
    }
}
