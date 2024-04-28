<html>
<head>
<title>CHECKOUT</title>
</head>
<body>
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
      <table border="1">
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
      <h2>Delivery</h2>
      <form action="hcheckout.php" method="post">
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
        <input type="text" id="name" name="name" required><br>
         <label for="cc">Credit Card Number:</label><br>
        <input type="text" id="cc" name="cc" required><br>
        
        <label for="exp">Expiration Date (MM/YYYY):</label><br>
        <input type="text" id="exp" name="exp" required><br>

        <input type="submit" name="submit_order" value="Place Order">
    </form>


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
    $cardholder_name = $_POST['name'];
    $credit_card_number = $_POST['cc'];
    $expiration_date = $_POST['exp'];
    $total = $_SESSION['final'];
    $data = array(
        'vendor' => 'Project-H',
        'trans' => '907-987654321-233',
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
        echo "Authorization Number: " . $response['authorization'];
        
        // Update the ORDERS table
        $dsn = "mysql:host=courses;dbname=z1957829";

        try {
            $pdo = new PDO($dsn, "z1957829", "2004May16");
            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO ORDERS (OrderID, Address, Email, TotalPrice, TotalWeight, Datee, Status) VALUES (?, ?, ?, ?, ?, CURDATE(), ?)");
            $weight= $_SESSION['weight'];
            // Bind parameters
            $stmt->bindParam(1, $response['authorization']);
            $stmt->bindParam(2, $address);
            $stmt->bindParam(3, $email);
            $stmt->bindParam(4, $total);
            $stmt->bindParam(5, $weight); // You need to define $weight
            $stmt->bindValue(6, 'Placed');

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
                $updateProductSQL = "UPDATE Product SET Quantity = Quantity - ? WHERE ProductID = ?";
                $stmt = $pdo->prepare($updateProductSQL);
                $stmt->bindParam(1, $quantity);
                $stmt->bindParam(2, $product_id);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
