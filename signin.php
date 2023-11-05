<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sign in</title>
</head>
<body>
    <h1>Restaurant Management Service</h1>
    <h2>Sign in</h2>
    <div name="signin_form">
        <form method="POST" action="">
            <p>id <input type="text" name="id" required></p>
            <p>username <input type="text" name="username" required></p>
            <p>pw <input type='password' name="password" required></p>
            <button type="submit">sign in</button>
        </form>
    </div>

    <?php
        $db=mysqli_connect("localhost:3306","team09","team09","team09");
        if(mysqli_connect_errno()){
            printf ("database connection failed: %s",mysqli_connect_error());
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $userid = $_POST['id'];
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $query = "INSERT INTO users (userid, username, userpw) VALUES (?, ?, ?)";
            if ($stmt = mysqli_prepare($db, $query)) {
                mysqli_stmt_bind_param($stmt, "sss", $userid, $username, $password);
                if (mysqli_stmt_execute($stmt)) {
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
