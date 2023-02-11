<?php
    require '../broker.php';
    $broker=Broker::getBroker();

    $naziv=$_POST['naziv'];
    $id=$_POST['id'];
    if(!preg_match('/^[a-zA-Z]*$/',$naziv)){
        echo json_encode([
            'status'=>false,
            'error'=>'Naziv kategorije sme da se sastoji samo od slova!'
        ]);
    }else{
        $rezultat=$broker->izmeni("update kategorija set naziv='".$naziv."' where id=".$id);
       echo json_encode($rezultat);
    }

?>