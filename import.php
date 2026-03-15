<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';
$pdo = Database::getInstance()->getConnection();

$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    country VARCHAR(100),
    city VARCHAR(100),
    is_active BOOLEAN,
    gender VARCHAR(20),
    birth_date DATE,
    salary NUMERIC(10, 2),
    has_children BOOLEAN,
    family_status VARCHAR(50),
    registration_date DATE
)");

$filename = 'data.csv';

if (!file_exists($filename) || !is_readable($filename))
{
    die("Файл не найден");
}

$file = fopen($filename, 'r');

$header = fgetcsv($file, 0, ',', '"', '\\');

$sql = "INSERT INTO users (country, city, is_active, gender, birth_date, salary, has_children, family_status, registration_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);

$count = 0;
while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false)
{
    $isActive = filter_var($row[2], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
    $hasChildren = filter_var($row[6], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';

    $stmt->execute([
        $row[0], // country
        $row[1], // city
        $isActive,
        $row[3], // gender
        $row[4], // birthDate
        $row[5], // salary
        $hasChildren,
        $row[7], // familyStatus
        $row[8]  // registrationDate
    ]);
    $count++;
}

fclose($file);
echo "испортировано $count записей";