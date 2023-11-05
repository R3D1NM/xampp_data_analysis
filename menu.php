<?php
    session_start();
    
    // Database connection
    $db = mysqli_connect("localhost:3306", "team09", "team09", "team09");
    if (mysqli_connect_errno()) {
        printf("Database connection failed: %s", mysqli_connect_error());
        exit();
    }
    
    // Fetching restaurant from user login
    $restaurant = $_SESSION['restaurant_id'];
    
    // Fetching menu from the Dishes table based on the restaurant
    $query = "SELECT * FROM Dishes WHERE restaurant = ?";
    if ($stmt = mysqli_prepare($db, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $restaurant);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo "ERROR: Could not prepare query " . $query . "" . mysqli_error($db);
    }
    
    // Delete menu item
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
    
    // Other CRUD operations and HTML to display the menu list
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
</head>
<body>
    <h1>Restaurant Management Service - Menu</h1>

    <h2>Menu List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Veganism</th>
            <th>Action</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['price'] . "</td>";
            echo "<td>" . $row['veganism'] . "</td>";
            echo "<td>";
            echo "<form method='post' action=''>";
            echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
            echo "<button type='submit'>Delete</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <h2>Create New Menu</h2>
    <form method="post" action="">
        <p>Name <input type="text" name="name" required></p>
        <p>Price <input type="text" name="price" required></p>
        <p>Veganism <input type="text" name="veganism" required></p>
        <button type="submit">Create</button>
    </form>

    <!-- Logic for inserting new menu item and updating existing menu items -->
</body>
</html>
