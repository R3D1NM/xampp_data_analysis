<?php
    session_start();

    // Database connection
    $db = mysqli_connect("localhost:3306", "team09", "team09", "team09");
    if (mysqli_connect_errno()) {
        printf("Database connection failed: %s", mysqli_connect_error());
        exit();
    }

    // Get restaurant id from user login
    $restaurant = $_SESSION['restaurant_id'];
    $mode = "create";

    // Get stock from the Stocks table based on the restaurant
    // $query = "SELECT * FROM Stocks WHERE restaurant = ?";
    // if ($stmt = mysqli_prepare($db, $query)) {
    //     mysqli_stmt_bind_param($stmt, "s", $restaurant);
    //     mysqli_stmt_execute($stmt);
    //     $result = mysqli_stmt_get_result($stmt);
    // } else {
    //     echo "ERROR: Could not prepare query " . $query . "" . mysqli_error($db);
    // }

    $inventory_list = [
        ['id' => 1, 'name' => 'Carrot', 'quantity' => 1200],
        ['id' => 2, 'name' => 'Milk', 'quantity' => 800],
        ['id' => 3, 'name' => 'Flour', 'quantity' => 1000]
    ];
    

    
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
</head>
<body>
    <h1>Inventory Management</h1>
    <a href="/main.php">Back to Main</a>
    <h2>Inventory List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Quantity</th>
        </tr>
        <?php
        // while ($row = mysqli_fetch_assoc($result)) {
            
        foreach ($inventory_list as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
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
    <h2><?php echo $mode==="update" ? 'Update Stock' : 'Create New Stock' ?></h2>
    <form method="post" action="">
        <input type="hidden" name="mode" value="<?php echo $mode ?>">
        <input type="hidden" name="update_id" value="<?php echo $mode==="update" ? $selected['id'] : '' ?>">
        <p>Name <input type="text" name="name" value="<?php echo $mode==="update" ? $selected['name'] : '' ?>" required></p>
        <p>Quantity <input type="number" name="quantity" value="<?php echo $mode==="update" ? $selected['quantity'] : '' ?>" required></p>
        <button type="submit"><?php echo $mode==="update" ? 'Update' : 'Create' ?></button>
    </form>
    <?php if($mode=="update") { ?>
        <form method="post" action="">
            <input type="hidden" name="mode" value="back">
            <button type="submit">Back</button>
        </form>
    <?php } ?>
</body>
</html>
