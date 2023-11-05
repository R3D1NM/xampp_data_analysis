<?php
    session_start();
    //If not logined yet
    if(!isset($_SESSION['userid'])) {
        header("Location: /login.php");
        exit();
    }
    if(!isset($_SESSION['restaurant_id'])){
        $db=mysqli_connect("localhost:3306","team09","team09","team09");
        if(mysqli_connect_errno()){
            printf ("database connection failed: %s",mysqli_connect_error());
            exit();
        }
        $query = "SELECT id FROM restaurants WHERE userid = ?";
        if($stmt=mysqli_prepare($db,$query)){
            mysqli_stmt_bind_param($stmt,"s",$id);
            $id = $_SESSION['userid'];
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if($result){
                if(mysqli_num_rows($result)>0){
                    //get restaurant id
                    $row = mysqli_fetch_assoc($result);
                    $_SESSION['restaurant_id']=$row['id'];                    
                }else{
                    header("Location: /register.php");
                    exit();
                }
            } else {
                echo "Error : " . mysqli_error($db);
            }
        }else{
            echo "ERROR: Could not prepare query".$query."".mysqli_error($db);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        session_destroy();
        header("Location: /login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
</head>
<body>
    <h1>Restraunt Management Service</h1>
    <form method='POST' action=''>
        <button type="submit">logout</button>
    </form>
    <h3>
    <?php
        echo "hello, ".$_SESSION['username'];
    ?>
    </h3>
    <ul>
        <li><a href="/menu.php">Manage Menu</a></li>
        <li><a href="/inventory.php">Manage Inventory</a></li>
        <li><a href="/purchase.php">Manage Purchase</a></li>
        <li><a href="/order.php">Manage Order</a></li>
        <li><a href="/reservation.php">Manage Reservation</a></li>
        <li><a href="/statistic.php">Daily Statistic</a></li>
        <li><a href="/employee.php">Manage Employee</a></li>

    </ul>
    
</body>
</html>