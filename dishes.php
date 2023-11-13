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
    $mode = "create";
    
    //POST Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_POST['mode'])) echo 'Wrong Request';
        switch ($_POST['mode']){
            case 'delete': //delete selected menu
                if (isset($_POST['delete_id'])) {
                    $delete_id = $_POST['delete_id'];
                    $delete_query = "DELETE FROM Dishes WHERE id = ?";
                    if ($stmt = mysqli_prepare($db, $delete_query)) {
                        mysqli_stmt_bind_param($stmt, "i", $delete_id);
                        mysqli_stmt_execute($stmt);
                        header("Location: menu.php");
                    } else {
                        echo "ERROR: Could not prepare delete query" . mysqli_error($db);
                    }
                }
                break;
            case 'create': //create new dish menu
                $name = $_POST['name'];
                $price = $_POST['price'];
                $veganism = $_POST['veganism'];

                //insert new menu into the Dishes table
                $insert_query = "INSERT INTO Dishes (restaurant, name, price, veganism) VALUES (?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($db, $insert_query)) {
                    mysqli_stmt_bind_param($stmt, "isis", $restaurant, $name, $price, $veganism);
                    mysqli_stmt_execute($stmt);
                    header("Location: menu.php");
                } else {
                    echo "ERROR: Could not prepare insert query" . mysqli_error($db);
                }
                break;
            case 'update': //update selected menu
                $name = $_POST['name'];
                $price = $_POST['price'];
                $veganism = $_POST['veganism'];
                $update_id = $_POST['update_id'];

                //update the selected menu in the Dishes table
                $update_query = "UPDATE Dishes SET name = ?, price = ?, veganism = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($db, $update_query)) {
                    mysqli_stmt_bind_param($stmt, "sisi", $name, $price, $veganism, $update_id);
                    mysqli_stmt_execute($stmt);
                    header("Location: menu.php");
                } else {
                    echo "ERROR: Could not prepare update query" . mysqli_error($db);
                }
                break;
            
            default :
                echo 'Wrong Request';
        }
    }    
    
?>