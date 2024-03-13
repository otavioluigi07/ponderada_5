<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Sample page</h1>
<?php
  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();
  $database = mysqli_select_db($connection, DB_DATABASE);
  VerifyEmployeesTable($connection, DB_DATABASE);
  $employee_name = htmlentities($_POST['NAME']);
  $employee_address = htmlentities($_POST['ADDRESS']);
  $employee_age = htmlentities($_POST['AGE']);
  $employee_cpf = htmlentities($_POST['CPF']); 
  $employee_email = htmlentities($_POST['EMAIL']);
  if (strlen($employee_name) || strlen($employee_address) || strlen($employee_age) || strlen($employee_cpf) || strlen($employee_email)) {
    AddEmployee($connection, $employee_name, $employee_address, $employee_age, $employee_cpf, $employee_email);
  }
?>
<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NAME</td>
      <td>ADDRESS</td>
      <td>AGE</td>
      <td>CPF</td> 
      <td>EMAIL</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="ADDRESS" maxlength="90" size="60" />
      </td>
      <td>
        <input type="text" name="AGE" maxlength="3" size="10" />
      </td>
      <td>
        <input type="text" name="CPF" maxlength="15" size="20" /> <!-- Changed from PHONE to CPF -->
      </td>
      <td>
        <input type="text" name="EMAIL" maxlength="100" size="40" />
      </td>
      <td>
        <input type="submit" value="Add Data" />
      </td>
    </tr>
  </table>
</form>
<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NAME</td>
    <td>ADDRESS</td>
    <td>AGE</td>
    <td>CPF</td> <!-- Changed from PHONE to CPF -->
    <td>EMAIL</td>
  </tr>
<?php
$result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");
while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>",
       "<td>",$query_data[4], "</td>",
       "<td>",$query_data[5], "</td>";
  echo "</tr>";
}
?>
</table>
<!-- Clean up. -->
<?php
  mysqli_free_result($result);
  mysqli_close($connection);
?>
</body>
</html>
<?php
/* Add an employee to the table. */
function AddEmployee($connection, $name, $address, $age, $cpf, $email) {
   $n = mysqli_real_escape_string($connection, $name);
   $a = mysqli_real_escape_string($connection, $address);
   $ag = $age != "" ? mysqli_real_escape_string($connection, $age) : "NULL";
   $cp = $cpf != "" ? "'" . mysqli_real_escape_string($connection, $cpf) . "'" : "NULL"; // Changed from PHONE to CPF
   $e = mysqli_real_escape_string($connection, $email);
   $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS, AGE, CPF, EMAIL) VALUES ('$n', '$a', $ag, $cp, '$e');"; // Changed from PHONE to CPF
   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}
/* Check whether the table exists and, if not, create it. */
function VerifyEmployeesTable($connection, $dbName) {
  if(!TableExists("EMPLOYEES", $connection, $dbName))
  {
     $query = "CREATE TABLE EMPLOYEES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90),
         AGE INT,
         CPF VARCHAR(60),
         EMAIL VARCHAR(100)
       )";
     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}
/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);
  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");
  if(mysqli_num_rows($checktable) > 0) return true;
  return false;
}
?>
