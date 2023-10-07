<?php

use App\Models\Regional;

try {
    
    $regional = Regional::getAll();

} catch(MeekroDBException $err) {
    dd(strval($err));
}