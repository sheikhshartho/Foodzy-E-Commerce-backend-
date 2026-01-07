<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
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

/* ---------- DELETE ---------- */
if ($method === "DELETE") {
  
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'] ?? null;

    if ($id) {
        $sql = "DELETE FROM `accounts` WHERE `accounts`.`id` = $id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "Account deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete account"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID is required to delete account"]);
    }
}


/* ---------- PUT (Update) ---------- */
if ($method === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID is required"]);
        exit;
    }

    $fields = [];

    if (!empty($data['name'])) {
        $name = mysqli_real_escape_string($conn, $data['name']);
        $fields[] = "`name` = '$name'";
    }

    if (!empty($data['email'])) {
        $email = mysqli_real_escape_string($conn, $data['email']);
        $fields[] = "`email` = '$email'";
    }

    if (!empty($data['role'])) {
        $role = mysqli_real_escape_string($conn, $data['role']);
        $fields[] = "`role_ENUM` = '$role'";
    }

    if (count($fields) === 0) {
        echo json_encode(["success" => false, "message" => "No data provided to update"]);
        exit;
    }

    $sql = "UPDATE `accounts` SET " . implode(", ", $fields) . " WHERE `id` = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "User updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed"]);
    }
}

