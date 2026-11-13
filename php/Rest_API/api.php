<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (!function_exists('getallheaders')) {
    function getallheaders() {
        $out = [];
        foreach ($_SERVER as $k => $v) {
            if (substr($k, 0, 5) === 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($k, 5)))));
                $out[$name] = $v;
            }
        }
        return $out;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method  = $_SERVER['REQUEST_METHOD'];
$payload = json_decode(file_get_contents("php://input"), true);
$headers = getallheaders();
$response = [];

switch ($method) {
    case "GET":
        $response["message"] = "GET received";
        $response["query"]   = $_GET;
        http_response_code(200);
        break;

    case "POST":
        if ($payload === null) {
            $response["error"] = "invalid json body";
            http_response_code(400);
            break;
        }
        $response["message"] = "resource created";
        $response["payload"] = $payload;
        http_response_code(201);
        break;

    case "PUT":
        if ($payload === null) {
            $response["error"] = "invalid json body";
            http_response_code(400);
            break;
        }
        $response["message"] = "resource updated";
        $response["payload"] = $payload;
        http_response_code(200);
        break;

    case "DELETE":
        $response["message"] = "resource removed";
        http_response_code(200);
        break;

    default:
        $response["error"] = "method not allowed";
        http_response_code(405);
}

$response["auth"] = $headers["Authorization"] ?? null;
echo json_encode($response, JSON_PRETTY_PRINT);