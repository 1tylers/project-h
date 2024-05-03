<?php
// Start the session
session_start();
// Include necessary files
include 'info.php';
// Name of the DB
$dsn = "mysql:host=courses;dbname=z1957829";
// Test the connection
try {
    $pdo = new PDO($dsn, "z1957829", "2004May16");
} catch (PDOexception $e) {
    echo "Connection to database failed: " . $e->getMessage();
}
// Get Input
$qty = "";
$prod_id = "";
if (isset($_POST["Add"])) {
    $qty = $_POST["quantity"];
    
    $product_id = $_POST["number"];
    // Adding to cart
    if (!isset($_SESSION["Add"])) {
        $_SESSION["Add"] = [];
    }
    
    //check quantity added
    $result = $pdo->query("SELECT Quantity FROM Inventory WHERE ProductID='$product_id'");
    
    //get value
    while ($row = $result->fetch(PDO::FETCH_ASSOC))
    {
      $qtyCheck = $row['Quantity'];
    }//end of while loop
    //check if quantity is valid
    if ($qtyCheck >= ($_SESSION["Add"][$product_id]+$qty))
    {
     $_SESSION["Add"][$product_id] += $qty;
      header("location:cart.php");
      exit(); 
    }//end of if statement
    else
    {
       echo "<script>alert('Error: Max Quantity for item is $qtyCheck');</script>";  
    }//end of else statement
}
?>
<!--- Group Project ~ CSCI466 																	--->
<!--- website.php																				--->
<!--- Aaron Arreola, Calvin Darley, Eli Gallegos, Jason Lan, Tyler Stenberg						--->
<!--- Purpose: 																					--->
<!--- This will be the main page of the website where different functions can be used and other --->
<!--- pages for the assignment can be reached here												--->
<html>
	<head>
		<title> Main Page - Project H</title>
   <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page - Project H</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        h1 {
            text-align: center;
            color: #343a40;
            font-size: 48px;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            justify-items: center;
        }
        .item {
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .item img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto 10px;
            border-radius: 8px;
        }
        .description {
            margin-bottom: 10px;
            color: #555;
        }
        .price {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .quantity-input {
            width: 50px;
            margin-right: 5px;
        }
        .add-to-cart-btn {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .add-to-cart-btn:hover {
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
    <h1>Welcome to Project H</h1>
    <div class="container">
        <?php
        $host1 = 'blitz.cs.niu.edu';
        $port1 = 3306;
        $dbname1 = 'csci467';
        $user1 = 'student';
        $password1 = 'student';

        $host2 = 'courses';
        $dbname2 = 'z1957829';
        $user2 = 'z1957829';
        $password2 = '2004May16';

        try {
            // Connect to the first MySQL database
            $pdo1 = new PDO("mysql:host=$host1;port=$port1;dbname=$dbname1", $user1, $password1);
            $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Connect to the second MySQL database
            $pdo2 = new PDO("mysql:host=$host2;dbname=$dbname2", $user2, $password2);
            $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fetch contents of the "parts" table from the first database
            $query1 = "SELECT * FROM parts";
            $stmt1 = $pdo1->query($query1);
            $parts = $stmt1->fetchAll(PDO::FETCH_ASSOC);

            foreach ($parts as $part) {
                // Fetch quantity from the second database
                $number = $part['number'];
                $query2 = "SELECT Quantity FROM Inventory WHERE ProductID = $number";
                $stmt2 = $pdo2->query($query2);
                $inventory = $stmt2->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="item">
                    <img src="<?php echo $part['pictureURL']; ?>" alt="Part Image">
                    <div class="quantity-available"><?php echo $inventory['Quantity']; ?> available</div>
                    <div class="description"><?php echo $part['description']; ?></div>
                    <div class="price">$<?php echo $part['price']; ?></div>
                    <div class="add-to-cart">
                        <form method="POST">
                            <input type="hidden" name="number" value="<?php echo $part['number']; ?>">
                            <input class="quantity-input" type="number" name="quantity" value="1" min="1">
                            <input class="add-to-cart-btn" type="submit" name="Add" value="Add to Cart">
                        </form>
                    </div>
                </div>
                <?php
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        ?>
    </div>
</body>
</html>
