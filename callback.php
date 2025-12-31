<?php
$data = file_get_contents("php://input");
file_put_contents("mpesa_log.json", $data, FILE_APPEND);
