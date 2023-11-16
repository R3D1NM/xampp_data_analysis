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

    //Get employee from the Employee table based on the restaurant
    $query = "SELECT * FROM EMPLOYEES WHERE restaurant = ?";
    if ($stmt = mysqli_prepare($db, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $restaurant);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Fetch all rows and store them in an array
        $emp_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $emp_list[] = $row;
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
                    $delete_query = "DELETE FROM EMPLOYEES WHERE id = ?";
                    if ($stmt = mysqli_prepare($db, $delete_query)) {
                        mysqli_stmt_bind_param($stmt, "i", $delete_id);
                        mysqli_stmt_execute($stmt);
                        header("Location: employee.php");
                    } else {
                        echo "ERROR: Could not prepare delete query" . mysqli_error($db);
                    }
                }
                break;
            case 'back':
                $mode = "create";
                break;
            case 'create':
                $position = $_POST['position'];
                $salary = $_POST['salary'];
                $name = $_POST['name'];
                $contact = $_POST['contact'];
                $age = $_POST['age'];
                $hire_date = $_POST['hire_date'];
                $grade = $_POST['grade'];

                // Insert new employees into the Employees table
                $insert_query = "INSERT INTO EMPLOYEES (restaurant, position, salary, name, contact, age, hire_date, grade) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
                if ($stmt = mysqli_prepare($db, $insert_query)) {
                    mysqli_stmt_bind_param($stmt, "ssississ", $restaurant, $position, $salary, $name, $contact, $age, $hire_date, $grade);
                    mysqli_stmt_execute($stmt);
                    header("Location: employee.php");
                } else {
                    echo "ERROR: Could not prepare insert query" . mysqli_error($db);
                }
                break;
            case 'update':
                if (isset($_POST['update_id'])) {
                    $update_id = $_POST['update_id'];
                    $position = $_POST['position'];
                    $salary = $_POST['salary'];
                    $name = $_POST['name'];
                    $contact = $_POST['contact'];
                    $age = $_POST['age'];
                    $hire_date = $_POST['hire_date'];
                    $grade = $_POST['grade'];

                    // Update the selected employees in the Employee table
                    $update_query = "UPDATE EMPLOYEES SET restaurant=?, position=?, salary=?, name=?, contact=?, age=?, hire_date=?, grade=? WHERE id=? AND restaurant=?";
                    if ($stmt = mysqli_prepare($db, $update_query)) {
                        mysqli_stmt_bind_param($stmt, "ssississis", $restaurant, $position, $salary, $name, $contact, $age, $hire_date, $grade, $update_id, $restaurant);
                        mysqli_stmt_execute($stmt);
                        header("Location: employee.php");
                    } else {
                        echo "ERROR: Could not prepare update query" . mysqli_error($db);
                    }
    
                }
                //switch to create form
                $mode="create";
                break;
            case 'select':
                $update_id = $_POST['update_id'];

                // Find the employee with the matching ID from the employee list
                $selected = null;
                foreach ($emp_list as $emp) {
                    if ($emp['id'] == $update_id) {
                        $selected = $emp;
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
    <title>Employee Management</title>
</head>
<body>
    <h1>Employee Management</h1>
    <a href="/main.php">Back to Main</a>
    <h2>Employee List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Restaurant</th>
            <th>Position</th>
            <th>Salary</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Age</th>
            <th>Hire_Date</th>
            <th>Grade</th>
        </tr>
        <?php
        foreach ($emp_list as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['restaurant'] . "</td>";
            echo "<td>" . $row['position'] . "</td>";
            echo "<td>" . $row['salary'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['contact'] . "</td>";
            echo "<td>" . $row['age'] . "</td>";
            echo "<td>" . $row['hire_date'] . "</td>";
            echo "<td>" . $row['grade'] . "</td>";
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
    <h2><?php echo $mode==="update" ? 'Update Employee' : 'Create New Employee' ?></h2>
    <form method="post" action="">
        <input type="hidden" name="mode" value="<?php echo $mode ?>">
        <input type="hidden" name="update_id" value="<?php echo $mode==="update" ? $selected['id'] : '' ?>">
        <p>Restaurant <input type="text" name="restaurant" value="<?php echo $mode==="update" ? $selected['restaurant'] : '' ?>" required></p>
        <p>Position <input type="text" name="position" value="<?php echo $mode==="update" ? $selected['position'] : '' ?>" required></p>
        <p>Salary <input type="number" name="salary" value="<?php echo $mode==="update" ? $selected['salary'] : '' ?>" required></p>
        <p>Name <input type="text" name="name" value="<?php echo $mode==="update" ? $selected['name'] : '' ?>" required></p>
        <p>Contact <input type="tel" name="contact" placeholder="01012345678" value="<?php echo $mode==="update" ? $selected['contact'] : '' ?>" required pattern="[0-9]{3}[0-9]{4}[0-9]{4}"></p>
        <p>Age <input type="number" name="age" value="<?php echo $mode==="update" ? $selected['age'] : '' ?>" required></p>
        <p>Hire_Date <input type="date" name="hire_date" value="<?php echo $mode==="update" ? $selected['hire_date'] : '' ?>" required></p>
        <p>Grade <input type="text" name="grade" value="<?php echo $mode==="update" ? $selected['grade'] : '' ?>" required></p>
        <button type="submit"><?php echo $mode==="update" ? 'Update' : 'Create' ?></button>
    </form>
    <h1>Employee Salary Report</h1>
    <table>
        <tr>
            <th>Position</th>
            <th>Grade</th>
            <th>Total Salary</th>
            <th>Count</th>
        </tr>
        <?php
            // SQL 쿼리 준비
            $stat_query = "SELECT 
                        CASE WHEN position IS NULL THEN 'POSITION_TOTAL' ELSE position END AS position, 
                        CASE WHEN grade IS NULL THEN 'GRADE_TOTAL' ELSE grade END AS grade, 
                        SUM(salary) AS total_salary, 
                        COUNT(*) AS count 
                        FROM EMPLOYEES 
                        WHERE restaurant = ? 
                        GROUP BY position, grade WITH ROLLUP";


            $stmt = mysqli_prepare($db, $stat_query);
            mysqli_stmt_bind_param($stmt, "s", $restaurant);
            mysqli_stmt_execute($stmt);
            $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['position']) . "</td>";
            echo "<td>" . htmlspecialchars($row['grade']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_salary']) . "</td>";
            echo "<td>" . htmlspecialchars($row['count']) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <?php if($mode=="update") { ?>
        <form method="post" action="">
            <input type="hidden" name="mode" value="back">
            <button type="submit">Back</button>
        </form>
    <?php } ?>
</body>
</html>
