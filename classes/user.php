<?php 

//user manager class, not individual user
class User{
	private $conn;

	function __construct($conn){
		$this->conn = $conn;
	}
													
	public function login($userName, $password)
    {
      	$selectQuery = "SELECT * FROM `users` WHERE `userName`='$userName'";
		$userResult = mysqli_query($this->conn, $selectQuery);
		$row = mysqli_fetch_array($userResult);
		//echo $row['password'];
		//echo password_verify("jo65tt", '$2y$10$WVSty3ywE3muf');
		if(password_verify($password, $row['password'])){
			return true;
		} else {
			return false;
		}
			//print_r($row);
   }

   //create new user
   public function register($userName, $fName,$lName, $password,$email)
    {
	    

		$password = password_hash($password, PASSWORD_DEFAULT);

		$insertQuery = "INSERT INTO `users` (`userName`, `fName`, `lName`, `password`, `email`) VALUES ('$userName','$fName', '$lName', '$password', '$email')";
		mysqli_query($this->conn, $insertQuery) or die(mysqli_error($this->conn));
    }

    public function updatePayment($cardName, $cardNumber, $cardExpiry, $userId){
    	$updateQuery = "INSERT INTO `payment` (`userId`, `cardName`, `cardNumber`, `cardExpiry`) VALUES ('$userId', '$cardName', '$cardNumber', '$cardExpiry') ON DUPLICATE KEY UPDATE `cardName`='$cardName',`cardNumber`='$cardNumber',`cardExpiry`='$cardExpiry' ";
    	
    	if (mysqli_query($this->conn, $updateQuery)){
    		return true;
    	} else {
    		return false;
    	}
    }
}


?>