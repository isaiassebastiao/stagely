<?php
    session_start();
    if(isset($_SESSION["role"]))
        echo json_encode(["role"=>$_SESSION["role"]]);