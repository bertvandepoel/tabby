#!/usr/bin/env php
<?php

require $_SERVER["WEB_DOCUMENT_ROOT"]."/config.php";

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$database_type = explode(':', $_SERVER["TABBY_DB_DSN"])[0];

$database_setup_file = $_SERVER["WEB_DOCUMENT_ROOT"].'/db.'.$database_type.'.sql';

if(!file_exists($database_setup_file)) {
    echo "No database schema available for $database_type";
    exit(1);
}

$sql_statements = explode(';', file_get_contents($database_setup_file));

echo "Load SQL statements from $database_setup_file\n\n";

foreach($sql_statements as $sql) {
    try {
        $db->exec($sql);
    } catch(PDOException $e) {
        echo "FAILED: ".str_replace("\n", "", $sql)."\n$e\n";
        exit(1);
    }
}

