<html>
<body style="font-family:Arial; margin: 0 auto; background-color: #f2f2f2;">
<header>
<blockquote>
	<img src="image/logo.png">
	<input class="hi" style="float: right; margin: 2%;" type="button" name="cancel" value="Home" onClick="window.location='index.php';" />
</blockquote>
</header>
<?php
session_start();

if(isset($_SESSION['id'])){
	$servername = "localhost";
	$username = "root";
	$password = "";

	$conn = new mysqli($servername, $username, $password); 

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	$sql = "USE bookstore";
	$conn->query($sql);

	$sql = "SELECT CustomerID FROM customer WHERE UserID = ".$_SESSION['id'];

	$result = $conn->query($sql);
	if ($result && $row = $result->fetch_assoc()) {
		$cID = $row['CustomerID'];
	}

	$sql = "UPDATE cart SET CustomerID = ".$cID." WHERE 1";
	$conn->query($sql);

	$sql = "SELECT * FROM cart WHERE CustomerID = ".$cID."";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc()){
		$sql = "INSERT INTO `order`(CustomerID, BookID, DatePurchase, Quantity, TotalPrice, Status) 
		VALUES(".$row['CustomerID'].", '".$row['BookID']
		."', CURRENT_TIME, ".$row['Quantity'].", ".$row['TotalPrice'].", 'N')";
		$conn->query($sql);
	}
	$sql = "DELETE FROM cart WHERE CustomerID = ".$cID."";
	$conn->query($sql);

	$sql = "SELECT customer.CustomerName, customer.CustomerIC, customer.CustomerGender, customer.CustomerAddress, customer.CustomerEmail, customer.CustomerPhone, book.BookTitle, book.Price, book.Image, `order`.`DatePurchase`, `order`.`Quantity`, `order`.`TotalPrice`
		FROM customer, book, `order`
		WHERE `order`.`CustomerID` = customer.CustomerID AND `order`.`BookID` = book.BookID AND `order`.`Status` = 'N' AND `order`.`CustomerID` = ".$cID."";
	$result = $conn->query($sql);
	echo '<div class="container">';
	echo '<blockquote>';
?>
<input class="button" style="float: right;" type="button" name="cancel" value="Continue Shopping" onClick="window.location='index.php';" />
<?php
	echo '<h2 style="color: #000;">Order Successful</h2>';
	echo "<table style='width:100%'>";
	echo "<tr><th>Order Summary</th>";
	echo "<th></th></tr>";
	$row = $result->fetch_assoc();
	echo "<tr><td>Name: </td><td>".$row['CustomerName']."</td></tr>";
	echo "<tr><td>No.Number: </td><td>".$row['CustomerIC']."</td></tr>";
	echo "<tr><td>E-mail: </td><td>".$row['CustomerEmail']."</td></tr>";
	echo "<tr><td>Mobile Number: </td><td>".$row['CustomerPhone']."</td></tr>";
	echo "<tr><td>Gender: </td><td>".$row['CustomerGender']."</td></tr>";
	echo "<tr><td>Address: </td><td>".$row['CustomerAddress']."</td></tr>";
	echo "<tr><td>Date: </td><td>".$row['DatePurchase']."</td></tr>";
	echo "</blockquote>";

	$sql = "SELECT customer.CustomerName, customer.CustomerIC, customer.CustomerGender, customer.CustomerAddress, customer.CustomerEmail, customer.CustomerPhone, book.BookTitle, book.Price, book.Image, `order`.`DatePurchase`, `order`.`Quantity`, `order`.`TotalPrice`
		FROM customer, book, `order`
		WHERE `order`.`CustomerID` = customer.CustomerID AND `order`.`BookID` = book.BookID AND `order`.`Status` = 'N' AND `order`.`CustomerID` = ".$cID."";
	$result = $conn->query($sql);
	$total = 0;
	while($row = $result->fetch_assoc()){
		echo "<tr><td style='border-top: 2px solid #ccc;'>";
		echo '<img src="'.$row["Image"].'"width="20%"></td><td style="border-top: 2px solid #ccc;">';
    	echo $row['BookTitle']."<br>RM".$row['Price']."<br>";
    	echo "Quantity: ".$row['Quantity']."<br>";
    	echo "</td></tr>";
    	$total += $row['TotalPrice'];
	}
	echo "<tr><td style='background-color: #ccc;'></td><td style='text-align: right;background-color: #ccc;''>Total Price: <b>RM".$total."</b></td></tr>";
	echo "</table>";
	echo "</div>";

	$sql = "UPDATE `order` SET Status = 'y' WHERE CustomerID = ".$cID."";
	$conn->query($sql);
}

