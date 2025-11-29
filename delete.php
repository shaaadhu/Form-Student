<?php include('config.php');?>
<?php

if (isset($_GET['id'])) {
    
    $id_to_delete =$_GET['id'];
    $sql_delete = "DELETE FROM student WHERE id = :id";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bindParam(':id', $id_to_delete, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: index.php?delte-msg=deleted data...");
    exit();
}


?>