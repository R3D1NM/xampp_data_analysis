<?php
    session_start();
    //If not logined yet
    if(!isset($_SESSION['userid'])) {
        header("Location: /login.html");
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