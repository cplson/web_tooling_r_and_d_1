<?php
$msg = "";
$errors = [];

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
    // "full_name" => "/^[a-zA-Z\s]{1,100}$/", // only letters and spaces
    // "item_name" => "/^[a-zA-Z0-9\s]{1,100}$/", // only alphanumeric and spaces
    "user_id" => "/^[0-9]{1,6}$/", // only integers, max 6 digits
    "item_id" =>"/^[0-9]{1,6}$/", // only integers, max 6 digits
    "borrow_date" => "/^[0-9\s:-]{1,25}$/", // only numbers, spaces, ':' and '-'
    "due_date" => "/^[0-9\s:-]{1,25}$/", // only numbers, spaces, ':' and '-'
    "status" => "/^(Borrowed|Overdue|Returned)$/", // Options: Borrowed, Overdue, or Returned
];



if($_SERVER["REQUEST_METHOD"]=="POST"){
    // echo "POST";
    // $full_name = sanitizeInput($_POST['full_name']);
    // $item_name = sanitizeInput($_POST['usage_location']);
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
            $query = 'INSERT INTO borrowings SET user_id = ?, item_id = ?, borrow_date = ?, due_date = ?, `status` = ?';
            
            $stmt = $conn -> prepare($query);
            
            // bin the parameters
            $stmt -> bindParam(1, $user_id);
            $stmt -> bindParam(2, $item_id);
            $stmt -> bindParam(3, $borrow_date);
            $stmt -> bindParam(4, $due_date);
            $stmt -> bindParam(5, $status);
            
            // execute the query
            if($stmt->execute()){
                $msg = "<div class='alert alert-success'><strong>Record was saved</strong></div>";
            }
            else{
                $msg = "<div class='alert alert-danger'><strong>Unable to save record</strong></div>";
            }
            
            
        }
        catch(Exception $e){
            echo "ERROR: ".$e->getMessage();
        }
    }
        
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
                <?php echo $msg; ?>
                <form action="#" method="POST">
                    <div class="form-group mt-2">
                        <label for=user_id>User ID:</label>
                        <input type="text" class="form-control" name="user_id"><br>
                        <span class="text-danger"><?php echo $errors['user_id'] ?? ''; ?></span>
                    </div>

                    <div class="form-group mt-2">
                        <label for=item_id>Item ID:</label>
                        <input type="text" class="form-control" name="item_id"><br>
                        <span class="text-danger"><?php echo $errors['item_id'] ?? ''; ?></span>
                    </div>

                    <div class="form-group mt-2">
                        <label for=borrow_date>Borrow Date:</label>
                        <input type="text" class="form-control" name="borrow_date"><br>
                        <span class="text-danger"><?php echo $errors['borrow_date'] ?? ''; ?></span>
                    </div>

                    <div class="form-group mt-2">
                        <label for=due_date>Due Date:</label>
                        <input type="text" class="form-control" name="due_date"><br>
                        <span class="text-danger"><?php echo $errors['due_date'] ?? ''; ?></span>
                    </div> 

                    <!-- <div class="form-group mt-2">
                        <label for=usage_location>Usage Location:</label>
                        <input type="text" class="form-control" name="usage_location"><br>
                        <span class="text-danger"><?php echo $errors['usage_location'] ?? ''; ?></span>
                    </div> -->

                    <div class="form-group mt-2">
                        <label for=status>Status:</label>
                        <input type="text" class="form-control" name="status"><br> 
                        <span class="text-danger"><?php echo $errors['status'] ?? ''; ?></span>        
                    </div>

                    <div class="form-group mt-2 d-flex justify-content-center">
                        <button class="btn btn-primary">Add</button>
                        <a href="../index.php" class="btn btn-danger ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>  
</body>
</html>