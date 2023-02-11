<?php
    require '../broker.php';
    $broker=Broker::getBroker();
    echo json_encode($broker->vratiKolekciju('select * from boja'));

?>