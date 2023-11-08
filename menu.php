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

    //Get menu from the Dishes table based on the restaurant
    $query = "SELECT * FROM Dishes WHERE restaurant = ?";
    if ($stmt = mysqli_prepare($db, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $restaurant);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Fetch all rows and store them in an array
        $dishes_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $dishes_list[] = $row;
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
            case 'back':
                $mode = "create";
                break;
            case 'create':
                $name = $_POST['name'];
                $price = $_POST['price'];
                $veganism = $_POST['veganism'];

                // Insert new menu into the Dishes table
                $insert_query = "INSERT INTO Dishes (restaurant, name, price, veganism) VALUES (?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($db, $insert_query)) {
                    mysqli_stmt_bind_param($stmt, "ssss", $restaurant, $name, $price, $veganism);
                    mysqli_stmt_execute($stmt);
                    header("Location: menu.php");
                } else {
                    echo "ERROR: Could not prepare insert query" . mysqli_error($db);
                }
                break;
            case 'update':
                $name = $_POST['name'];
                $price = $_POST['price'];
                $veganism = $_POST['veganism'];
                $update_id = $_POST['update_id'];

                // Update the selected menu in the Dishes table
                $update_query = "UPDATE Dishes SET name = ?, price = ?, veganism = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($db, $update_query)) {
                    mysqli_stmt_bind_param($stmt, "sssi", $name, $price, $veganism, $update_id);
                    mysqli_stmt_execute($stmt);
                    header("Location: menu.php");
                } else {
                    echo "ERROR: Could not prepare update query" . mysqli_error($db);
                }

                //switch to create form
                $mode="create";
                break;
            case 'select':
                $update_id = $_POST['update_id'];

                // Find the menu with the matching ID from the menu list
                $selected = null;
                foreach ($dishes_list as $menu) {
                    if ($menu['id'] == $update_id) {
                        $selected = $menu;
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
    <title>Menu</title>
</head>
<body>
    <h1>Menu Management</h1>
    <a href="/main.php">Back to Main</a>
    <h2>Menu List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Veganism</th>
            <th>Manage</th>
        </tr>
        <?php
        foreach ($dishes_list as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['price'] . "</td>";
            echo "<td>" . $row['veganism'] . "</td>";
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
    <h2><?php echo $mode==="update" ? 'Update Menu' : 'Create New Menu' ?></h2>
    <form method="post" action="">
        <input type="hidden" name="mode" value="<?php echo $mode ?>">
        <input type="hidden" name="update_id" value="<?php echo $mode==="update" ? $selected['id'] : '' ?>">
        <p>Name <input type="text" name="name" value="<?php echo $mode==="update" ? $selected['name'] : '' ?>" required></p>
        <p>Price <input type="number" name="price" value="<?php echo $mode==="update" ? $selected['price'] : '' ?>" required></p>
        <p>Veganism 
            <select name="veganism" required>
                <option value="YES" <?php echo ($mode==="update" && $selected['veganism'] === 'YES') ? 'selected' : '' ?>>YES</option>
                <option value="NO" <?php echo ($mode==="update" && $selected['veganism'] === 'NO') ? 'selected' : '' ?>>NO</option>
            </select>
        </p>
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
