<?php
function first_error($error)
{
    $error_message = '';

    foreach ($error as $key => $item) {
        $error_message = $item[0];
        break;
    }

    return $error_message;
}