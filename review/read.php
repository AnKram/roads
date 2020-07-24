<?php

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// подключение к базе данных будет здесь

// подключение базы данных и файл, содержащий объекты
include_once '../app/db.php';
include_once '../objects/review.php';


$db_conn = DB::getDbConn();

$review = new Review($db_conn);

$stmt = $review->read();

$num = $stmt->rowCount();
if ($num > 0) {
    $reviews_arr = array();
    $reviews_arr['records'] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $review_item = array(
            'id' => $id,
            'name' => $name,
            'text' => html_entity_decode($text),
            'rating' => $rating,
            'link1' => $link1,
            'link2' => $link2,
            'link3' => $link3,
            'date_added' => $date_added,
        );

        array_push($reviews_arr['records'], $review_item);
    }

    http_response_code(200);
    echo json_encode($reviews_arr);
} else {
    http_response_code(404);
    echo json_encode(array('message' => 'Отзывы не найдены.'), JSON_UNESCAPED_UNICODE);
}
