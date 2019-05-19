#!/usr/bin/env php
<?php

require $_SERVER["WEB_DOCUMENT_ROOT"]."/config.php";

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$database_type = $db->getAttribute(PDO::ATTR_DRIVER_NAME);

$database_setup_file = $_SERVER["WEB_DOCUMENT_ROOT"].'/db.'.$database_type.'.sql';

if(!file_exists($database_setup_file)) {
    echo "No database schema available for $database_type";
    exit(1);
}

$sql_statements = explode(';', file_get_contents($database_setup_file));

echo "Load SQL statements from $database_setup_file\n\n";

foreach($sql_statements as $sql) {
    $sql = str_replace("\n", "", $sql);
    if(!trim($sql))
        continue;
    try {
        echo "$sql\n";
        $db->exec($sql);
    } catch(PDOException $e) {
        echo "FAILED: $e\n";
        exit(1);
    }
}

