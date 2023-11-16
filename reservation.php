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

    //Get reservations from the Reservations table based on the restaurant
    $query = "SELECT * FROM RESERVATIONS WHERE restaurant = ?";
    if ($stmt = mysqli_prepare($db, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $restaurant);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Fetch all rows and store them in an array
        $reserv_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserv_list[] = $row;
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
                    $delete_query = "DELETE FROM RESERVATIONS WHERE id = ?";
                    if ($stmt = mysqli_prepare($db, $delete_query)) {
                        mysqli_stmt_bind_param($stmt, "i", $delete_id);
                        mysqli_stmt_execute($stmt);
                        header("Location: reservation.php");
                    } else {
                        echo "ERROR: Could not prepare delete query" . mysqli_error($db);
                    }
                }
                break;
            case 'back':
                $mode = "create";
                break;
            case 'create':
                $restaurant = $_POST['restaurant'];
                $customer = $_POST['customer'];
                $date = $_POST['date'];
                $time = $_POST['time'];
                $guests_number = $_POST['guests_number'];
                $r_table = $_POST['r_table'];

                // Insert new reservations into the Reservations table
                $insert_query = "INSERT INTO RESERVATIONS (restaurant, customer, date, time, guests_number, r_table) VALUES (?, ?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($db, $insert_query)) {
                    mysqli_stmt_bind_param($stmt, "ssssis", $restaurant, $customer, $date, $time, $guests_number, $r_table);
                    mysqli_stmt_execute($stmt);
                    header("Location: reservation.php");
                } else {
                    echo "ERROR: Could not prepare insert query" . mysqli_error($db);
                }
                break;
            case 'update':
                if (isset($_POST['update_id'])) {
                    $update_id = $_POST['update_id'];
                    $restaurant = $_POST['restaurant'];
                    $customer = $_POST['customer'];
                    $date = $_POST['date'];
                    $time = $_POST['time'];
                    $guests_number = $_POST['guests_number'];
                    $r_table = $_POST['r_table'];
    
                    // Update the selected reservations in the Reservations table
                    $update_query = "UPDATE RESERVATIONS SET restaurant=?, customer= ?, date= ?, time= ?, guests_number= ?, r_table = ? WHERE id = ?";
                    if ($stmt = mysqli_prepare($db, $update_query)) {
                        mysqli_stmt_bind_param($stmt, "ssssisi", $restaurant, $customer, $date, $time, $guests_number, $r_table, $update_id);
                        mysqli_stmt_execute($stmt);
                        header("Location: reservation.php");
                    } else {
                        echo "ERROR: Could not prepare update query" . mysqli_error($db);
                    }
    
                }
                //switch to create form
                $mode="create";
                break;
            case 'select':
                $update_id = $_POST['update_id'];

                // Find the reservation with the matching ID from the reservation list
                $selected = null;
                foreach ($reserv_list as $rsv) {
                    if ($rsv['id'] == $update_id) {
                        $selected = $rsv;
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
    <title>Reservation Management</title>
</head>
<body class="body">
    <h1>Reservation Management</h1>
    <a href="/main.php">Back to Main</a>
    <div class="flex">
        <div class="list">
            <h2>Reservation List</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Restaurant</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Guest Number</th>
                    <th>R_Table</th>
                </tr>
                <?php
                foreach ($reserv_list as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['restaurant'] . "</td>";
                    echo "<td>" . $row['customer'] . "</td>";
                    echo "<td>" . $row['date'] . "</td>";
                    echo "<td>" . $row['time'] . "</td>";
                    echo "<td>" . $row['guests_number'] . "</td>";
                    echo "<td>" . $row['r_table'] . "</td>";
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
            <h2><?php echo $mode==="update" ? 'Update Reservation' : 'Create New Reservation' ?></h2>
            <form method="post" action="">
                <input type="hidden" name="mode" value="<?php echo $mode ?>">
                <input type="hidden" name="update_id" value="<?php echo $mode==="update" ? $selected['id'] : '' ?>">
                <p>Restaurant <input type="text" name="restaurant" value="<?php echo $mode==="update" ? $selected['restaurant'] : '' ?>" required></p>
                <p>Customer <input type="text" name="customer" value="<?php echo $mode==="update" ? $selected['customer'] : '' ?>" required></p>
                <p>Date <input type="date" name="date" value="<?php echo $mode==="update" ? $selected['date'] : '' ?>" required></p>
                <p>Time <input type="time" name="time" value="<?php echo $mode==="update" ? $selected['time'] : '' ?>" required></p>
                <p>Guest Number <input type="number" name="guests_number" value="<?php echo $mode==="update" ? $selected['guests_number'] : '' ?>" required></p>
                <p>R_Table <input type="text" name="r_table" value="<?php echo $mode==="update" ? $selected['r_table'] : '' ?>" required></p>
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
