<?php
include 'db_helper.php';
header("Content-Type:application/json");
$db_helper = new DbHelper();
$db_helper->createDbConnection();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = $_POST["id"];
  $name = $_POST["name"];
  $email = $_POST["email"];
  $myFile = $_FILES["image"];
  $salary = $_POST["salary"];

  $db_helper->updateEmployee($id, $name, $email, $myFile, $salary);
}
