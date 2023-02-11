<?php
    require '../broker.php';
    $broker=Broker::getBroker();
    $id=$_POST['id'];

    if(!isset($id) || !intval($id)){
        echo json_encode([
            'status'=>false,
            'error'=>'los ID'
        ]);
    }else{
        echo json_encode($broker->izmeni('delete from kategorija where id='.$id));
    }

?> 