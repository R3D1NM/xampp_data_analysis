<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sign in</title>
</head>
<body>
    <h1>Restaurant Management Service</h1>
    <h2>Register Restaurant</h2>
    <div name="register_restaurant">
        <form method="POST" action="">
            <p>name <input type="text" name="name" required></p>
            <p>contact <input type="text" name="contact" required></p>
            <p>location <input type='text' name="location" required></p>
            <p>category 
                <select name="category" required>
                    <option value="Korean">Korean</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Japanese">Japanese</option>
                    <option value="Western">Western</option>
                    <option value="Asian">Asian</option>
                </select>
            </p>
            <button type="submit">Register</button>
        </form>
    </div>

    <?php
        session_start();
        $db=mysqli_connect("localhost:3306","team09","team09","team09");
        if(mysqli_connect_errno()){
            printf ("database connection failed: %s",mysqli_connect_error());
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST['name'];
            $contact = $_POST['contact'];
            $location = $_POST['location'];
            $category = $_POST['category'];
            $userid=$_SESSION['userid'];

            $query = "INSERT INTO restaurants (name, contact, location, category, userid) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($db, $query)) {
                mysqli_stmt_bind_param($stmt, "sssss", $name, $contact, $location,$category,$userid);
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: /main.php");
                    exit();
                } else {
                    echo "Error: " . mysqli_error($db);
                }
            } else {
                echo "ERROR: Could not prepare query" . $query . "" . mysqli_error($db);
            }
        }
    ?>
</body>
</html>
