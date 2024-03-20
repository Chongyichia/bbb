<?php

$servername = "localhost";
$username = "myusername";
$password = "mypassword";
$dbname = "blah";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function create_table($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS login (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                IC INT(12),
                address TEXT,
                telephone INT(10),
                emergency_call INT(10),
                email VARCHAR(255)
            )";

    if ($conn->query($sql) === FALSE) {
        echo "Error creating table: " . $conn->error;
    }
}

function insert_data($conn, $data) {
    $sql = "INSERT INTO login (name, IC, address, telephone, emergency_call, email) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("issiis", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5]);

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $IC = $_POST["IC"];
    $address = $_POST["address"];
    $telephone = $_POST["telephone"];
    $emergency_call = $_POST["emergency_call"];
    $email = $_POST["email"];

    if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        echo "Invalid name";
    } elseif (!preg_match("/^[0-9]{12}$/", $IC)) {
        echo "Invalid IC number";
    } elseif (!preg_match("/^[0-9]{10}$/", $telephone)) {
        echo "Invalid telephone number";
    } elseif (!preg_match("/^[0-9]{10}$/", $emergency_call)) {
        echo "Invalid emergency telephone number";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email";
    } else {
        create_table($conn);
        insert_data($conn, [$name, $IC, $address, $telephone, $emergency_call, $email]);
        $conn->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Emergency App</title>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="name">Enter your name:</label><br>
        <input type="text" id="name" name="name"><br>
        <label for="IC">Enter your IC Number:</label><br>
        <input type="text" id="IC" name="IC"><br>
        <label for="address">Enter your address:</label><br>
        <input type="text" id="address" name="address"><br>
        <label for="telephone">Enter your Telephone Number:</label><br>
        <input type="text" id="telephone" name="telephone"><br>
        <label for="emergency_call">Enter your Emergency Telephone Number:</label><br>
        <input type="text" id="emergency_call" name="emergency_call"><br>
        <label for="email">Enter your email:</label><br>
        <input type="text" id="email" name="email"><br>
        <input type="submit" value="Log In">
        </form>

</body>
</html>