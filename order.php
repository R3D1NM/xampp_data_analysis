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

    //Get orders from the Orders table based on the restaurant
    $query = "SELECT * FROM ORDERS WHERE restaurant = ?";
    if ($stmt = mysqli_prepare($db, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $restaurant);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Fetch all rows and store them in an array
        $orders_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders_list[] = $row;
        }
    } else {
        echo "ERROR: Could not prepare query " . $query . "" . mysqli_error($db);
    }
    
    //POST Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_POST['mode'])) echo 'Wrong Request';
        switch ($_POST['mode']){
            case 'delete':
                if (isset($_POST['delete_id'])) {
                    $delete_id = $_POST['delete_id'];
                    $delete_query = "DELETE FROM ORDERS WHERE id = ?";
                    if ($stmt = mysqli_prepare($db, $delete_query)) {
                        mysqli_stmt_bind_param($stmt, "i", $delete_id);
                        mysqli_stmt_execute($stmt);
                        header("Location: order.php");
                    } else {
                        echo "ERROR: Could not prepare delete query" . mysqli_error($db);
                    }
                }
                break;
            case 'back':
                $mode = "create";
                break;
            case 'create':
                $menu = $_POST['menu'];
                $r_table = $_POST['r_table'];
                $order_time = $_POST['order_time'];
                $payment = $_POST['payment'];
                $price = $_POST['price'];

                // Insert new order into the Orders table
                $insert_query = "INSERT INTO ORDERS (restaurant, menu, r_table, order_time, payment, price) VALUES (?, ?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($db, $insert_query)) {
                    mysqli_stmt_bind_param($stmt, "sssssi", $restaurant, $menu, $r_table, $order_time, $payment, $price);
                    mysqli_stmt_execute($stmt);
                    header("Location: order.php");
                } else {
                    echo "ERROR: Could not prepare insert query" . mysqli_error($db);
                }
                break;
            case 'update':
                if (isset($_POST['update_id'])) {
                    $update_id = $_POST['update_id'];
                    $menu = $_POST['menu'];
                    $r_table = $_POST['r_table'];
                    $order_time = $_POST['order_time'];
                    $payment = $_POST['payment'];
                    $price = $_POST['price'];
    
                    // Update the selected order in the Orders table
                    $update_query = "UPDATE ORDERS SET restaurant = ?, menu = ?, r_table = ?, order_time = NOW(), payment = ?, price = ? WHERE id = ?";
                    if ($stmt = mysqli_prepare($db, $update_query)) {
                        mysqli_stmt_bind_param($stmt, "ssssii", $restaurant, $menu, $r_table, $payment, $price, $update_id);
                        mysqli_stmt_execute($stmt);
                        header("Location: order.php");
                    } else {
                        echo "ERROR: Could not prepare update query" . mysqli_error($db);
                    }
    
                }
                //switch to create form
                $mode="create";
                break;
            case 'select':
                $update_id = $_POST['update_id'];

                // Find the order with the matching ID from the order list
                $selected = null;
                foreach ($orders_list as $order) {
                    if ($order['id'] == $update_id) {
                        $selected = $order;
                        break;
                    }
                }
                //switch to update form
                $mode = "update";
                break;
            
            default :
                echo 'Wrong Request';
        }
    }    
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="main.css" media="all" rel="Stylesheet" type="text/css" />
    <title>Order</title>
</head>
<body>
    <h1>Order Management</h1>
    <a href="/main.php">Back to Main</a>
    <div class="flex">    
        <div class="list">
            <h2>Order List</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Menu</th>
                    <th>R_Table</th>
                    <th>Order_Time</th>
                    <th>Payment</th>
                    <th>Price</th>
                </tr>
                <?php
                foreach ($orders_list as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['menu'] . "</td>";
                    echo "<td>" . $row['r_table'] . "</td>";
                    echo "<td>" . $row['order_time'] . "</td>";
                    echo "<td>" . $row['payment'] . "</td>";
                    echo "<td>" . $row['price'] . "</td>";
                    echo "<td>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='mode' value='delete'>";
                    echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit'>Delete</button>";
                    echo "</form>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='update_id' value='" . $row['id'] . "'>";
                    echo "<input type='hidden' name='mode' value='select'>";
                    echo "<button type='submit'>Update</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>    
        <div class="panel">
            <h2><?php echo $mode==="update" ? 'Update Order' : 'Create New Order' ?></h2>
            <form method="post" action="">
            <input type="hidden" name="mode" value="<?php echo $mode ?>">
            <input type="hidden" name="update_id" value="<?php echo $mode==="update" ? $selected['id'] : '' ?>">
            <p>Menu <input type="text" name="menu" value="<?php echo $mode==="update" ? $selected['menu'] : '' ?>" required></p>
            <p>R_Table <input type="text" name="r_table" value="<?php echo $mode==="update" ? $selected['r_table'] : '' ?>" required></p>
            <p>Payment 
                <select name="payment" required>
                    <option value="Cash" <?php echo ($mode==="update" && $selected['payment'] === 'Cash') ? 'selected' : '' ?>>Cash</option>
                    <option value="Card" <?php echo ($mode==="update" && $selected['payment'] === 'Card') ? 'selected' : '' ?>>Card</option>
                </select>
            </p>
            <p>Price <input type="number" name="price" value="<?php echo $mode==="update" ? $selected['price'] : '' ?>" required></p>
            <button type="submit"><?php echo $mode==="update" ? 'Update' : 'Create' ?></button>
        </form>
        <?php if($mode=="update") { ?>
            <form method="post" action="">
                <input type="hidden" name="mode" value="back">
                <button type="submit">Back</button>
            </form>
        <?php } ?>
        </div>
    </div>
</body>
</html>
