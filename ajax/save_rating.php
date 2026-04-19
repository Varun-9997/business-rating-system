<?php
require __DIR__ . '/../conn.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$business_id = $_POST['business_id'];
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$rating = trim($_POST['rating']);

if (empty($name)) {
exit(json_encode(["status"=>"error","message"=>"Name required"]));
}

if (empty($email) && empty($phone)) {
exit(json_encode(["status"=>"error","message"=>"Email or Phone required"]));
}

if ($rating < 0 || $rating > 5) {
exit(json_encode(["status"=>"error","message"=>"Invalid rating"]));
}

try {

$sql = "SELECT id FROM ratings WHERE business_id = ?";
$params = [$business_id];

if (!empty($email) && !empty($phone)) {
$sql .= " AND (email = ? OR phone = ?)";
$params[] = $email;
$params[] = $phone;
}
elseif (!empty($email)) {
$sql .= " AND email = ?";
$params[] = $email;
}
else {
$sql .= " AND phone = ?";
$params[] = $phone;
}

$check = $conn->prepare($sql);
$check->execute($params);

if ($check->rowCount() > 0) {

$row = $check->fetch(PDO::FETCH_ASSOC);

$update = $conn->prepare("
UPDATE ratings
SET name=?, email=?, phone=?, rating=?
WHERE id=?
");

$update->execute([
$name,
$email,
$phone,
$rating,
$row['id']
]);

} else {

$insert = $conn->prepare("
INSERT INTO ratings
(business_id,name,email,phone,rating)
VALUES(?,?,?,?,?)
");

$insert->execute([
$business_id,
$name,
$email,
$phone,
$rating
]);

}

echo json_encode([
"status"=>"success",
"message"=>"Rating saved successfully"
]);

} catch (Exception $e) {

echo json_encode([
"status"=>"error",
"message"=>$e->getMessage()
]);

}
}