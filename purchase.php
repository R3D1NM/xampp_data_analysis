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

    // Get ingredient from the Ingredients table
    // $query = "SELECT * FROM Ingredients";
    // if ($stmt = mysqli_prepare($db, $query)) {
    //     mysqli_stmt_execute($stmt);
    //     $result = mysqli_stmt_get_result($stmt);
    // } else {
    //     echo "ERROR: Could not prepare query " . $query . "" . mysqli_error($db);
    // }

    $ingredient_list = [
        ['id' => 1, 'supplier' => 'AAA', 'name' => 'Carrot', 'price' => 1200],
        ['id' => 2, 'supplier' => 'BBB', 'name' => 'Milk', 'price' => 800],
        ['id' => 3, 'supplier' => 'CCC', 'name' => 'Flour', 'price' => 1000]
    ];
    

    //Create new purchase
    if (isset($_POST['mode']) && $_POST['mode'] === "purchase") {
        $ingredient_id = $_POST['ingredient_id'];
        $stock_id = $_POST['stock_id'];
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];

        // Add new stock to the inventory list
        $new_id = count($inventory_list) + 1;
        $new_stock = ['id' => $new_id, 'name' => $name, 'quantity' => $quantity];
        array_push($inventory_list, $new_stock);

        // Insert new stock into the Stocks table
        // $insert_query = "INSERT INTO Stocks (restaurant, name, quantity) VALUES ( ?, ?, ?)";
        // if ($stmt = mysqli_prepare($db, $insert_query)) {
        //     mysqli_stmt_bind_param($stmt, "sss", $restaurant, $name, $quantity);
        //     mysqli_stmt_execute($stmt);
        //     header("Location: inventory.php");
        // } else {
        //     echo "ERROR: Could not prepare insert query" . mysqli_error($db);
        // }
    }

    //Change mode of view
    if(isset($_POST['mode'])&& $_POST['mode']==="back"){
        $mode = "view";
    }

    //Select ingredient to purchase
    if(isset($_POST['mode']) && $_POST['mode']==="select") {
        $purchase_id = $_POST['purchase_id'];

        // Find the stock with the matching ID from the inventory list
        $selected = null;
        foreach ($ingredient_list as $ingredient) {
            if ($ingredient['id'] == $purchase_id) {
                $selected = $ingredient;
                break;
            }
        }
        $selected['quantity']=0;
        $mode = "purchase";
    }
    
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase</title>
</head>
<body>
    <h1>Purchase Management</h1>
    <a href="/main.php">Back to Main</a>
    <a href="/inventory.php">Check Inventory</a>
    <h2>Purchase List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Name</th>
            <th>Price</th>
            <th>Purchase</th>
        </tr>
        <?php
        // while ($row = mysqli_fetch_assoc($result)) {
            
        foreach ($ingredient_list as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['supplier'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['price'] . "</td>";
            echo "<td>";
            echo "<form method='post' action=''>";
            echo "<input type='hidden' name='purchase_id' value='" . $row['id'] . "'>";
            echo "<input type='hidden' name='mode' value='select'>";
            echo "<button type='submit'>select</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <?php if($mode=="purchase") { ?>
    <h2>New Purchase</h2>
    <form method="post" action="">
        <input type="hidden" name="mode" value="purchase">
        <input type="hidden" name="purchase_id" value="<?php echo $selected['id'] ?>">
        <p>Supplier <input type="text" name="supplier" disabled value="<?php echo $selected['supplier'] ?>" required></p>
        <p>Name <input type="text" name="name" disabled value="<?php echo $selected['name'] ?>" required></p>
        <p>Quantity <input type="number" name="quantity" id="quantity" value="<?php echo $selected['quantity']; ?>" required></p>
        <p>Total Price <input type="number" name="price" id="price" disabled value="<?php echo $selected['price'] * $selected['quantity']; ?>" required></p>
        <button type="submit">purchase</button>
    </form>
    <form method="post" action="">
        <input type="hidden" name="mode" value="back">
        <button type="submit">Back</button>
    </form>
    <?php } ?>
    <script>
        document.getElementById('quantity').addEventListener('change', function() {
            var quantity = this.value;
            var price = <?php echo $selected['price'] ?>;
            document.getElementById('price').value = price * quantity;
        });
    </script>
</body>
</html>
