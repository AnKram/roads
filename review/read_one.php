<?php

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include_once '../app/db.php';
include_once '../objects/review.php';

$db_conn = DB::getDbConn();

$review = new Review($db_conn);

$review->id = isset($_GET['id']) ? $_GET['id'] : die();

$fields = isset($_GET['fields']) && $_GET['fields'] == true ? true : false;

$review->readOne($fields);

if ($review->name != null) {
    $review_arr_short = array(
        'id' => $review->id,
        'name' => $review->name,
        'rating' => $review->rating,
        'link1' => $review->link1
    );
    $review_arr_add = array(
        'text' => $review->text,
        'link2' => $review->link2,
        'link3' => $review->link3
    );

    http_response_code(200);

    //если параметр fields не был отправлен, то возвращаем неполные данные
    if ($fields) {
        echo json_encode(array_merge($review_arr_short, $review_arr_add));
    } else {
        echo json_encode($review_arr_short);
    }
} else {
    http_response_code(404);
    echo json_encode(array('message' => 'Отзыв не существует.'), JSON_UNESCAPED_UNICODE);
}
