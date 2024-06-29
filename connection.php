<?php

// Establishing the Database Connection
$serverName = "localhost";
$userName = "root";
$password = "";
$conn = mysqli_connect($serverName, $userName, $password);

// Checking the Connection Status
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Creating a database named "db_Prototype_2" if it doesn't already exist
$createDatabase = "CREATE DATABASE IF NOT EXISTS db_Prototype_2";
if (!mysqli_query($conn, $createDatabase)) {
    die("Failed to create database: " . mysqli_error($conn));
}

// Selecting the "db_Prototype_2" database for further operations
mysqli_select_db($conn, 'db_Prototype_2');

// Creating a table if it doesn't already exist.This table stores the weather data.

$createTable = "CREATE TABLE IF NOT EXISTS `prototype2` (  
    temperature FLOAT NOT NULL,
    tempCondition varchar(255) NOT NULL,
    weatherIcon varchar(255) NOT NULL,
    city varchar(255) NOT NULL,
    humidity FLOAT NOT NULL,
    wind FLOAT NOT NULL,
    pressure FLOAT NOT NULL,
    `date` DATETIME
);";

if (!mysqli_query($conn, $createTable)) {
    die("Failed to create table: " . mysqli_error($conn));
}

// Verifying if a query parameter named "q" is present in the URL. This parameter specifies the
// city for which weather information is requested. If absent, the default city is "Manchester"

if (isset($_GET['q'])) {
    $cityName = $_GET['q'];
} else {
    $cityName = "Manchester";
}

// Selecting weather data from the table for the specified city.This sorts the data
// by date in descending order.The result of the query is stored in the variable $result.

$selectData = "SELECT * FROM prototype2 WHERE city = '$cityName' ORDER BY date DESC";
$result = mysqli_query($conn, $selectData);
$row = mysqli_fetch_assoc($result);
$lastUpdatedTime = strtotime($row['date']);
$currentTime = time();

// Fetching current weather data if data not exists for the city or if data is 2 hours older.
// This code fetches fresh weather data from API and inserts it into the database table
if (mysqli_num_rows($result) == 0 || $currentTime - $lastUpdatedTime >= 7200) {
    $apikey = "2293671f0c948d6ca4df54ad7a73e082";
    $url = "https://api.openweathermap.org/data/2.5/weather?&q=" . $cityName . '&appid=' . $apikey . "&units=metric";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    $weatherIcon = "https://openweathermap.org/img/wn/".$data['weather'][0]['icon']."@2x.png";
    $city = $data['name'];
    $humidity = $data['main']['humidity'];
    $wind = $data['wind']['speed'];
    $pressure = $data['main']['pressure'];
    $temperature = $data['main']['temp'];
    $tempCondition = $data['weather'][0]['description'];
    $date = date('Y-m-d h:i:s A', $data['dt']);

// Inserting the fetched weather data into the table in the database.
 
    $insertData = "INSERT INTO `prototype2` (`city`,`humidity`, `wind`, `pressure`,`weatherIcon`,`temperature`,`tempCondition`,`date`)
        VALUES ('$city','$humidity', '$wind', '$pressure', '$weatherIcon','$temperature','$tempCondition','$date')";

// Checking if the data insertion was successful.It prints an error message if there is an error.

    if (mysqli_query($conn, $insertData)) {
    }else{
        echo "Failed to insert data ".mysqli_error($conn);
    }
}

// Again fetching weather data from the database to include the newly inserted data in the response.
$result = mysqli_query($conn, $selectData);
if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}
// Iterating over the fetched data rows and stores them in an array named $rows.
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

// Converting the array that contains weather data into JSON format.
$json_data = json_encode($rows);
// Setting header to indicate that the response is in JSON format.
header('Content-Type: application/json');
// Outputting JSON Data
echo $json_data;
?>

