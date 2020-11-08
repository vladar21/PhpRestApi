<?php

require_once 'MovieApi.php';

try {
    $api = new MovieApi();
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}
