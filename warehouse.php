<!--- Warehouse Page ~ CSCI467 --->

<!--- Connect to the server --->
<?php
    //name of DB
    $dsn = "mysql:host=courses;dbname=z1957829";

    $host2 = 'blitz.cs.niu.edu';
    $port2 = 3306;
    $dbname2 = 'csci467';
    $user2 = 'student';
    $password2 = 'student';

    //test the connection
    try
    {
        $pdo = new PDO($dsn, "z1957829", "2004May16");
        $pdo2 = new PDO("mysql:host=$host2;port=$port2;dbname=$dbname2", $user2, $password2);
        $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } //end of try statement
    catch (PDOexception $e)
    {
        echo "Connection to database failed: " . $e->getMessage();
    } //end of catch statement
?>

<html>
<head>
    <title> Warehouse </title>
    <style>
            body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
            /* Navbar styles */
        .navbar {
            overflow: hidden;
            background-color: #343a40;
            border-radius: 8px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .navbar a {
            display: block;
            color: #f8f9fa;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #495057;
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="parts.php">Main Page</a>
        <a href="cart.php">Cart</a>
        <a href="employee_login.php">Employee Login</a>
    </div>
<h1> Warehouse Interface </h1>

    <!--- Ask what order they want to print --->
    <form method="POST">
        <label>Select OrderID:</label>
        <select name="orderNum" id="orderNum">
            <?php
                try {
                    $result = $pdo->query("SELECT * FROM Orders");
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $ID = $row['OrderID'];
                        echo "<option value='$ID'>$ID</option>";
                    }
                } catch (PDOException $e) {
                    echo "ERROR: " . $e->getMessage();
                } //end of try-catch statement
            ?>
        </select>
        <input type="submit" name="printInvoice" value="Print Invoice">
        <input type="submit" name="printShippingLabel" value="Print Shipping Label">
    </form>

    <!--- Validate Input and Execute Query for Invoice or Shipping Label --->
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            // Check if print shipping label button clicked
            if (isset($_POST["printShippingLabel"])) {
                $orderNum = $_POST["orderNum"];

                // Execute query to print invoice
                // (Code for printing invoice)

                echo "<h2> Shipping Label for OrderID: $orderNum </h2>";

                //query to get results
                $query = "SELECT Email, TotalPrice, Datee FROM Orders WHERE OrderID='$orderNum'";
                $stmt = $pdo->query($query);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$result)
                {
                    echo "No data found for $orderNum";
                }
                else
                {
                    //start table
                    echo "<table>";

                    //print out the headers of the file
                    echo "<tr>";
                    echo "<th> EMAIL </th>";
                    echo "<th> ORDER TOTAL </th>";
                    echo "<th> DATE </th>";
                    echo "</tr>";

                    //create arow
                    echo "<tr>";    
                    //fill row with data
                    echo "<td> {$result['Email']} </td>";
                    echo "<td> {$result['TotalPrice']} </td>";
                    echo "<td> {$result['Datee']} </td>";
                    //close the row
                    echo "</tr>";

                    //close the table
                    echo "</table>";
                }
            }
            // Check if print invoice button clicked
            elseif (isset($_POST["printInvoice"])) {
                $orderNum = $_POST["orderNum"];

                // Execute query to print shipping label
                // (Code for printing shipping label)

                echo "<h2> Invoice for OrderID: $orderNum </h2>";

                //query to get results
                $query2 = "SELECT ProductID, Quantity FROM ProductStored WHERE OrderID='$orderNum'";
                $stmt2 = $pdo->query($query2);
                $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

                //query to get description
                $query3 = "SELECT description FROM parts WHERE number='{$result2['ProductID']}'";
                $stmt3 = $pdo2->query($query3);
                $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);  
                    
                
                if (!$result2)
                {
                    echo "No data found for $orderNum";
                }
                else
                {
                    //start table
                    echo "<table>";

                    //print out the headers of the file
                    echo "<tr>";
                    echo "<th> PART NUMBER </th>";
                    //echo "<th> DESCRIPTION </th>";
                    echo "<th> QUANTITY </th>";
                    echo "</tr>";

                    //create a row
                    echo "<tr>";    
                    //fill row with data
                    echo "<td> {$result2['ProductID']} </td>";
                    //echo "<td> {$result3['description']} </td>";                    
                    echo "<td> {$result2['Quantity']} </td>";
                    //close the row
                    echo "</tr>";

                    //close the table
                    echo "</table><br>";

                    echo "<tr>";
                    echo "<th> DESCRIPTION </th>";
                    echo "</tr>";

                    //create a row for description
                    echo "<tr>";
                    //fill row with data
                    if ($result3)
                    {
                        echo "<td> {$result3['description']} </td>";
                    }
                    else
                    {
                        echo "<td> No description found </td>";
                    }
                    echo "</tr>";
                }
            }
        }
    ?>
</body>
</html>

