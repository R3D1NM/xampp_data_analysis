<?php
    session_start();
    //if already logined
    if(isset($_SESSION['userid'])) {
        header("Location: /main.php");
        exit();
    }

    //db connection
    $db=mysqli_connect("localhost:3306","team09","team09","team09");
    if(mysqli_connect_errno()){
        printf ("database connection failed: %s",mysqli_connect_error());
        exit();
    }

    //query id to get hashed pw from DB
    $query = "SELECT * FROM users WHERE userid = ?";
    if($stmt=mysqli_prepare($db,$query)){
        mysqli_stmt_bind_param($stmt,"s",$id);
        $id = $_POST['id'];
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if($result){
            if(mysqli_num_rows($result)>0){
                //get user info
                $row = mysqli_fetch_assoc($result);
                $hashed_password = $row['userpw'];
                $password = $_POST['password'];
                //verify password
                if(password_verify($password,$hashed_password)){
                    //generate new session and set username
                    $_SESSION['userid'] = $row['userid'];
                    $_SESSION['username']=$row['username'];
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
        echo "ERROR: Could not prepeare query".$query."".mysqli_error($db);
    }

    
?>
