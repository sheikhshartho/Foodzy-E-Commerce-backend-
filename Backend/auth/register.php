<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$conn = mysqli_connect("localhost", "root", "", "user");

if (!$conn) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

/* ---------- GET (Read) ---------- */
if ($method === "GET") {
    $sql = "SELECT * FROM users";
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

    $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "User added"]);
    }
}

/* ---------- DELETE ---------- */
if ($method === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $sql = "DELETE FROM users WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "User deleted"]);
    }
}