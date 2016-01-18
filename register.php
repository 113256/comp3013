<!DOCTYPE html>
<?php 
include('includes/head.php');
include('includes/connect.php');
error_reporting(E_ALL);

if(isset($_POST['register'])){
	$userName = $_POST['userName'];
	$fName = $_POST['fName'];
	$lName = $_POST['lName'];
	$password = $_POST['password'];
	$email = $_POST['email'];

	$insertQuery = "INSERT INTO `users` (`userName`, `fName`, `lName`, `password`, `email`) VALUES ('$userName','$fName', '$lName', '$password', '$email')";
	mysqli_query($conn, $insertQuery) or die(mysqli_error($conn));

}

?>


<h1 style = "text-align: center">Register</h1>



<div class = "container">

	<?php
		if(isset($_POST['register'])){
			echo "<p>Registered!</p>";
		}
	 ?>

	<form role = "form" method = "post" action = "register.php">

		<div class = "form-group">
			<label>Username</label>
			<input type = "text" class = "form-control" name = "userName" required>
		</div>

		<div class = "form-group">
			<label>First name</label>
			<input type = "text" class = "form-control" name = "fName" required>
		</div>

		<div class = "form-group">
			<label>Last name</label>
			<input type = "text" class = "form-control" name = "lName" required>
		</div>

		<div class = "form-group">
			<label>Password</label>
			<input type = "password" class = "form-control" name = "password" required>
		</div>

		<div class = "form-group">
			<label>Email</label>
			<input type = "email" class = "form-control" name = "email" required>
		</div>

		<button type = "submit" class = "btn btn-default" name = "register">Submit</button>

	</form>
</div>






</html>