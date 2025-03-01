<?php

try{
    //include databse connection
    include "../config/dbConfig.php";

    $id = isset($_GET['id']) ? $_GET['id'] : die("ERROR: ID not found.");
    
    $query = "DELETE FROM borrowings WHERE borrow_id=?";
    $stmt = $conn->prepare($query);

    $stmt -> bindParam(1, $id);

    if($stmt -> execute()){
        header("Location: ../index.php");
    }
}
catch(PDOException $e){
    echo "Error".$e->getMessage();
}

?>