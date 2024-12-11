<?php 

    define("DB_HOST","localhost");
    define("DB_PRE","root");
    define("DB_PASS","1234");
    define("DB_NAME","zehadkha_bubtsdp");

    $con = mysqli_connect(DB_HOST,DB_PRE,DB_PASS,DB_NAME) or die("Database connection failed!!");

?>