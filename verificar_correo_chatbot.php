<?php
require "conecta_chatbot.php";
$con=conecta();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $correo=$_POST['correo'];
    
    $sql="SELECT * FROM datos WHERE correo='$correo'";
    $res=$con->query($sql);

    if($res->num_rows>0){
        echo "1"; 
    }else{
        echo "0";
    }
}
?>