<?php
class Comment {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Get ALL comments (for Single Post page)
    public function getCommentsByPostId($id){
        $this->db->query('SELECT *,
                        comments.id as commentId,
                        users.name as userName,
                        comments.created_at as commentCreated
                        FROM comments
                        LEFT JOIN users
                        ON comments.user_id = users.id
                        WHERE comments.post_id = :post_id
                        ORDER BY comments.created_at ASC');
        $this->db->bind(':post_id', $id);
        return $this->db->resultSet();
    }

    // NEW: Get only 2 recent comments (for Feed Preview)
    public function getRecentComments($id, $limit = 2){
        $this->db->query('SELECT *,
                        comments.id as commentId,
                        users.name as userName,
                        comments.created_at as commentCreated
                        FROM comments
                        LEFT JOIN users
                        ON comments.user_id = users.id
                        WHERE comments.post_id = :post_id
                        ORDER BY comments.created_at DESC 
                        LIMIT :limit');
        $this->db->bind(':post_id', $id);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function addComment($data){
        $this->db->query('INSERT INTO comments (post_id, user_id, content) VALUES(:post_id, :user_id, :content)');
        $this->db->bind(':post_id', $data['post_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':content', $data['content']);
        return $this->db->execute();
    }
}