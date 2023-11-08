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

    // Get stock from the Stocks table based on the restaurant
    $query = "SELECT * FROM Stocks WHERE restaurant = ?";
    if ($stmt = mysqli_prepare($db, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $restaurant);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Fetch all rows and store them in an array
        $inventory_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $inventory_list[] = $row;
        }
    } else {
        echo "ERROR: Could not prepare query " . $query . "" . mysqli_error($db);
    }


    
    // Delete stock item
    if(isset($_POST['mode'])&& $_POST['mode']==="delete"){
        if (isset($_POST['delete_id'])) {
            $delete_id = $_POST['delete_id'];
            $delete_query = "DELETE FROM Stocks WHERE id = ?";
            if ($stmt = mysqli_prepare($db, $delete_query)) {
                mysqli_stmt_bind_param($stmt, "i", $delete_id);
                mysqli_stmt_execute($stmt);
                header("Location: inventory.php");
            } else {
                echo "ERROR: Could not prepare delete query" . mysqli_error($db);
            }
        }
    }

    //Change mode of view
    if(isset($_POST['mode'])&& $_POST['mode']==="back"){
        $mode = "create";
    }

    //Create new stock
    if (isset($_POST['mode']) && $_POST['mode'] === "create") {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];


        // Insert new stock into the Stocks table
        $insert_query = "INSERT INTO Stocks (restaurant, name, quantity) VALUES ( ?, ?, ?)";
        if ($stmt = mysqli_prepare($db, $insert_query)) {
            mysqli_stmt_bind_param($stmt, "sss", $restaurant, $name, $quantity);
            mysqli_stmt_execute($stmt);
            header("Location: inventory.php");
        } else {
            echo "ERROR: Could not prepare insert query" . mysqli_error($db);
        }
    }

    //Update selected stock
    if (isset($_POST['mode']) && $_POST['mode'] === "update") {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];
        $update_id = $_POST['update_id'];   
    
        // Update the selected stock in the Stocks table
        $update_query = "UPDATE Stocks SET name = ?, quantity = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($db, $update_query)) {
            mysqli_stmt_bind_param($stmt, "sii", $name, $quantity, $update_id);
            mysqli_stmt_execute($stmt);
            header("Location: inventory.php");
        } else {
            echo "ERROR: Could not prepare update query" . mysqli_error($db);
        }
        $mode="create";
    }

    //Select stock to update
    if(isset($_POST['mode']) && $_POST['mode']==="select") {
        $update_id = $_POST['update_id'];

        // Find the stock with the matching ID from the inventory list
        $selected = null;
        foreach ($inventory_list as $stock) {
            if ($stock['id'] == $update_id) {
                $selected = $stock;
                break;
            }
        }
        $mode = "update";
    }
    
    
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
    <a href="/purchase.php">Purchase ingredient</a>
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
