<?php

class Review
{
    private $conn;
    private $table_name = 'review';

    public $id;
    public $name;
    public $rating;
    public $text;
    public $link1;
    public $link2;
    public $link3;
    public $date_added;

    private $sort = array('date', 'rating');

    public function __construct($db)
    {
        $this->conn = $db;
    }


    function read()
    {
        $query = 'SELECT * FROM `' . $this->table_name . '`;';

        return $this->conn->query($query);
    }

    function create()
    {
        $query = '
            INSERT INTO ' . $this->table_name . '
            SET
                name=:name, 
                rating=:rating, 
                text=:text, 
                link1=:link1, 
                link2=:link2, 
                link3=:link3, 
                date_added=:date_added';

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->text = htmlspecialchars(strip_tags($this->text));
        $this->link1 = htmlspecialchars(strip_tags($this->link1));
        $this->link2 = htmlspecialchars(strip_tags($this->link2));
        $this->link3 = htmlspecialchars(strip_tags($this->link3));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':text', $this->text);
        $stmt->bindParam(':link1', $this->link1);
        $stmt->bindParam(':link2', $this->link2);
        $stmt->bindParam(':link3', $this->link3);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function readOne(bool $fields)
    {
        $query = '
            SELECT * 
            FROM ' . $this->table_name . ' 
            WHERE `id` = ?
            LIMIT 0,1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['name'];
        $this->rating = $row['rating'];
        $this->text = $row['text'];
        $this->link1 = $row['link1'];
        $this->link2 = $row['link2'];
        $this->link3 = $row['link3'];
    }

    public function readPag(int $from_record_num = 0, int $records_per_page = 10, string $sort = 'date')
    {
        if (!empty($sort) && in_array($sort, $this->sort)) {
            $query = '
                SELECT *
                FROM ' . $this->table_name . '
                ORDER BY `' . $sort . '`
                LIMIT ' . (int)$from_record_num . ', ' . (int)$records_per_page . ';';

            return $this->conn->query($query);
        } else {
            die(json_encode(array('message' => 'sort error', 'sort' => $sort)));
        }
    }

    public function count(){
        $query = 'SELECT COUNT(*) as total_rows FROM " . $this->table_name . "';

        $row = $this->conn->prepare($query)->execute()->fetch(PDO::FETCH_ASSOC);

        return $row['total_rows'];
    }
}
