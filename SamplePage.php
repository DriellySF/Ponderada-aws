<?php include "inc/dbinfo.inc"; ?>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="text"], input[type="submit"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .form-container {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<h1>Sample Page</h1>
<?php

  // Connect to MySQL and select the database.
  $connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

  if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
  }

  // Ensure that the tables exist.
  VerifyTables($connection, DB_DATABASE);

  // If input fields are populated, add or update a row.
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['NAME']) && !empty($_POST['ADDRESS'])) {
        AddEmployee($connection, $_POST['NAME'], $_POST['ADDRESS']);
    }
    if (!empty($_POST['SKU']) && !empty($_POST['LOJA']) && !empty($_POST['CIDADE'])) {
        AddProduct($connection, $_POST['SKU'], $_POST['LOJA'], $_POST['CIDADE']);
    }
  }
?>

<!-- Employee Form -->
<div class="form-container">
    <h2>Add Employee</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
      <table>
        <tr>
          <td>NAME</td>
          <td>ADDRESS</td>
        </tr>
        <tr>
          <td><input type="text" name="NAME" maxlength="45" size="30" /></td>
          <td><input type="text" name="ADDRESS" maxlength="90" size="60" /></td>
          <td><input type="submit" value="Add Employee" /></td>
        </tr>
      </table>
    </form>
</div>

<!-- Product Form -->
<div class="form-container">
    <h2>Add Product</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
      <table>
        <tr>
          <td>SKU</td>
          <td>STORE</td>
          <td>CITY</td>
        </tr>
        <tr>
          <td><input type="text" name="SKU" maxlength="45" size="30" /></td>
          <td><input type="text" name="LOJA" maxlength="45" size="30" /></td>
          <td><input type="text" name="CIDADE" maxlength="45" size="30" /></td>
          <td><input type="submit" value="Add Product" /></td>
        </tr>
      </table>
    </form>
</div>

<!-- Display Employees -->
<h2>Employees</h2>
<table>
  <tr>
    <th>ID</th>
    <th>NAME</th>
    <th>ADDRESS</th>
  </tr>

<?php
$result = $connection->query("SELECT * FROM EMPLOYEES");

while($query_data = $result->fetch_assoc()) {
  echo "<tr>";
  echo "<td>{$query_data['ID']}</td>",
       "<td>{$query_data['NAME']}</td>",
       "<td>{$query_data['ADDRESS']}</td>";
  echo "</tr>";
}

$result->free();
?>

</table>

<!-- Display Products -->
<h2>Products</h2>
<table>
  <tr>
    <th>ID</th>
    <th>SKU</th>
    <th>STORE</th>
    <th>CITY</th>
  </tr>

<?php
$result = $connection->query("SELECT * FROM produto");

while($query_data = $result->fetch_assoc()) {
  echo "<tr>";
  echo "<td>{$query_data['id']}</td>",
       "<td>{$query_data['sku']}</td>",
       "<td>{$query_data['loja']}</td>",
       "<td>{$query_data['cidade']}</td>";
  echo "</tr>";
}

$result->free();
$connection->close();
?>

</table>

</body>
</html>

<?php

// Add an employee to the table.
function AddEmployee($connection, $name, $address) {
   $stmt = $connection->prepare("INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES (?, ?)");
   $stmt->bind_param("ss", $name, $address);

   if(!$stmt->execute()) {
     echo("<p>Error adding employee data.</p>");
   }

   $stmt->close();
}

// Add a product to the table.
function AddProduct($connection, $sku, $store, $city) {
   $stmt = $connection->prepare("INSERT INTO produto (sku, loja, cidade) VALUES (?, ?, ?)");
   $stmt->bind_param("sss", $sku, $store, $city);

   if(!$stmt->execute()) {
     echo("<p>Error adding product data.</p>");
   }

   $stmt->close();
}

// Check whether the tables exist and, if not, create them.
function VerifyTables($connection, $dbName) {
  if (!TableExists("EMPLOYEES", $connection, $dbName)) {
     $query = "CREATE TABLE EMPLOYEES (
         ID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90)
       )";

     if(!$connection->query($query)) {
       echo("<p>Error creating EMPLOYEES table.</p>");
     }
  }

  if (!TableExists("produto", $connection, $dbName)) {
     $query = "CREATE TABLE produto (
         id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         sku VARCHAR(45),
         loja VARCHAR(45),
         cidade VARCHAR(45)
       )";

     if(!$connection->query($query)) {
       echo("<p>Error creating produto table.</p>");
     }
  }
}

// Check for the existence of a table.
function TableExists($tableName, $connection, $dbName) {
  $t = $connection->real_escape_string($tableName);
  $d = $connection->real_escape_string($dbName);

  $checktable = $connection->query(
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  return $checktable->num_rows > 0;
}
?>