$nameErr = $emailErr = $genderErr = $addressErr = $icErr = $contactErr = "";
$name = $email = $gender = $address = $ic = $contact = "";
$cID;

if(isset($_POST['submitButton'])){
	if (empty($_POST["name"])) {
		$nameErr = "Please enter your name";
	}else{
		if (!preg_match("/^[a-zA-Z ]*$/", $name)){
			$nameErr = "Only letters and white space allowed";
			$name = "";
		}else{
			$name = $_POST['name'];
			if (empty($_POST["ic"])){
				$icErr = "Please enter your IC number";
			}else{
				if(!preg_match("/^[0-9 -]*$/", $ic)){
					$icErr = "Please enter a valid IC number";
					$ic = "";
				}else{
					$ic = $_POST['ic'];
					if (empty($_POST["email"])){
						$emailErr = "Please enter your email address";
					}else{
						if (filter_var($email, FILTER_VALIDATE_EMAIL)){
							$emailErr = "Invalid email format";
							$email = "";
						}else{
							$email = $_POST['email'];
							if (empty($_POST["contact"])){
								$contactErr = "Please enter your phone number";
							}else{
								if(!preg_match("/^[0-9 -]*$/", $contact)){
									$contactErr = "Please enter a valid phone number";
									$contact = "";
								}else{
									$contact = $_POST['contact'];
									if (empty($_POST["gender"])){
										$genderErr = "* Gender is required!";
										$gender = "";
									}else{
										$gender = $_POST['gender'];
										if (empty($_POST["address"])){
											$addressErr = "Please enter your address";
											$address = "";
										}else{
											$address = $_POST['address'];

											$servername = "localhost";
											$username = "root";
											$password = "";

											$conn = new mysqli($servername, $username, $password); 

											if ($conn->connect_error) {
											    die("Connection failed: " . $conn->connect_error);
											} 

											$sql = "USE bookstore";
											$conn->query($sql);

											$sql = "INSERT INTO customer(CustomerName, CustomerPhone, CustomerIC, CustomerEmail, CustomerAddress, CustomerGender) 
											VALUES('".$name."', '".$contact."', '".$ic."', '".$email."', '".$address."', '".$gender."')";
											$conn->query($sql);
 
											$sql = "SELECT CustomerID from customer WHERE CustomerName = '".$name."' AND CustomerIC = '".$ic."'";
											$result = $conn->query($sql);
											if ($result && $row = $result->fetch_assoc()) {
												$cID = $row['CustomerID'];
											}

											$sql = "UPDATE cart SET CustomerID = ".$cID." WHERE 1";
											$conn->query($sql);

											$sql = "SELECT * FROM cart WHERE CustomerID = ".$cID."";
											$result = $conn->query($sql);
											while($row = $result->fetch_assoc()){
												$sql = "INSERT INTO `order`(CustomerID, BookID, DatePurchase, Quantity, TotalPrice, Status) 
												VALUES(".$row['CustomerID'].", '".$row['BookID']."', CURRENT_TIME, ".$row['Quantity'].", ".$row['TotalPrice'].", 'N')";
												$conn->query($sql);
											}
											$sql = "DELETE FROM cart WHERE CustomerID = ".$cID."";
											$conn->query($sql);

											$sql = "UPDATE `order` SET Status = 'y' WHERE CustomerID = ".$cID."";
											$conn->query($sql);
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

?>
</body>
</html>
