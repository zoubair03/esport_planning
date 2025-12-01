<?php
class Comment {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getCommentsByPostId($id){
        $this->db->query('SELECT *,
                        comments.id as commentId,
                        users.name as userName,
                        comments.created_at as commentCreated
                        FROM comments
                        INNER JOIN users
                        ON comments.user_id = users.id
                        WHERE comments.post_id = :post_id
                        ORDER BY comments.created_at DESC');
        $this->db->bind(':post_id', $id);
        return $this->db->resultSet();
    }

    public function addComment($data){
        $this->db->query('INSERT INTO comments (post_id, user_id, content) VALUES(:post_id, :user_id, :content)');
        $this->db->bind(':post_id', $data['post_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':content', $data['content']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}