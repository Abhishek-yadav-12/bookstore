<?php
session_start();
$nameErr = $emailErr = $genderErr = $addressErr = $icErr = $contactErr = $usernameErr = $passwordErr = "";
$name = $email = $gender = $address = $ic = $contact = $uname = $upassword = "";
$cID;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
        $nameErr = "Please enter your name";
    } else {
        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
            $name = "";
        } else {
            $name = $_POST['name'];

            if (empty($_POST["uname"])) {
                $usernameErr = "Please enter your Username";
                $uname = "";
            } else {
                $uname = $_POST['uname'];

                if (empty($_POST["upassword"])) {
                    $passwordErr = "Please enter your Password";
                    $upassword = "";
                } else {
                    $upassword = $_POST['upassword'];

                    if (empty($_POST["ic"])) {
                        $icErr = "Please enter your IC number";
                    } else {
                        if (!preg_match("/^[0-9 -]*$/", $ic)) {
                            $icErr = "Please enter a valid IC number";
                            $ic = "";
                        } else {
                            $ic = $_POST['ic'];

                            if (empty($_POST["email"])) {
                                $emailErr = "Please enter your email address";
                            } else {
                                // Validate email format
                                $email = $_POST['email'];
                                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $emailErr = "Invalid email format";
                                } else {
                                    // Check if email already exists
                                    $servername = "localhost";
                                    $username = "root";
                                    $password = "";
                                    $conn = new mysqli($servername, $username, $password, "bookstore");

                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }

                                    $sql = "SELECT * FROM users WHERE Email = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("s", $email);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        $emailErr = "Email already exists!";
                                    } else {
                                        // Insert into users table
                                        $hashedPassword = password_hash($upassword, PASSWORD_DEFAULT); // Hash the password
                                        $sql = "INSERT INTO users(UserName, Password, Email) VALUES(?, ?, ?)";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("sss", $uname, $hashedPassword, $email);
                                        $stmt->execute();

                                        // Retrieve UserID
                                        $sql = "SELECT UserID FROM users WHERE UserName = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("s", $uname);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $row = $result->fetch_assoc();
                                        $id = $row['UserID'];

                                        // Insert into customer table
                                        $sql = "INSERT INTO customer(CustomerName, CustomerPhone, CustomerIC, CustomerEmail, CustomerAddress, CustomerGender, UserID) 
                                                VALUES(?, ?, ?, ?, ?, ?, ?)";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("ssssssi", $name, $contact, $ic, $email, $address, $gender, $id);
                                        $stmt->execute();

                                        // Redirect to index.php
                                        header("Location:index.php");
                                        exit;
                                    }

                                    $conn->close();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<html>
<link rel="stylesheet" href="style.css">
<body>
<header>
<blockquote>
    <a href="index.php"><img src="image/logo.png"></a>
</blockquote>
</header>
<blockquote>
<div class="container">
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <h1>Register:</h1>
    Full Name:<br><input type="text" name="name" placeholder="Full Name">
    <span class="error" style="color: red; font-size: 0.8em;"><?php echo $nameErr;?></span><br><br>

    User Name:<br><input type="text" name="uname" placeholder="User Name">
    <span class="error" style="color: red; font-size: 0.8em;"><?php echo $usernameErr;?></span><br><br>

    New Password:<br><input type="password" name="upassword" placeholder="Password">
    <span class="error" style="color: red; font-size: 0.8em;"><?php echo $passwordErr;?></span><br><br>

    IC Number:<br><input type="text" name="ic" placeholder="xxxxxx-xx-xxxx">
    <span class="error" style="color: red; font-size: 0.8em;"><?php echo $icErr;?></span><br><br>

    E-mail:<br><input type="text" name="email" placeholder="example@email.com">
    <span class="error" style="color: red; font-size: 0.8em;"><?php echo $emailErr;?></span><br><br>

    Mobile Number:<br><input type="text" name="contact" placeholder="012-3456789">
    <span class="error" style="color: red; font-size: 0.8em;"><?php echo $contactErr;?></span><br><br>

    <label>Gender:</label><br>
    <input type="radio" name="gender" <?php if (isset($gender) && $gender == "Male") echo "checked";?> value="Male">Male
    <input type="radio" name="gender" <?php if (isset($gender) && $gender == "Female") echo "checked";?> value="Female">Female
    <span class="error" style="color: red; font-size: 0.8em;"><?php echo $genderErr;?></span><br><br>

    <label>Address:</label><br>
    <textarea name="address" cols="50" rows="5" placeholder="Address"></textarea>
    <span class="error" style="color: red; font-size: 0.8em;"><?php echo $addressErr;?></span><br><br>

    <input class="button" type="submit" name="submitButton" value="Submit">
    <input class="button" type="button" name="cancel" value="Cancel" onClick="window.location='index.php';" />
</form>
</div>
</blockquote>
</body>
</html>
