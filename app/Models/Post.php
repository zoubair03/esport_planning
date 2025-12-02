<?php
class Post {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Get posts; by default only fetch approved posts for public views
    public function getPosts($onlyApproved = true){
        // If we only want approved posts, add WHERE clause
        if($onlyApproved){
            $this->db->query('SELECT *, 
                        posts.id as postId, 
                        users.id as userId,
                        posts.created_at as postCreated,
                        users.name as userName
                        FROM posts
                        LEFT JOIN users
                        ON posts.user_id = users.id
                        WHERE posts.approved = 1
                        ORDER BY posts.created_at DESC');
        } else {
            $this->db->query('SELECT *, 
                        posts.id as postId, 
                        users.id as userId,
                        posts.created_at as postCreated,
                        users.name as userName
                        FROM posts
                        LEFT JOIN users
                        ON posts.user_id = users.id
                        ORDER BY posts.created_at DESC');
        }
        return $this->db->resultSet();
    }

    // Get a single post; by default only return if approved
    public function getPostById($id, $onlyApproved = true){
        if($onlyApproved){
            $this->db->query('SELECT *,
                        posts.id as postId,
                        users.id as userId,
                        posts.created_at as postCreated,
                        users.name as userName
                        FROM posts
                        LEFT JOIN users
                        ON posts.user_id = users.id
                        WHERE posts.id = :id AND posts.approved = 1');
        } else {
            $this->db->query('SELECT *,
                        posts.id as postId,
                        users.id as userId,
                        posts.created_at as postCreated,
                        users.name as userName
                        FROM posts
                        LEFT JOIN users
                        ON posts.user_id = users.id
                        WHERE posts.id = :id');
        }
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // NEW: Add Post Function (now accepts an approved flag)
    public function addPost($data){
        try {
            $this->db->query('INSERT INTO posts (title, user_id, content, image, approved) VALUES(:title, :user_id, :content, :image, :approved)');

            $this->db->bind(':title', $data['title']);
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':content', $data['body']);
            $this->db->bind(':image', $data['image']);
            $this->db->bind(':approved', isset($data['approved']) ? (int)$data['approved'] : 0, PDO::PARAM_INT);

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

    // Return posts that are pending approval
    public function getPendingPosts(){
        $this->db->query('SELECT *, 
                        posts.id as postId, 
                        users.id as userId,
                        posts.created_at as postCreated,
                        users.name as userName
                        FROM posts
                        LEFT JOIN users
                        ON posts.user_id = users.id
                        WHERE posts.approved = 0
                        ORDER BY posts.created_at DESC');
        return $this->db->resultSet();
    }

    // Set approved flag (1 or 0)
    public function setApproved($id, $value = 1){
        $this->db->query('UPDATE posts SET approved = :val WHERE id = :id');
        $this->db->bind(':val', (int)$value, PDO::PARAM_INT);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}