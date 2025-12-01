<?php
class Post {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getPosts(){
        // CHANGED: INNER JOIN -> LEFT JOIN
        // This ensures posts appear even if the user ID is invalid/deleted
        $this->db->query('SELECT *, 
                        posts.id as postId, 
                        users.id as userId,
                        posts.created_at as postCreated,
                        users.name as userName
                        FROM posts
                        LEFT JOIN users
                        ON posts.user_id = users.id
                        ORDER BY posts.created_at DESC');
        return $this->db->resultSet();
    }

    public function getPostById($id){
        // CHANGED: INNER JOIN -> LEFT JOIN
        $this->db->query('SELECT *,
                        posts.id as postId,
                        users.id as userId,
                        posts.created_at as postCreated,
                        users.name as userName
                        FROM posts
                        LEFT JOIN users
                        ON posts.user_id = users.id
                        WHERE posts.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // NEW: Add Post Function
    public function addPost($data){
        try {
            $this->db->query('INSERT INTO posts (title, user_id, content, image) VALUES(:title, :user_id, :content, :image)');
            
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':content', $data['body']);
            $this->db->bind(':image', $data['image']);

            if($this->db->execute()){
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // This will show you exactly why it failed (e.g., Foreign Key error)
            die("Database Error: " . $e->getMessage());
        }
    }

    public function deletePost($id){
        $this->db->query('DELETE FROM posts WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}