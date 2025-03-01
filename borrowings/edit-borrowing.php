<?php
 $msg = "";
 $errors = [];

include "../config/dbconfig.php";



    $borrow_id = $_GET['id'];
    $query = "SELECT
    users.user_id as user_id,
    users.full_name as full_name,
    borrowings.borrow_id as borrow_id,
    borrowings.borrow_date as borrow_date,
    borrowings.due_date as due_date,
    borrowings.status as `status`,
    items.item_id as item_id,
    `items`.item_name as item_name
    FROM users
    INNER JOIN borrowings ON users.user_id = borrowings.user_id 
    INNER JOIN items ON borrowings.item_id = items.item_id
    WHERE borrow_id = ?;
    "; 	
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $borrow_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // echo "<script>console.log('herehere: ', {$row})</script>";

        function sanitizeInput($data){
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            $data = trim($data);
        
            return $data;
        }
        
        // Validation function using regex
        function validateInput($data, $patterns){
            return preg_match($patterns, $data);
        }
        
        // Define patterns
        $patterns = [
            "user_id" => "/^[0-9]{1,6}$/", // only integers, max 6 digits
            "item_id" =>"/^[0-9]{1,6}$/", // only integers, max 6 digits
            "borrow_date" => "/^[0-9\s:-]{1,25}$/", // only numbers, spaces, ':' and '-'
            "due_date" => "/^[0-9\s:-]{1,25}$/", // only numbers, spaces, ':' and '-'
            "status" => "/^(Borrowed|Overdue|Returned)$/", // Options: Borrowed, Overdue, or Returned
        ];

        
        if($_SERVER["REQUEST_METHOD"]=="POST"){
            echo "POST";
            
            $user_id = sanitizeInput($_POST['user_id']);
            $item_id = sanitizeInput($_POST['item_id']);
            $borrow_date = sanitizeInput($_POST['borrow_date']);
            $due_date = sanitizeInput($_POST['due_date']);
            $status = sanitizeInput($_POST['status']);
            
            // Input validation
            
            
            if(!validateInput($user_id, $patterns['user_id'])){
                $errors['user_id'] = "Invalid User ID (only integers).";
            }
            
            if(!validateInput($item_id, $patterns['item_id'])){
                $errors['item_id'] = "Invalid Item ID (only integers).";
            }
            
            if(!validateInput($borrow_date, $patterns['borrow_date'])){
                $errors['borrow_date'] = "Invalid date (only numbers, spaces, ':' and '-').";
            }
            
            if(!validateInput($due_date, $patterns['due_date'])){
                $errors['due_date'] = "Invalid due date (only numbers, spaces, ':' and '-').";
            }
            
            
            if(!validateInput($status, $patterns['status'])){
                $errors['status'] = "Invalid status (Options: Borrowed, Overdue, or Returned).";
            }
            
            // Proceed if no validation errors
            if(empty($errors)){
                
                try{
                    //include databse connection
                    include "../config/dbConfig.php";
                    
                    // insert query
                    $query = 'UPDATE borrowings 
                        SET user_id = ?, item_id = ?, borrow_date = ?, due_date = ?, `status` = ?
                        WHERE borrow_id = ?';
            
            $stmt = $conn -> prepare($query);
            
            // bin the parameters
            $stmt -> bindParam(1, $user_id);
            $stmt -> bindParam(2, $item_id);
            $stmt -> bindParam(3, $borrow_date);
            $stmt -> bindParam(4, $due_date);
            $stmt -> bindParam(5, $status);
            $stmt -> bindParam(6, $borrow_id);
            
            // execute the query
            if($stmt->execute()){
                header("Location: index.php");
                exit(); 
            }
            else{
                $msg = "<div class='alert alert-danger'><strong>Unable to save record</strong></div>";
            }
            
            
        }
        catch(Exception $e){
            echo "ERROR: ".$e->getMessage();
        }
    }
    else
    {
        echo "<p class='text-danger'>Invalid request.</p>";
    }
    }
}
else
{
    echo "<p class='text-danger'>Borrowing not found.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Create New Rental</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5 mb-5 d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group mt-2">
                        <label for=user_id>User ID:</label>
                        <input type="text" class="form-control" name="user_id" value="<?php echo htmlspecialchars(($row['user_id'])); ?>"><br>
                        <span class="text-danger"><?php echo $errors['user_id'] ?? ''; ?></span>
                    </div>

                    <div class="form-group mt-2">
                        <label for=item_id>Item ID:</label>
                        <input type="text" class="form-control" name="item_id" value="<?php echo htmlspecialchars(($row['item_id'])); ?>"><br>
                        <span class="text-danger"><?php echo $errors['item_id'] ?? ''; ?></span>
                    </div>

                    <div class="form-group mt-2">
                        <label for=borrow_date>Borrow Date:</label>
                        <input type="text" class="form-control" name="borrow_date" value="<?php echo htmlspecialchars(($row['borrow_date'])); ?>"><br>
                        <span class="text-danger"><?php echo $errors['borrow_date'] ?? ''; ?></span>
                    </div>

                    <div class="form-group mt-2">
                        <label for=due_date>Due Date:</label>
                        <input type="text" class="form-control" name="due_date" value="<?php echo htmlspecialchars(($row['due_date'])); ?>"><br>
                        <span class="text-danger"><?php echo $errors['due_date'] ?? ''; ?></span>
                    </div> 

                    <div class="form-group mt-2">
                        <label for=status>Status:</label>
                        <input type="text" class="form-control" name="status" value="<?php echo htmlspecialchars(($row['status'])); ?>"><br> 
                        <span class="text-danger"><?php echo $errors['status'] ?? ''; ?></span>        
                    </div>

                    <div class="form-group mt-2 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Edit</button>
                        <a href="./index.php" class="btn btn-danger ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>  
</body>
</html>