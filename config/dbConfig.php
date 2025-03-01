<?php
    $host = "localhost";
    $dbName = "itemborrowingdb";
    $userName = "root";
    $password = "";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbName", $userName, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected to $dbName at $host successfully.";
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>