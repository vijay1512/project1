<?php
$host = 'localhost';
$dbname = 'netflix';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");

    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            age INT
        )
    ";
    $pdo->exec($createTableSQL);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['email'], $_POST['age'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $age = $_POST['age'];

        $insertSQL = "INSERT INTO users (name, email, age) VALUES (:name, :email, :age)";
        $stmt = $pdo->prepare($insertSQL);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':age', $age);

        try {
            $stmt->execute();
            echo "New record created successfully.<br>";
        } catch (PDOException $e) {
            echo "Error inserting record: " . $e->getMessage() . "<br>";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['retrieve'])) {
        $selectSQL = "SELECT * FROM users";
        $stmt = $pdo->prepare($selectSQL);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "Data from 'users' table:<br>";
            foreach ($results as $row) {
                echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Email: " . $row['email'] . " | Age: " . $row['age'] . "<br>";
            }
        } catch (PDOException $e) {
            echo "Error retrieving records: " . $e->getMessage() . "<br>";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_id'], $_POST['update_name'], $_POST['update_email'], $_POST['update_age'])) {
        $updateId = $_POST['update_id'];
        $updateName = $_POST['update_name'];
        $updateEmail = $_POST['update_email'];
        $updateAge = $_POST['update_age'];

        $updateSQL = "UPDATE users SET name = :name, email = :email, age = :age WHERE id = :id";
        $stmt = $pdo->prepare($updateSQL);
        $stmt->bindParam(':id', $updateId);
        $stmt->bindParam(':name', $updateName);
        $stmt->bindParam(':email', $updateEmail);
        $stmt->bindParam(':age', $updateAge);

        try {
            $stmt->execute();
            echo "Record updated successfully.<br>";
        } catch (PDOException $e) {
            echo "Error updating record: " . $e->getMessage() . "<br>";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<h2>Insert New User</h2>
<form method="POST">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Age: <input type="number" name="age" required><br>
    <input type="submit" value="Insert">
</form>

<h2>Update User</h2>
<form method="POST">
    ID: <input type="number" name="update_id" required><br>
    New Name: <input type="text" name="update_name" required><br>
    New Email: <input type="email" name="update_email" required><br>
    New Age: <input type="number" name="update_age" required><br>
    <input type="submit" value="Update">
</form>

<h2>Retrieve All Users</h2>
<form method="GET">
    <input type="hidden" name="retrieve" value="true">
    <input type="submit" value="Retrieve">
</form>