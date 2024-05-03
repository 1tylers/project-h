<!DOCTYPE html>
<html>
 <head>
   <title>Receiving Desk</title>

   <style>
		.logout {
			display: inline-block;
			padding: 10px 20px;
			text-decoration: none;
			background-color: #C35048;
			color: white;
			border-radius: 5px;
		}

		body {
			font-family: Arial, sans-serif;
		}
   </style>
 </head>

 <h1>Receiving Interface</h1>

 <body>
   <?php

	session_start();

	if(!isset($_SESSION["username"]))
	{
		header("location:login.php");
	}

    // Name of the DB
    $dsn = "mysql:host=courses;dbname=z1957829";

    $host1 = 'blitz.cs.niu.edu';
    $port1 = 3306;
    $dbname1 = 'csci467';
    $user1 = 'student';
    $password1 = 'student';

    // Test the connection
    try {
        $pdo = new PDO($dsn, "z1957829", "2004May16");
        $pdo1 = new PDO("mysql:host=$host1;port=$port1;dbname=$dbname1", $user1, $password1);
        $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (PDOexception $e) {
        echo "Connection to database failed: " . $e->getMessage();
    }

    if(isset($_POST['search']))
    {
      $description = $_POST['search'];

      $sql = "SELECT number, description FROM parts WHERE description LIKE '%$description%'";


      $stmt = $pdo1->query($sql);
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if(isset($_POST['productID']) && isset($_POST['quantity']))
    {
      $productID = $_POST['productID'];
      $quantity = $_POST['quantity'];

      if($quantity < 0)
      {
        echo 'ERROR: INVALID QUANTITY';
      }
      else
      {
        $sql = "UPDATE Inventory SET Quantity = Quantity + '$quantity' WHERE ProductID = '$productID'";

        $result1 = $pdo->exec($sql);

        if(!$result1)
        {
          die('Error: Product not found.' . $pdo->errorInfo()[2]);
        }

        $sql = "SELECT Quantity FROM Inventory WHERE ProductID = '$productID'";

        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "PRODUCT ID '$productID' QUANTITY UPDATED TO '{$result['Quantity']}'";
      }
    }
    ?>

   <form method="POST">

     Product ID:
     <input type="text" value="" name="productID"><br>
     Quantity received:
     <input type="text" value="" name="quantity"><br>


     <input type="submit" value="Receive Product">

   </form><br><br>

   <form method="POST">
     <label for="search">Product Lookup</label>
     <input type="text" name="search" id="search">
     <input type="submit" value ="Search">

   </form><br>

   <?php
    if (isset($result) && !empty($result))
    {
      echo"<table border = 1>";
      echo"<tr>";
      echo"<th>number</th>";
      echo"<th>description</th>";
      echo"</tr>";

      foreach($result as $row)
      {
        echo "<tr>";
        echo "<td>{$row['number']}</td>";
        echo "<td>{$row['description']}</td>";
        echo "</tr>";
      }

      echo"</table>";
    }
    elseif(isset($result))
    {
      echo "No products found.";
    }
  ?>

	<a href="logout.php" class="logout">Logout</a>

 </body>


</html>

