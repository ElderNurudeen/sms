<?php
//Send JSON response
function jsonResponse($data = [], $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Send JSON error message

function jsonError($message, $status = 400)
{
    jsonResponse(["error" => $message], $status);
}
