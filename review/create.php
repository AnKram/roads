<?php

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../app/db.php';
include_once '../objects/review.php';

$db_conn = DB::getDbConn();

$review = new Review($db_conn);

$data = json_decode(file_get_contents('php://input'));

if (
    empty($data->name) ||
    empty($data->rating) ||
    empty($data->text)
) {
    http_response_code(400);
    echo json_encode(array('message' => 'Невозможно создать отзыв. Данные неполные.'), JSON_UNESCAPED_UNICODE);
} elseif (
    count($data->name) > 50 ||
    $data->rating > 5 ||
    count($data->text) > 1000
) {
    http_response_code(400);
    echo json_encode(array('message' => 'Невозможно создать отзыв. Данные неверны.'), JSON_UNESCAPED_UNICODE);
} else {
    $review->name = $data->name;
    $review->rating = $data->rating;
    $review->text = $data->text;
    $review->link2 = !empty($data->link1) ? $data->link1 : '';
    $review->link2 = !empty($data->link2) ? $data->link2 : '';
    $review->link2 = !empty($data->link3) ? $data->link3 : '';
    $review->date_added = date('Y-m-d H:i:s');

    if ($review->create()) {
        http_response_code(201);
        echo json_encode(array('id' => $review->id), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(503);
        echo json_encode(array('message' => 'Невозможно создать отзыв.'), JSON_UNESCAPED_UNICODE);
    }
}
