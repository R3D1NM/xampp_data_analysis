<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="main.css" media="all" rel="Stylesheet" type="text/css" />
    <title>Restaurant Statistics</title>
</head>
<body>
    <h1>Restaurant Statistics</h1>
    <a href="/main.php">Back to Main</a>
    <!-- 날짜 선택 폼 -->
    <form action="" method="post">
        <label for="singleDate">Select Date for Daily Statistics:</label>
        <input type="date" id="singleDate" name="singleDate">
        <br>
        <label for="startDate">Select Start Date for Periodic Statistics:</label>
        <input type="date" id="startDate" name="startDate">
        <br>
        <label for="endDate">Select End Date for Periodic Statistics:</label>
        <input type="date" id="endDate" name="endDate">
        <br>
        <input type="submit" value="Show Statistics">
    </form>

    <?php
    // 데이터베이스 연결 설정
    $host = 'localhost:3306'; // 데이터베이스 호스트
    $username = 'team09'; // 데이터베이스 사용자 이름
    $password = 'team09'; // 데이터베이스 비밀번호
    $database = 'team09'; // 데이터베이스 이름


    // 데이터베이스 연결
    $conn = new mysqli($host, $username, $password, $database);

    // 연결 오류 확인
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $singleDate = $_POST['singleDate'];
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        $restaurantId = 1; // 예시 레스토랑 ID

        // 일일 통계: 일 매출
        if (!empty($singleDate)) {
            echo "<h2>Daily Sales for " . $singleDate . "</h2>";
            $query = "SELECT DATE(order_time) AS date, SUM(price) AS daily_sales FROM ORDERS WHERE restaurant = ? AND DATE(order_time) = ? GROUP BY 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("is", $restaurantId, $singleDate);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<table border='1'><tr><th>Date</th><th>Daily Sales</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['daily_sales'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            $stmt->close();
        }

        // 기간 통계: 일별 매출
        if (!empty($startDate) && !empty($endDate)) {
            echo "<h2>Periodic Sales from " . $startDate . " to " . $endDate . "</h2>";
            $query = "SELECT DATE(order_time) AS date, SUM(price) as sales, RANK() OVER(ORDER BY SUM(price) DESC) AS rank FROM ORDERS WHERE restaurant = ? AND DATE(order_time) BETWEEN ? AND ? GROUP BY 1 ORDER BY 3";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iss", $restaurantId, $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<table border='1'><tr><th>Date</th><th>Sales</th><th>Rank</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['sales'] . "</td>";
                echo "<td>" . $row['rank'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            $stmt->close();
        }
    }

    // Add this code inside the PHP section after your existing database queries

    // SQL query to get customer performance details
    $query = "SELECT customer AS customer_id, guests_number, count(*) AS count FROM RESERVATIONS GROUP BY 1,2 ORDER BY 3 DESC";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Preparing to display the results in an HTML table
        echo "<h2>Customer Performance Details</h2>";
        echo "<table border='1'><tr><th>Customer ID</th><th>Guests Number</th><th>Count</th></tr>";

        // Fetch and display each row of data
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["customer_id"]. "</td><td>" . $row["guests_number"] . "</td><td>" . $row["count"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No results found.";
    }


    // 데이터베이스 연결 닫기
    $conn->close();
    ?>
</body>
</html>
