<?php

class Comment extends Model {
    public function getComments($postId) {
        $this->db->query("SELECT * FROM comments WHERE post_id = :post_id");
        $this->db->bind(':post_id', $postId);
        return $this->db->resultSet();
    }
}
