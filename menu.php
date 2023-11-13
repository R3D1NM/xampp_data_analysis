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

    //Get menu from the Drinks table based on the restaurant
    $query = "SELECT * FROM Drinks WHERE restaurant = ?";
    if ($stmt = mysqli_prepare($db, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $restaurant);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Fetch all rows and store them in an array
        $drinks_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $drinks_list[] = $row;
        }
    } else {
        echo "ERROR: Could not prepare query " . $query . "" . mysqli_error($db);
    }

    if(isset($_GET['type'])){
        $type = $_GET['type'];
    }else{
        $type = "dishes";
    }

    //POST Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_POST['type'])&&!isset(($_POST['mode']))){
            $type=$_POST['type'];
        }
        else if(isset($_POST['mode'])&&($_POST['mode']=="select")){
            $update_id = $_POST['update_id'];
            $type=$_POST['type'];
            // Find the menu with the matching ID from the menu list
            $selected = null;
            if($type==="dishes"){
                foreach ($dishes_list as $menu) {
                    if ($menu['id'] == $update_id) {
                        $selected = $menu;
                        break;
                    }
                }
            }else{
                foreach ($drinks_list as $menu) {
                    if ($menu['id'] == $update_id) {
                        $selected = $menu;
                        break;
                    }
                }
            }
            // echo $selected;
            //switch to update form
            $mode = "update";
        } else if (isset($_POST['mode'])&&($_POST['mode']=="back")){
            $mode = "create";
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
    <form method="post" action="">
        <p>
        <select name="type" required>
            <option value="dishes" <?php echo ($type==="dishes") ? 'selected' : '' ?>>dishes</option>
            <option value="drinks" <?php echo ($type==="drinks") ? 'selected' : '' ?>>drinks</option>
        </select>
        <button type="submit">switch</button>
        </p>
        
    </form>
    <h2>Menu List</h2>
    <table>
        <?php
        if($type==="dishes"){
            echo "<tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Veganism</th>
            <th>Manage</th>
            </tr>";
            foreach ($dishes_list as $row) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['price'] . "</td>";
                echo "<td>" . $row['veganism'] . "</td>";
                echo "<td>";
                echo "<form method='post' action='dishes.php'>";
                echo "<input type='hidden' name='mode' value='delete'>";
                echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
                echo "<button type='submit'>Delete</button>";
                echo "</form>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='update_id' value='" . $row['id'] . "'>";
                echo "<input type='hidden' name='mode' value='select'>";
                echo "<input type='hidden' name='type' value='dishes'>";
                echo "<button type='submit'>Update</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        }else{
            echo "<tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Alcoholic</th>
            <th>Milliliters</th>
            <th>Manage</th>
            </tr>";
            foreach ($drinks_list as $row) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['price'] . "</td>";
                echo "<td>" . $row['alcoholic'] . "</td>";
                echo "<td>" . $row['milliliters'] . "</td>";
                echo "<td>";
                echo "<form method='post' action='drinks.php'>";
                echo "<input type='hidden' name='mode' value='delete'>";
                echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
                echo "<button type='submit'>Delete</button>";
                echo "</form>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='update_id' value='" . $row['id'] . "'>";
                echo "<input type='hidden' name='mode' value='select'>";
                echo "<input type='hidden' name='type' value='drinks'>";
                echo "<button type='submit'>Update</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        }
        
        ?>
    </table>
    <h2><?php echo $mode==="update" ? 'Update Menu' : 'Create New Menu' ?></h2>
    <?php if($type==="dishes"){?>
    <form method="post" action="dishes.php">
        <input type="hidden" name="mode" value="<?php echo $mode ?>">
        <input type="hidden" name="update_id" value="<?php echo $mode==="update" ? $selected['id'] : '' ?>">
        <p>Name <input type="text" name="name" value="<?php echo $mode==="update" ? $selected['name'] : '' ?>" required></p>
        <p>Price <input type="number" name="price" value="<?php echo $mode==="update" ? $selected['price'] : '' ?>" required></p>
        <p>Veganism 
            <select name="veganism" required>
                <option value="Yes" <?php echo ($mode==="update" && $selected['veganism'] === 'Yes') ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?php echo ($mode==="update" && $selected['veganism'] === 'No') ? 'selected' : '' ?>>No</option>
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
    <?php } else { ?>
        <form method="post" action="drinks.php">
            <input type="hidden" name="mode" value="<?php echo $mode ?>">
            <input type="hidden" name="update_id" value="<?php echo $mode==="update" ? $selected['id'] : '' ?>">
            <p>Name <input type="text" name="name" value="<?php echo $mode==="update" ? $selected['name'] : '' ?>" required></p>
            <p>Price <input type="number" name="price" value="<?php echo $mode==="update" ? $selected['price'] : '' ?>" required></p>
            <p>Milliliters <input type="number" name="milliliters" value="<?php echo $mode === "update" ? $selected['milliliters'] : ''; ?>" required></p>
            <p>Alcoholic 
                <select name="alcoholic" required>
                    <option value="Yes" <?php echo ($mode === "update" && $selected['alcoholic'] === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                    <option value="No" <?php echo ($mode === "update" && $selected['alcoholic'] === 'No') ? 'selected' : ''; ?>>No</option>
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
    <?php } ?>

</body>
</html>
