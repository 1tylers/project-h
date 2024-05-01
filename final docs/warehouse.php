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
	</head>

	<h1> Warehouse Interface </h1>

	<body>
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
				<input type="submit" value="Print Invoice">
		</form>
		<!--- Validate Input --->
		<?php
			//variables
			$orderNum = "";

			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				//retrive the value
				$orderNum = $_POST["orderNum"]; // what part they want to see
			} //end of if statement
		?>

		<!--- Execute Query for Shipping Label--->
		<?php
			if (empty($orderNum)) //missing parameter
			{
				echo "Parameters not recieved <br>";
			} //end of if statement
			else
			{
				//print invoice label
				echo "<h2> Invoice for OrderID: $orderNum </h2>";

				//query to get results
				$query = "SELECT Email, TotalPrice, Datee FROM Orders WHERE OrderID='$orderNum'";
				$stmt = $pdo->query($query);
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

                if(!$result)
                {
            		echo "No data found for $ordernum";
                }

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

				//print shipping label
				echo "<h2> Shipping Label for OrderID: $orderNum </h2>";

				//query to get results
				$query2 = "SELECT ProductID, Quantity FROM ProductStored WHERE OrderID='$orderNum'";
				$stmt2 = $pdo->query($query2);
				$result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

			
				//query to get description
				$query3 = "SELECT DESCRIPTION FROM parts WHERE number='{$result2['ProductID']}'";
				$stmt3 = $pdo2->query($query3);
				$result3 = $stmt3->fetch(PDO::FETCH_ASSOC);	
					
				
				if(!$result2)
                {
            		echo "No data found for $ordernum";
                }

				//start table
				echo "<table>";

				//print out the headers of the file
				echo "<tr>";
					echo "<th> PART NUMBER </th>";
					//echo "<th> DESCRIPTION </th>";
					echo "<th> QUANTITY </th>";
				echo "</tr>";

				foreach($result2 as $row2)
				{
					//create a row
					echo "<tr>";	
						//fill row with data
						echo "<td> {$row2['ProductID']} </td>";
						//echo "<td> {$result3['description']} </td>";					
						echo "<td> {$row2['Quantity']} </td>";
					//close the row
					echo "</tr>";
				}//end of for each statement

				//close the table
				echo "</table>";
			} //end of else statement
		?>
	</body>
</html>
