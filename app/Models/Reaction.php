<?php
class Reaction {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Check if user already reacted
    public function checkReaction($postId, $userId){
        $this->db->query('SELECT * FROM reactions WHERE post_id = :post_id AND user_id = :user_id');
        $this->db->bind(':post_id', $postId);
        $this->db->bind(':user_id', $userId);
        return $this->db->single();
    }

    // Add or Update Reaction
    public function addReaction($postId, $userId, $type){
        // Check existing
        $existing = $this->checkReaction($postId, $userId);
        
        if($existing){
            // Update existing reaction
            $this->db->query('UPDATE reactions SET type = :type WHERE post_id = :post_id AND user_id = :user_id');
        } else {
            // Insert new reaction
            $this->db->query('INSERT INTO reactions (post_id, user_id, type) VALUES(:post_id, :user_id, :type)');
        }
        
        $this->db->bind(':post_id', $postId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':type', $type);
        
        return $this->db->execute();
    }
}