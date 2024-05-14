<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
        @media print {
            .noPrint {
                display: none;
            }
        }
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        th:first-child, td:first-child {
            border-left: none;
        }
        th:last-child, td:last-child {
            border-right: none;
        }
        .status-select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .status-btn {
            padding: 5px 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
        }
        .status-btn:hover {
            background-color: #45a049;
        }
        .action-btn {
            padding: 5px 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background-color: #007bff;
            color: white;
        }
        .action-btn:hover {
            background-color: #0056b3;
        }

        .logout {
        	display: inline-block;
        	padding: 10px 20px;
        	text-decoration: none;
        	background-color: #C35048;
        	color: white;
        	border-radius: 5px;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<div class="navbar noPrint" >
    <a href="parts.php">Main Page</a>
    <a href="cart.php">Cart</a>
    <a href="login.php">Employee Login</a>
</div>
<h1 class="noPrint" >Welcome to Project H</h1>
<div class="container">
    <h2 class="noPrint" >Orders</h2>
    <table class="noPrint">
        <tr>
            <th>OrderID</th>
            <th>Address</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php

		session_start();

		if(!isset($_SESSION["username"]))
		{
			header("location:login.php");
		}

        // Database credentials
        $host1 = 'courses';
        $dbname1 = 'z1957829';
        $user1 = 'z1957829';
        $password1 = '2004May16';
        $host2 = 'blitz.cs.niu.edu';
        $port2 = 3306;
        $dbname2 = 'csci467';
        $user2 = 'student';
        $password2 = 'student';
        // First database connection
        try {
            $pdo1 = new PDO("mysql:host=$host1;dbname=$dbname1", $user1, $password1);
            $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection to database 1 failed: " . $e->getMessage();
        }
        // Second database connection
        try {
            $pdo2 = new PDO("mysql:host=$host2;port=$port2;dbname=$dbname2", $user2, $password2);
            $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection to database 2 failed: " . $e->getMessage();
        }
        try {
            $result = $pdo1->query("SELECT * FROM Orders");
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['OrderID']}</td>";
                $orderNum=$row['OrderID'];
                echo "<td>{$row['Address']}</td>";
                echo "<td>";
                echo "<form method='POST'>";
                echo "<select class='status-select' name='status'>";
                echo "<option value='Processing' " . ($row['Status'] == 'Processing' ? 'selected' : '') . ">Processing</option>";
                echo "<option value='Shipped' " . ($row['Status'] == 'Shipped' ? 'selected' : '') . ">Shipped</option>";
                echo "<option value='Delivered' " . ($row['Status'] == 'Delivered' ? 'selected' : '') . ">Delivered</option>";
                echo "</select>";
                echo "</td>";
                echo "<input type='hidden' name='orderNumber' value='{$row['OrderID']}'>";
                echo "<td><input class='status-btn' type='submit' name='updateStatus' value='Update'></td>";
                echo "</form>";
                echo "<td>";
                echo "<form method='POST'>";
                echo "<input type='hidden' name='orderNum' value='{$row['OrderID']}'>";
                echo "<input class='action-btn' type='submit' name='printInvoice' value='Invoice'>";
                echo "<input class='action-btn' type='submit' name='printShippingLabel' value='Shipping Label'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </table>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateStatus"])) {
    // Check if the form is submitted and the updateStatus button is clicked
    
    // Get the order ID and new status from the form
    $orderId = $_POST["orderNumber"];
    $newStatus = $_POST["status"];

    try {
        // Update the status in the database
        $stmt = $pdo1->prepare("UPDATE Orders SET Status = :status WHERE OrderID = :orderId");
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();

        $query = "SELECT Email FROM Orders WHERE OrderID='$orderId'";
        $stmt = $pdo1->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result)
        {
            echo "No email";
        }//end of if statement
        else
        {
            echo "Status is now $newStatus Email sent to $result[Email]";
        }//end of else statement

        // Redirect to the current page to refresh the table
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
        header("Refresh:0");
    } catch (PDOException $e) {
        echo "Error updating status: " . $e->getMessage();
    }
}
?>

    <?php
    // Handle button actions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if print invoice button clicked
        if (isset($_POST["printShippingLabel"])) {
            $orderNum = $_POST["orderNum"];
            // Execute query to print invoice
            // (Code for printing invoice)
            //query to get results
            $query = "SELECT Email, TotalPrice, Address,Datee FROM Orders WHERE OrderID='$orderNum'";
            $stmt = $pdo1->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                echo "No data found for $orderNum";
            } else {
                                    //start table
                    echo "<table>";

                    //print out the headers of the file
                    echo "<tr>";
                    echo "<th> EMAIL </th>";                        
                    echo "<th> ADDRESS </th>";
                    echo "<th> DATE </th>";
                    echo "</tr>";

                    //create arow
                    echo "<tr>";    
                    //fill row with data
                    echo "<td> {$result['Email']} </td>";
                    echo "<td> {$result['Address']} </td>";

                    echo "<td> {$result['Datee']} </td>";
                    //close the row
                    echo "</tr>";
                //close the table
                echo "</table>";
                echo '<button onclick="window.print();" class="noPrint">Print</button>';

            }
        }
        // Check if print shipping label button clicked
        elseif (isset($_POST["printInvoice"])) {
            $orderNum = $_POST["orderNum"];
            // Execute query to print shipping label
            // (Code for printing shipping label)
            //query to get results
            $query2 = "SELECT ProductID, Quantity FROM ProductStored WHERE OrderID='$orderNum'";
            $stmt2 = $pdo1->query($query2);
            $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            //query to get description
            $query3 = "SELECT description FROM parts WHERE number='{$result2['ProductID']}'";
            $stmt3 = $pdo2->query($query3);
            $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            $query4 = "SELECT TotalPrice FROM Orders WHERE OrderID='$orderNum'";
            $stmt4 = $pdo1->query($query4);
            $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
            if (!$result2) {
                echo "No data found for $orderNum";
            } else {
                //start table
                echo "<table>";
                //print out the headers of the file
                echo "<tr>";
                echo "<th> PART NUMBER </th>";
                echo "<th> QUANTITY </th>";
                echo "<th> DESCRIPTION </th>";
                echo "</tr>";

                foreach ($result2 as $row)
                {
                //create a row
                  echo "<tr>";
                //fill row with data
                  echo "<td> {$row['ProductID']} </td>";
                    //echo "<td> {$result3['description']} </td>";                    
                  echo "<td> {$row['Quantity']} </td>";

                  $query3 = "SELECT description FROM parts WHERE number='{$row['ProductID']}'";
                  $stmt3 = $pdo2->query($query3);
                  $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                  echo "<td> {$result3['description']} </td>";
                //close the row
                  echo "</tr>";
                }
                //close the table
                echo "</table><br>";
                //output description
                echo "<table>"; 
                 echo "<th> Total </th>";
                 echo "<tr><td> {$result4['TotalPrice']} </td></tr>";
                echo "</table><br>";
                echo '<button onclick="window.print();" class="noPrint">Print</button>';

            }
        }     
    }
    ?>

	<br><br>
	<a href="logout.php" class="logout">Logout</a>
    
</div>
</body>
</html>
