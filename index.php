<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case "GET":
        $sql = "SELECT * FROM universitarios";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[5]) && is_numeric($path[5])){
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[5]);
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($students);
        break;
    case "POST":
        $student = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO universitarios(id, nombre, correo, universidad) VALUES(null, :nombre, :correo, :universidad)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $student->nombre);
        $stmt->bindParam(':correo', $student->correo);
        $stmt->bindParam(':universidad', $student->universidad);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record created succesfully!'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to create record!'];
        }
        break;
    case "PUT":
        $student = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE universitarios SET nombre =:nombre, correo =:correo, universidad =:universidad WHERE id =:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $student->id);
        $stmt->bindParam(':nombre', $student->nombre);
        $stmt->bindParam(':correo', $student->correo);
        $stmt->bindParam(':universidad', $student->universidad);
    
        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record updated succesfully!'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to update record!'];
        }
        break;
    case "DELETE":
        $sql = "DELETE FROM universitarios WHERE id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        print_r($path);

        $stmt = $conn->prepare($sql);
        // In this case, the id is in the 4th position of the array.
        $stmt->bindParam(':id', $path[4]);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record deleted succesfully!'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to delete record!'];
        }
        break;
}