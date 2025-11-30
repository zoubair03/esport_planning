<?php

class Post extends Model {
    public function getPosts() {
        $this->db->query("SELECT * FROM posts");
        return $this->db->resultSet();
    }
}
