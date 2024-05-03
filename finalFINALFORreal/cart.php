<!DOCTYPE html>
<?php
// Start the session
session_start();
// Include necessary files
include 'info.php';
// Name of the DB
$host1 = 'blitz.cs.niu.edu';
$port1 = 3306;
$dbname1 = 'csci467';
$user1 = 'student';
$password1 = 'student';

$host2 = 'courses';
$dbname2 = 'z1968549';
$user2 = 'z1968549';
$password2 = '2004Jul30';

// Test the connection
try {
    $pdo1 = new PDO("mysql:host=$host1;port=$port1;dbname=$dbname1", $user1, $password1);
    $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Connect to the second MySQL database
    $pdo2 = new PDO("mysql:host=$host2;dbname=$dbname2", $user2, $password2);
    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Connection to the database failed: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the delete button is clicked
    if (isset($_POST['delete'])) {
        $deleteProductId = $_POST['delete'];
        // Remove the selected product from the session
        unset($_SESSION['Add'][$deleteProductId]);
    } elseif (isset($_POST['quantity'])) {
        // Update the quantity in the session
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            // Validate quantity (you might want to add additional validation)
            $_SESSION['Add'][$product_id] = $quantity;
        }
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" type="text/css" href="hstyles.css">
    <style>
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
body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
    </style
    
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="parts.php">Main Page</a>
        <a href="cart.php">Cart</a>
        <a href="login.php">Employee Login</a>
    </div>
<h1>Shopping Cart</h1>
<form action="cart.php" method="post">
    <?php
    $total = 0;
    $items = 0;
    echo "<table>";
    echo "<tr>";
    echo "<th>Product Name </th> ";
    echo "<th>Quantity </th> ";
    echo "<th>Price </th>";
    echo "<th>Total </th>";
    echo "</tr>";
    if (!isset($_SESSION["Add"])) {
        $_SESSION["Add"] = [];
    }
    foreach ($_SESSION['Add'] as $product_id => $quantity) {
        $query1 = "SELECT description, price,weight FROM parts WHERE number=$product_id";
        $stmt1 = $pdo1->query($query1);
        $parts = $stmt1->fetch(PDO::FETCH_ASSOC);
        $query2 = "SELECT Quantity FROM Inventory WHERE ProductID = $product_id";
        $stmt2 = $pdo2->query($query2);
        $inventory = $stmt2->fetch(PDO::FETCH_ASSOC);

        $availableQuantity = $inventory['Quantity'];
        $description = $parts['description'];
        $price = $parts['price'];
        $weight= $parts['weight'];
        
        $totalItem = $quantity * $price;
        $itemweight=$quantity * $weight;
        $tweight+=$itemweight;
        $total += $totalItem;
        $items += $quantity;
        echo "<tr>";
        echo "<td>$description</td> ";
        echo "<td><input type='number' name='quantity[$product_id]' value='$quantity' min='1' max='$availableQuantity'></td>";
        echo "<td>$price </td>";
        echo "<td>$totalItem </td>";
        echo "<td><button type='submit' name='delete' value='$product_id'>X</button></td>";
        echo "</tr>";
    }
    $_SESSION['total'] = $total;
    $_SESSION['items'] = $items;
    $_SESSION['weight'] = $tweight;
    echo "<tr>";
    echo "<td> Total:</td>";
    echo "<td> $total</td>";
    echo "<td> Total Items:</td>";
    echo "<td> $items</td>";
    echo "</tr>";
    echo "</table>";
    ?>
    <input type='submit' value='Update Quantities'>
</form>
<?php
    // Display checkout button only if the cart is not empty
    if ($_SESSION['items'] > 0) {
        echo "<form action='hcheckout.php' method='post'>";
        echo "<input type='submit' value='Checkout'>";
        echo "</form>";
    } else {
        echo "<p>Your cart is empty. Please add items to proceed to checkout.</p>";
    }
    ?>
<form action="parts.php" method="get">
    <input type='submit' value='Continue Shopping'>
</form>
</body>
</html>
