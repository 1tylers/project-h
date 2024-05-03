<!DOCTYPE html>
<html>
<head>
    <title>CHECKOUT</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        h2 {
            text-align: center;
            color: #343a40;
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #e9ecef;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
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
            }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="parts.php">Main Page</a>
        <a href="cart.php">Cart</a>
        <a href="login.php">Employee Login</a>
    </div>
    <?php
    session_start();

    $dsn = "mysql:host=courses;dbname=z1957829";

    try {
        $pdo = new PDO($dsn, "z1957829", "2004May16");
    } catch (PDOexception $e) {
        echo "Connection to database failed: " . $e->getMessage();
    }
    $weight= $_SESSION['weight'];
    $sql = "SELECT Price FROM Brackets WHERE Min <= :weight AND Max >= :weight";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':weight', $weight);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $shipping = $row["Price"];

        } else {
            echo "No price found for the given weight.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    $subtotal = $_SESSION['total'];
    $total = $subtotal + $shipping;
    $_SESSION['final']=$total;
    ?>

    <table>
        <tr>
            <th>Order</th>
            <th>Taking</th>
        </tr>
        <tr>
            <td>Subtotal:</td>
            <td><?php echo "$" . number_format($subtotal, 2); ?></td>
        </tr>
        <tr>
            <td>Shipping and Handling:</td>
            <td><?php echo "$" . number_format($shipping, 2); ?></td>
        </tr>
        <tr>
            <td>Total:</td>
            <td><?php echo "$" . number_format($total, 2); ?></td>
        </tr>
    </table>

    
    <form id="checkoutForm" action="hcheckout.php" method="post">
        <h2>Delivery</h2>
        <label for="name">Name:</label>
        <input type="text" name="name" required><br>
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number" required><br>

        <label for="address">Address:</label>
        <input type="text" name="address" required><br>
        
        <h2>Checkout</h2>
        <label for="name">Cardholder Name:</label><br>
        <input type="text" id="name" name="cname" required><br>
        <label for="cc">Credit Card Number:</label><br>
        <input type="text" id="cc" name="cc" required><br>
        
        <label for="exp">Expiration Date (MM/YYYY):</label><br>
        <input type="text" id="exp" name="exp" required><br>

        <input type="submit" name="submit_order" value="Place Order">
    </form>

    <script>
        // JavaScript function to hide the form and display a confirmation popup
        function handleSuccess() {
            // Hide the form
            document.getElementById("checkoutForm").style.display = "none";
            // Display a confirmation popup
            alert("Your purchase was successful. Thank you!");
        }
    </script>
</body>
</html>

<?php
session_start();
if(isset($_POST['submit_order'])) {
    // Collect delivery information
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    
    // Collect payment information
    $cardholder_name = $_POST['cname'];
    $credit_card_number = $_POST['cc'];
    $expiration_date = $_POST['exp'];
    $total = $_SESSION['final'];
    $trans = '907-' . rand(100000000, 999999999) . '-244';
    $data = array(
        'vendor' => 'Project-H',
        'trans' => $trans,
        'cc' => $credit_card_number,
        'name' => $cardholder_name, 
        'exp' => $expiration_date, 
        'amount' => $total
    );
    
    // Define the URL where you want to send the data
    $url = 'http://blitz.cs.niu.edu/CreditCard/';

    // Define options for the HTTP request
    $options = array(
        'http' => array(
            'header' => array('Content-type: application/json', 'Accept: application/json'),
            'method' => 'POST',
            'content'=> json_encode($data)
        )
    );

    // Create a stream context
    $context = stream_context_create($options);

    // Make the request and get the response
    $result = file_get_contents($url, false, $context);

    $response = json_decode($result, true);

    // Check if there are errors in the response
    if (isset($response['errors'])) {
        // Display error messages
        foreach ($response['errors'] as $error) {
            echo $error . "<br>";
        }
    } else {
        // Display the authorization number if successful
        echo "Thank you for your purchase " . $name . "<br>";
        echo "Order Number: " . $response['authorization'];
        
        // Update the ORDERS table
        $dsn = "mysql:host=courses;dbname=z1957829";

        try {
            $pdo = new PDO($dsn, "z1957829", "2004May16");
            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO Orders (OrderID, Address, Email, TotalPrice, TotalWeight, Datee, Status) VALUES (?, ?, ?, ?, ?, CURDATE(), ?)");
            $weight= $_SESSION['weight'];
            // Bind parameters
            $stmt->bindParam(1, $response['authorization']);
            $stmt->bindParam(2, $address);
            $stmt->bindParam(3, $email);
            $stmt->bindParam(4, $total);
            $stmt->bindParam(5, $weight); // You need to define $weight
            $stmt->bindValue(6, 'Processing');

            // Execute the statement
            $stmt->execute();
            
            // Loop through the items in the cart and insert them into the ProductStored table
            foreach ($_SESSION['Add'] as $product_id => $quantity) {
                $insertProductSQL = "INSERT INTO ProductStored (OrderID, ProductID, Quantity) 
                                     VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($insertProductSQL);
                $stmt->bindParam(1, $response['authorization']);
                $stmt->bindParam(2, $product_id);
                $stmt->bindParam(3, $quantity);
                $stmt->execute();
                
                // Update product quantity
                $updateProductSQL = "UPDATE Inventory SET Quantity = Quantity - ? WHERE ProductID = ?";
                $stmt = $pdo->prepare($updateProductSQL);
                $stmt->bindParam(1, $quantity);
                $stmt->bindParam(2, $product_id);
                $stmt->execute();
            }
          
            // Call JavaScript function to handle success
            echo "<script>handleSuccess();</script>";
            session_unset();
            session_destroy();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

