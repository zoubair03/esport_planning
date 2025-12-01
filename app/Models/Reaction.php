<?php
class Reaction {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Get total reaction count for a post
    public function getCount($postId){
        $this->db->query('SELECT COUNT(*) as count FROM reactions WHERE post_id = :post_id');
        $this->db->bind(':post_id', $postId);
        $row = $this->db->single();
        return $row->count;
    }

    // Check if a specific user has reacted (and what type)
    public function getCurrentUserReaction($postId, $userId){
        $this->db->query('SELECT type FROM reactions WHERE post_id = :post_id AND user_id = :user_id');
        $this->db->bind(':post_id', $postId);
        $this->db->bind(':user_id', $userId);
        $row = $this->db->single();
        return $row ? $row->type : false; // Returns 'like', 'love', or false
    }

    // Toggle Reaction: Add, Update, or Remove
    public function toggleReaction($postId, $userId, $type){
        // 1. Check if reaction exists
        $this->db->query('SELECT * FROM reactions WHERE post_id = :post_id AND user_id = :user_id');
        $this->db->bind(':post_id', $postId);
        $this->db->bind(':user_id', $userId);
        $existing = $this->db->single();

        if($existing){
            // 2. If it exists...
            if($existing->type == $type){
                // ...and it's the SAME type (e.g., clicked Like twice), REMOVE it.
                $this->db->query('DELETE FROM reactions WHERE post_id = :post_id AND user_id = :user_id');
            } else {
                // ...and it's a DIFFERENT type (e.g., changed Like to Love), UPDATE it.
                $this->db->query('UPDATE reactions SET type = :type WHERE post_id = :post_id AND user_id = :user_id');
            }
        } else {
            // 3. If it doesn't exist, INSERT it.
            $this->db->query('INSERT INTO reactions (post_id, user_id, type) VALUES(:post_id, :user_id, :type)');
        }

        // Bind common params for the execution above
        $this->db->bind(':post_id', $postId);
        $this->db->bind(':user_id', $userId);
        // Only bind type if we are strictly inserting or updating
        if(!$existing || ($existing && $existing->type != $type)){
             $this->db->bind(':type', $type);
        }

        return $this->db->execute();
    }
}