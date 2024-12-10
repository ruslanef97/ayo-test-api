<?php
function matchResult($key = null){
    $data = [
        0 => 'Draw',
        1 => 'Home Win',
        2 => 'Away Win'
    ];

    if($key == null){
        return $data;
    }else{
        return isset($data[$key]) ? $data[$key] : 'Unknown';
    }
}