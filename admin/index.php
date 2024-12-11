<?php
    session_start();
    if (!isset($_SESSION["adminLogin"]) && $_SESSION["adminLogin"] == false) {
        header("Location: ../");
        exit;
    }else{
        header("Location:/");
        exit;
    }