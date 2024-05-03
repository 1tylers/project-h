<!DOCTYPE html>
<html>
<head>
 <title>Admin Interface</title>

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

 <body>

 <h1>Admin Interface</h1>
  <?php

  	session_start();

  	if(!isset($_SESSION["username"]))
  	{
  		header("location:login.php");
  	}

    // Name of the DB
    $dsn = "mysql:host=courses;dbname=z1957829";

    // Test the connection
    try {
        $pdo = new PDO($dsn, "z1957829", "2004May16");
    } catch (PDOexception $e) {
        echo "Connection to database failed: " . $e->getMessage();
    }
  ?>

   <form method="POST">

     <label for="bracket">Update Bracket: </label>
       <select name="bracket" id="bracket">
         <option value="Light">Light</option>
         <option value="Medium">Medium</option>
         <option value="Heavy">Heavy</option>
       </select><br><br>


       <label for="Price">New Price:</label>
        <input type="text" value="" name="Price">
       <input type="submit" value="Submit">
    </form><br>

  <?php
    if(isset($_POST['bracket'], $_POST['Price']))
    {
      $bracket = $_POST['bracket'];
      $price = $_POST['Price'];

      if($Price < 0)
      {
        echo "Error: Invalid price";
      }
      else
      {
        $sql = "UPDATE Brackets SET Price = '$price' WHERE Bracket = '$bracket';";

        $result = $pdo->exec($sql);

        if(!$result)
	    {
	      die('Error: Bracket not updated.' . $pdo->errorInfo()[2]);
	    }

	    echo "'$bracket' price updated to '$price'";
	  }
    }

    $query = "SELECT Bracket, Price FROM Brackets;";
    $brackets = $pdo->query($query);
  ?>

  <table border = 1>
  <tr>
    <th>Bracket</th>
    <th>Price</th>
  </tr>

  <?php
    while($row = $brackets->fetch())
    {
      echo "<tr>";
      echo "<td>{$row['Bracket']}</td>";
      echo "<td>{$row['Price']}</td>";
      echo "</tr>";
    }
  ?>
  </table>

  <h2>Order Lookup</h2>

  <form method="POST">

    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date">
    <br><br>

    <label for="end_date">End Date:</label>
    <input type="date" name="end_date">
    <br><br>

    <label for="status">Status:</label>
    <select name="status">
        <option value="">Any</option>
        <option value="Processing">Processing</option>
        <option value="Shipped">Shipped</option>
        <option value="Delivered">Delivered</option>
    </select>
    <br><br>

    <label for="min_price">Minimum Price:</label>
    <input type="number" name="min_price" step="0.01">
    <br><br>

    <label for="max_price">Maximum Price:</label>
    <input type="number" name="max_price" step="0.01">
    <br><br>

    <input type="submit" name="search_orders" value="Search Orders">
  </form><br>

  <?php
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
      $start_date = $_POST['start_date'];
	  $end_date = $_POST['end_date'];
	  $status = $_POST['status'];
	  $min_price = $_POST['min_price'];
	  $max_price = $_POST['max_price'];

	  $sql = "SELECT * FROM Orders WHERE 1=1";

	  if (!empty($start_date))
	  {
        $sql .= " AND Datee >= '$start_date'";
	  }

	  if (!empty($end_date))
	  {
        $sql .= " AND Datee <= '$end_date'";
	  }

	  if (!empty($status))
	  {
        $sql .= " AND Status = '$status'";
	  }

	  if (!empty($min_price))
	  {
	    if($min_price < 0)
	    {
	      echo "Error: Invalid minimum price";
	    }
	    else
	    {
          $sql .= " AND TotalPrice >= $min_price";
        }
	  }

	  if (!empty($max_price))
	  {
        if($max_price < 0)
        {
          echo "Error: Invalid maximum price";
        }
        else
        {
          $sql .= " AND TotalPrice <= $max_price";
        }
	  }

	  $result = $pdo->query($sql);

	  if($result->rowCount() == 0)
	  {
	    echo "NO ORDERS FOUND";
	  }
	  else
	  {
        echo "<table border='1'>";
        echo "<tr>";
	    echo "<th>OrderID</th>";
	    echo "<th>Address</th>";
	    echo "<th>Email</th>";
	    echo "<th>TotalPrice</th>";
	    echo "<th>TotalWeight</th>";
	    echo "<th>Date</th>";
	    echo "<th>Status</th>";
	    echo "</tr>";

        if($result)
        {
          while($row = $result->fetch())
          {
            echo "<tr>";
            echo "<td>{$row['OrderID']}</td>";
            echo "<td>{$row['Address']}</td>";
            echo "<td>{$row['Email']}</td>";
            echo "<td>{$row['TotalPrice']}</td>";
            echo "<td>{$row['TotalWeight']}</td>";
            echo "<td>{$row['Datee']}</td>";
            echo "<td>{$row['Status']}</td>";
            echo "</tr>";
          }
        }
      }
    }
  ?>
  </table><br><br>

	<a href="logout.php" class="logout">Logout</a>

 </body>


</html>

