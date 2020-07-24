<?php

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// подключение файлов
include_once '../app/core.php';
include_once '../app/pagination.php';
include_once '../app/db.php';
include_once '../objects/review.php';

// utilities
$pagination = new Pagination();

$db_conn = DB::getDbConn();

$review = new Review($db_conn);

$stmt = $review->readPag($from_record_num, $records_per_page);
$num = $stmt->rowCount();

if ($num > 0) {
    $reviews_arr = array();
    $reviews_arr['records'] = array();
    $reviews_arr['paging'] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // извлечение строки
        extract($row);

        $review_item = array(
            'id' => $id,
            'name' => $name,
            'text' => html_entity_decode($text),
            'rating' => $rating,
            'link1' => $link1,
            'link2' => $link2,
            'link3' => $link3
        );

        array_push($reviews_arr["records"], $review_item);
    }

    // подключим пагинацию
    $total_rows = $review->count();
    $page_url = "{$home_url}product/read_pag.php?";
    $paging = $pagination->getPagination($page, $total_rows, $records_per_page, $page_url);
    $products_arr['paging'] = $paging;

    http_response_code(200);
    echo json_encode($products_arr);
} else {
    http_response_code(404);
    echo json_encode(array('message' => 'Отзывы не найдены.'), JSON_UNESCAPED_UNICODE);
}