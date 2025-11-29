<?php
include('config.php');
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request'];

try {

    
    if ($_POST['action'] === 'insert') {
        $stmt = $conn->prepare("INSERT INTO student (name, email, address, phone, gender, level) VALUES (:name, :email, :address, :phone, :gender, :level)");
        $stmt->execute([
            ':name' => $_POST['name'],
            ':email' => $_POST['email'],
            ':address' => $_POST['address'],
            ':phone' => $_POST['phone'],
            ':gender' => $_POST['gender'],
            ':level' => $_POST['level']
        ]);

        $id = $conn->lastInsertId();
        $stmt2 = $conn->prepare("SELECT * FROM student WHERE id=:id");
        $stmt2->execute([':id' => $id]);
        $record = $stmt2->fetch(PDO::FETCH_ASSOC);

        $response = ['status' => 'success', 'message' => 'Inserted', 'data' => $record];
    }

    
    elseif ($_POST['action'] === 'update') {
        $stmt = $conn->prepare("UPDATE student SET name=:name, email=:email, address=:address, phone=:phone, gender=:gender, level=:level WHERE id=:id");
        $stmt->execute([
            ':name' => $_POST['name'],
            ':email' => $_POST['email'],
            ':address' => $_POST['address'],
            ':phone' => $_POST['phone'],
            ':gender' => $_POST['gender'],
            ':level' => $_POST['level'],
            ':id' => $_POST['id']
        ]);
        $response = ['status' => 'success', 'message' => 'Updated'];
    }

    
   if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];

    
    $stmt = $conn->prepare("SELECT * FROM student WHERE id=:id");
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    $sql = "UPDATE student SET is_deleted = 1 WHERE id=:id";
        $stmt2 = $conn->prepare($sql);
        $stmt2->execute([':id' => $id]);

        $response = [
            'status' => 'success',
            'message' => 'Record deleted successfully',
            'data' => [
                'id' => $record['id'],
                'name' => $record['name'],
                'email' => $record['email'],
                'address' => $record['address'],
                'phone' => $record['phone'],
                'gender' => $record['gender'],
                'level' => $record['level'],
            ]
        ];

}

    
if (isset($_POST['action']) && $_POST['action'] === 'restore') {
    $id = $_POST['id'];

    
    $stmt = $conn->prepare("SELECT * FROM student WHERE id=:id");
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "UPDATE student SET is_deleted = 0 WHERE id=:id";
        $stmt2 = $conn->prepare($sql);
        $stmt2->execute([':id' => $id]);

        $response = [
            'status' => 'success',
            'message' => 'Record restored successfully',
            'data' => [
                'id' => $record['id'],
                'name' => $record['name'],
                'email' => $record['email'],
                'address' => $record['address'],
                'phone' => $record['phone'],
                'gender' => $record['gender'],
                'level' => $record['level'],
            ]
        ];

}

} catch (PDOException $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
