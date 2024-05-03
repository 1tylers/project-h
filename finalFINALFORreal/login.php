<?php

$host="courses";
$user="z1957829";
$password="2004May16";
$db="z1957829";

session_start();

$data=mysqli_connect($host,$user,$password,$db);

if($data===false)
{
	die("connection error");
}


if($_SERVER["REQUEST_METHOD"]=="POST")
{
	$username=$_POST["username"];
	$password=$_POST["password"];

	$condition = false;


	$sql="select * from login where username='".$username."' AND password='".$password."'";

	$result=mysqli_query($data,$sql);
	$row=mysqli_fetch_array($result);


	if($row["usetype"]=="warehouse")
	{
		$_SESSION["username"]=$username;

		header("location:warehouse.php");
	}
	elseif($row["usetype"]=="admin")
	{
		$_SESSION["username"]=$username;

		header("location:admin.php");
	}
	elseif($row["usetype"]=="recievingdesk")
	{
		$_SESSION["username"]=$username;

		header("location:receive.php");
	}
	else
	{
		$condition = true;
	}
}

?>




<!DOCTYPE html>
<html>
<head>
	<title>Employee Login</title>

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

		h3 {
			text-align: center;
			color: #343a40;
			font-size: 14px;
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
        input[type="email"],
        input[type="password"] {
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

	        .loginbg {
	        	width: 500px;
	        	height: 320px;
	        	background-color: #B6C0D0;
	        	border-radius: 10px;
	        	
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

	<center>

		<br><br><br><br>

		<h1>Employee Login</h1>

		<div class="loginbg">

			<br><br>

			<form action="#" method="POST">

				<div>
					<label>Username</label>
					<input type="text" name="username" required>
				</div>

				<br><br>

				<div>
					<label>Password</label>
					<input type="password" name="password" required>
				</div>

				<br><br>

				<div>
					<input type="submit" value="Login">
				</div>

			</form>

		</div>

	</center>

	<?php
		if ($condition)
		{
			echo "<h3>Username or Password Incorrect</h3>";
		}
	?>

</body>
</html>
