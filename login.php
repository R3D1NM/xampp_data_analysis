<?php
    session_start();
    //if already logged in
    if(isset($_SESSION['userid'])) {
        header("Location: /main.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //db connection
        $db=mysqli_connect("localhost:3306","team09","team09","team09");
        if(mysqli_connect_errno()){
            printf ("database connection failed: %s",mysqli_connect_error());
            exit();
        }

        //query id to get hashed pw from DB
        $query = "SELECT * FROM users WHERE user_id = ?";
        if($stmt=mysqli_prepare($db,$query)){
            mysqli_stmt_bind_param($stmt,"s",$id);
            $id = $_POST['id'];
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if($result){
                if(mysqli_num_rows($result)>0){
                    //get user info
                    $row = mysqli_fetch_assoc($result);
                    $hashed_password = $row['user_pw'];
                    $password = $_POST['password'];
                    //verify password
                    if(password_verify($password,$hashed_password)){
                        //generate new session and set username
                        $_SESSION['userid'] = $row['id'];
                        $_SESSION['username']=$row['user_name'];
                        $_SESSION['restaurant'] = $row['restaurant'];

                        header("Location: /main.php");
                        exit();
                    } else {
                        echo "Login Failed: Check id or password again <a href='/login.html'>Try again</a>";
                    }
                    
                }else{
                    echo "Login Failed: Check id or password again <a href='/login.html'>Try again</a>";
                }
            } else {
                echo "Error : " . mysqli_error($db);
            }
        }else{
            echo "ERROR: Could not prepare query".$query."".mysqli_error($db);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
    <h1>Restaurant Management Service</h1>
    <h2>Login</h2>
    <div name='login_form'>
        <form method='POST' action=''>
            <p>id <input type='text' name='id' required></p>
            <p>pw <input type='password' name='password' required></p>
            <button type='submit'>login</button>
        </form>
    </div>
    <div>
        <p>Join Us!  <a href='signin.php'>sign in</a></p>
        
    </div>
    
</body>
</html>
