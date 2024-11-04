<?php

/*******w******** 
    
    Name: Malcolm White
    Date: 2024-10-30
    Description: This page contains the logic that is used to connect the PHP files to the database.

****************/

    define('DB_DSN','mysql:host=localhost;dbname=serverside;charset=utf8');
    define('DB_USER','serveruser');
    define('DB_PASS','gorgonzola7!');   
     
     try {
         $db = new PDO(DB_DSN, DB_USER, DB_PASS);
     } catch (PDOException $e) {
         print "Error: " . $e->getMessage();
         die(); 
     }
 ?>