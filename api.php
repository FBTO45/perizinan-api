<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_URI'] == '/api/user') {
    echo json_encode(["data" => "Ini endpoint user"]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}