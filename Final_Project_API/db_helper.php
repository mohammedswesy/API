<?php
class DbHelper
{
  private $conn;

  function createDbConnection()
  {
    try {
      $this->conn = new mysqli("localhost", "root", "", "API_Project");
    } catch (Exception $error) {
      echo $error->getMessage();
    }
  }
  function insertNewEmployee($name, $email, $image, $salary)
  {
    try {
      $current_date = date('Y-m-d H:i:s');
      $file_link = $this->saveImage($image);
      $sql = "INSERT INTO employee (name,email,image,created_at, salary)VALUES ('$name','$email','$file_link','$current_date', '$salary')";
      $result =  $this->conn->query($sql);
      if ($result == true) {
        $this->createResponse(
          true,
          1,
          $this->createEmployeeResponse(
            $this->conn->insert_id,
            $name,
            $email,
            $file_link,
            $current_date,
            $salary
          )
        );
      } else {
        $this->createResponse(false, 0, "data has not been inserted");
      }
    } catch (Exception $error) {
      $this->createResponse(false, 0, $error->getMessage());
    }
  }
  function getAllEmployee()
  {
    try {
      $sql = "SELECT * FROM employee";
      $result = $this->conn->query($sql);

      $count =  $result->num_rows;
      if ($count > 0) {
        $allEmployeesArray = array();
        while ($row = $result->fetch_assoc()) {
          $id = $row["id"];
          $name = $row["name"];
          $email = $row["email"];
          $image = $row["image"];
          $date = $row["created_at"];
          $salary = $row["salary"];
          // create associative array for the student
          $employee_array = $this->createEmployeeResponse($id, $name, $email, $image, $date, $salary);
          array_push($allEmployeesArray, $employee_array);
        }
        $this->createResponse(true, $count, $allEmployeesArray);
      } else {
        throw new Exception("No Data Found");
      }
    } catch (Exception $exception) {
      $this->createResponse(false, 0, array("error" => $exception->getMessage()));
    }
  }
  function getEmployeeById($id)
  {
    $sql = "SELECT * FROM employee where id = $id";
    $result = $this->conn->query($sql);
    try {
      if ($result->num_rows == 0) {
        throw new Exception("there are no Employees with the passed id");
      } else {
        $row =   $result->fetch_assoc();
        $id = $row["id"];
        $name = $row["name"];
        $email = $row["email"];
        $image = $row["image"];
        $date = $row["created_at"];
        $salary = $row["salary"];
        // create associative array for the student
        $student_array = $this->createEmployeeResponse($id, $name, $email, $image, $date, $salary);
        $this->createResponse(true, 1, $student_array);
      }
    } catch (Exception $exception) {
      http_response_code(400);
      $this->createResponse(false, 0, array("error" => $exception->getMessage()));
    }
  }
  function deleteEmployee($id)
  {
    try {
      $sql = "DELETE FROM employee WHERE id = $id";
      $result = $this->conn->query($sql);

      if (mysqli_affected_rows($this->conn) > 0) {
        $this->createResponse(true, 1, array("data" => "Employee has been deleted"));
      } else {
        throw new Exception("There are no Employees with the passed id");
      }
    } catch (Exception $exception) {
      $this->createResponse(false, 0, array("error" => $exception->getMessage()));
    }
  }
  function updateEmployee($id, $name, $email, $image, $salary)
  {
    try {
      $current_date = date('Y-m-d H:i:s');
      $file_link = $this->saveImage($image);
      $sql = "UPDATE employee set name = '$name', email = '$email', image = '$file_link', created_at = '$current_date', salary = $salary where id = $id";
      $result = $this->conn->query($sql);
      if ($result == true) {
        $this->createResponse(
          true,
          1,
          $this->createEmployeeResponse(
            $this->conn->insert_id,
            $name,
            $email,
            $file_link,
            $current_date,
            $salary
          )
        );
      } else {
        $this->createResponse(false, 0, "data has not been inserted");
      }
    } catch (Exception $exception) {
      $this->createResponse(false, 0, array("error" => $exception->getMessage()));
    }
  }
  function saveImage($file)
  {
    $dir_name = "images/";
    $fullPath = $dir_name . $file["name"];
    move_uploaded_file($file["tmp_name"], $fullPath);
    $file_link = "http://localhost:8080/Final_Project_API/" . $fullPath;
    return $file_link;
  }
  function createResponse($isSuccess, $count, $data)
  {
    echo json_encode(array(
      "success" => $isSuccess,
      "count" => $count,
      "data" => $data
    ));
  }
  function createEmployeeResponse($id, $name, $email, $image_url, $created_date, $salary)
  {
    return array(
      "id" => $id,
      "name" => $name,
      "email" => $email,
      "image" => $image_url,
      "created_at" => $created_date,
      "salary" => $salary
    );
  }
}
