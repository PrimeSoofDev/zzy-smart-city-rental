<?php
require_once "config/config.php";
require_once "app/Core/Database.php";
$db = Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE messages");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $db->query("DESCRIBE attachments");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
