<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sign in</title>
</head>
<body>
    <a href="/main.php"><h1>Restaurant Management Service</h1></a>
    <h2>Sign in</h2>
    <div name="signin_form">
        <form method="POST" action="">
            <p>id <input type="text" name="id" required></p>
            <p>username <input type="text" name="username" required></p>
            <p>pw <input type='password' name="password" required></p>

    <h2>Register Restaurant</h2>
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
        //connect DB
        $db=mysqli_connect("localhost:3306","team09","team09","team09");
        if(mysqli_connect_errno()){
            printf ("database connection failed: %s",mysqli_connect_error());
            exit();
        }

        //Register new user
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $userid = $_POST['id'];
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $name = $_POST['name'];
            $contact = $_POST['contact'];
            $location = $_POST['location'];
            $category = $_POST['category'];

            //Begin Transaction
            mysqli_begin_transaction($db);

            //Register Restaurant first
            $query = "INSERT INTO restaurants (name, contact, location, category) VALUES (?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($db, $query)) {
                mysqli_stmt_bind_param($stmt, "ssss", $name, $contact, $location,$category);
                if (!mysqli_stmt_execute($stmt)) {
                    echo "Error: " . mysqli_error($db);
                }
            } else {
                echo "ERROR: Could not prepare query" . $query . "" . mysqli_error($db);
            }

            //Get Restaurant ID
            $query = "SELECT id FROM RESTAURANTS WHERE name=? and contact=?";
            if ($stmt = mysqli_prepare($db, $query)) {
                mysqli_stmt_bind_param($stmt,"ss",$name,$contact);

                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if($result){
                    if(mysqli_num_rows($result)>0){
                        //get user info
                        $row = mysqli_fetch_assoc($result);
                        $restaurant = $row['id'];
                        
                    }else{
                        echo "Regiser Failed";
                    }
                }
            } else {
                echo "ERROR: Could not prepare query" . $query . "" . mysqli_error($db);
            }

            //Create new User
            $query = "INSERT INTO users (user_id, user_name, user_pw, restaurant) VALUES (?, ?, ?,?)";
            if ($stmt = mysqli_prepare($db, $query)) {
                mysqli_stmt_bind_param($stmt, "ssss", $userid, $username, $password, $restaurant);
                if (mysqli_stmt_execute($stmt)) {
                    // Commit Transaction
                    mysqli_commit($db);
                    header("Location: /login.php");
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
