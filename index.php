<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Borrow Dashboard</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
        <div class="container mt-5">
            <h2>Rentals</h2>
            <a href="./borrowings/create.php" class="btn btn-success float-end" >Add Rental</a>
            <table class="table table-sm table-striped table-hover">
                <thead>
                <tr>
                    <th>User Name</th>
                    <th>Borrowed Item</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
                </thead>
                <?php 
                    //include database connection
                    include "config/dbConfig.php";
                   
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
                    "; 



                    //prepare query statement
                    $stmt = $conn->prepare($query);

                    //execute query
                    $stmt->execute();
             
                ?>
                <tbody>
                    <?php
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            extract($row);
                        

                        echo"<tr>";
                            echo "<td>{$full_name}</td>";
                            echo "<td>{$item_name}</td>";
                            echo "<td>{$borrow_date}</td>";
                            echo "<td>{$due_date}</td>";
                            echo "<td>{$status}</td>";
                            echo "<td>";
                                echo '<button type="button" class="btn btn-primary btn-sm">Info</button>'; 
                                echo "<button class='btn btn-warning btn-sm' onclick='editBorrow({$borrow_id})' data-bs-toggle='modal' data-bs-target='#editBorrowModal'>Edit</button>";
                                echo "<a href='#' onclick='delete_borrowing({$borrow_id});' class='btn btn-danger btn-sm'>Del</a>";
                        echo '</td>';     
                       echo '</tr>';
                        }
                    ?>

                    <!-- Read Borrowing Modal -->
                    <div class="modal fade" id="editBorrowModal" tabindex="-1">
                    <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Edit Rental</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="borrowingDetails">
                            Loading....
                    </div>
                    </div>
                    </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

                    <script>
                            function delete_borrowing(id){
                                let answer = confirm ("are you sure?")
                                if(answer){
                                    window.location="./borrowings/delete.php?id="+id;
                                }
                            }
                    </script>
                </tbody>
            </table>
        </div>
 
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/Fetch-Borrowing-Details.js"></script>
    </body>
</html>