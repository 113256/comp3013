
<?php 
include('includes/head.php');
include('includes/connect.php');
error_reporting(E_ALL);

if(isset($_POST['login'])){


	$userName = $_POST['userName'];
	$password = $_POST['password'];

	$selectQuery = "SELECT * FROM `users` WHERE `userName`='$userName'";
	$userResult = mysqli_query($conn, $selectQuery);
	$row = mysqli_fetch_array($userResult);
	//print_r($row);
	session_start();
	//this works because $row has $row['userName'] = $userName, $row['fName'] etc... since $row is an array.
	//so this sets $_SESSION['user']['userName'] = $userName and so on 
	$_SESSION['user'] = $row; 

	header("Location: dashboard.php"); 
    die("Redirecting to: dashboard.php"); 
}

?>

<!DOCTYPE html>
<h1 style = "text-align: center">Login</h1>



<div class = "container">

	

	<form role = "form" method = "post" action = "login.php">
		<div class = "form-group">
			<label>Username</label>
			<input type = "text" class = "form-control" name = "userName" required>
		</div>
		<div class = "form-group">
			<label>Password</label>
			<input type = "password" class = "form-control" name = "password" required>
		</div>
	

		<button type = "submit" class = "btn btn-default" name = "login">Submit</button>

	</form>
</div>






</html>