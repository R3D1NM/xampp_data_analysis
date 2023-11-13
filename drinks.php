<?php
    session_start();

    // Database connection
    $db = mysqli_connect("localhost:3306", "team09", "team09", "team09");
    if (mysqli_connect_errno()) {
        printf("Database connection failed: %s", mysqli_connect_error());
        exit();
    }

    // Get restaurant id from user login
    $restaurant = $_SESSION['restaurant'];
    
    //POST Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_POST['mode'])) echo 'Wrong Request';
        switch ($_POST['mode']){
            case 'delete':
                if (isset($_POST['delete_id'])) {
                    $delete_id = $_POST['delete_id'];
                    $delete_query = "DELETE FROM Drinks WHERE id = ?";
                    if ($stmt = mysqli_prepare($db, $delete_query)) {
                        mysqli_stmt_bind_param($stmt, "i", $delete_id);
                        mysqli_stmt_execute($stmt);
                        header("Location: menu.php");
                    } else {
                        echo "ERROR: Could not prepare delete query" . mysqli_error($db);
                    }
                }
                break;
            case 'create':
                $name = $_POST['name'];
                $price = $_POST['price'];
                $milliliters = $_POST['milliliters'];
                $alcoholic = $_POST['alcoholic'];

                // Insert new menu into the Drinks table
                $insert_query = "INSERT INTO Drinks (restaurant, name, price, milliliters, alcoholic) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($db, $insert_query)) {
                    mysqli_stmt_bind_param($stmt, "issis", $restaurant, $name, $price, $milliliters, $alcoholic);
                    mysqli_stmt_execute($stmt);
                    header("Location: menu.php");
                } else {
                    echo "ERROR: Could not prepare insert query" . mysqli_error($db);
                }
                break;
            case 'update':
                $name = $_POST['name'];
                $price = $_POST['price'];
                $milliliters = $_POST['milliliters'];
                $alcoholic = $_POST['alcoholic'];
                $update_id = $_POST['update_id'];

                // Update the selected menu in the Drinks table
                $update_query = "UPDATE Drinks SET name = ?, price = ?, milliliters = ?, alcoholic = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($db, $update_query)) {
                    mysqli_stmt_bind_param($stmt, "siisi", $name, $price, $milliliters, $alcoholic, $update_id);
                    mysqli_stmt_execute($stmt);
                    header("Location: menu.php?type='drinks'");
                } else {
                    echo "ERROR: Could not prepare update query" . mysqli_error($db);
                }
                break;
            
            default :
                echo 'Wrong Request';
        }
    }    
    
?>