<?php

namespace App;

use PDO;

class Database
{
    private $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host=db;dbname=banner', 'root', 'root');
    }

    protected function getCollection($sql)
    {
        $result = $this->db->query($sql, PDO::FETCH_ASSOC);

        $collection = [];
        foreach ($result as $row) {
            $collection[$row['id']] = $row;
        }

        return $collection;
    }

    protected function execute($sql)
    {
        return $this->db->query($sql);
    }

    protected function isRowExists($table, $condition, $id = 'id'): bool
    {
        $sql = "SELECT $id FROM $table WHERE ";
        foreach ($condition as $row => $value) {
            $sql .= "$row='$value' AND ";
        }
        $sql .= "1=1 LIMIT 1";

        $result = $this->execute($sql);

        return (bool)$result->rowCount();
    }

    public function incrementUserBannerCounter($bannerId, $userId)
    {
        if ($this->isRowExists('banner_user', ['banner_id' => $bannerId, 'user_id' => $userId], 'banner_id')) {
            $query = "UPDATE banner_user SET view_count=view_count+1 WHERE banner_id=$bannerId AND user_id = '$userId'";
        } else {
            $query = "INSERT INTO banner_user (banner_id, user_id, view_count) VALUE ($bannerId, '$userId', 1)";
        }

        return $this->execute($query);
    }

    public function incrementBannerCounter($bannerId)
    {
        $table = 'banner';

        if ($this->isRowExists($table, ['id' => $bannerId])) {
            $query = "UPDATE $table SET view_count=view_count+1 WHERE id=$bannerId";
            $result = $this->execute($query);
        } else {
            return false;
        }

        return $result;
    }

    public function getBannerListByUser($usedId, $number = 3)
    {
        $query = "SELECT b.*
FROM banner b
         LEFT JOIN banner_user bu on b.id = bu.banner_id AND bu.user_id = '$usedId'
WHERE b.view_count < b.total_views
  AND (bu.view_count < 2 OR bu.banner_id is NULL)
ORDER BY b.view_count * k
LIMIT $number;";

        return $this->getCollection($query);
    }
}