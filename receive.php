<!DOCTYPE html>
<html>
 <head>
   <title>Receiving Desk</title>
 </head>

 <h1>Receiving Interface</h1>

 <body>
   <?php
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

      $sql = "SELECT number, description FROM parts WHERE description LIKE '$description'";

      $stmt = $pdo->query($sql);
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>

   <form method="POST">

     Enter Product ID: 
     <input type="text" value="" name="productID">
     <input type="submit" value="Receive Product">

   </form><br><br>

   <form method="POST">
     <label for="search">Product Lookup</label>
     <input type="text" name="search" id="search">
     <input type="submit" value ="Search">

   </form>

   <?php
    if (isset($result))
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
 </body>


</html>
