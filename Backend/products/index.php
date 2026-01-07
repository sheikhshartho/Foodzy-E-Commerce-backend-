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
    $sql = "SELECT * FROM `products`";
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

    $title  = $data['title'];
    $description = $data['description'];
    $category = $data['category'];
    $price = $data['price'];
    $discount = $data['discount'];
    $rating = $data['rating'];
    $stock = $data['stock'];
    $brand = $data['brand'];
    $thumbnail = $data['thumbnail'];
    $warranty_information = $data['warranty'];
    $shipping_information = $data['shipping'];
    $availability_status = $data['availability'];
    $return_policy = $data['returnPolicy'];
    



    $sql = "INSERT INTO `products` (`title`, `description`, `category`, `price`, `discount`, `rating`, `stock`, `brand`, `thumbnail`, `warranty_information`, `shipping_information`, `availability_status`, `return_policy`) 
    VALUES ('$title', '$description', '$category', '$price', '$discount',  '$rating', '$stock', '$brand', '$thumbnail', '$warranty_information', '$shipping_information', '$availability_status', '$return_policy')";



    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Your account is created successfully "]);
    }else{
        echo json_encode(["success"=> false, "message"=> "Account creating faild"]);
    }
}
