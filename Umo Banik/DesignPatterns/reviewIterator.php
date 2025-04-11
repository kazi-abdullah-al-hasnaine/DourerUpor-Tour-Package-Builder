<?php
class Reviews {
    public $reviewId;
    public $userID;
    public $userName;
    public $rating;
    public $review;
    
    public function __construct($reviewId, $userID, $userName, $rating, $review) {
        $this->reviewId = $reviewId;
        $this->userID = $userID;
        $this->userName = $userName;
        $this->rating = $rating;
        $this->review = $review;
    }
}

class ReviewCollection implements Iterator {
    private $reviews = [];
    private $position = 0;
    
    public function __construct($reviews = []) {
        $this->reviews = $reviews;
    }
    
    public function addReview(Reviews $review): void {
        $this->reviews[] = $review;
    }
    
    public function current(): Reviews {
        return $this->reviews[$this->position];
    }
    
    public function key(): int {
        return $this->position;
    }
    
    public function next(): void {
        ++$this->position;
    }
    
    public function rewind(): void {
        $this->position = 0;
    }
    
    public function valid(): bool {
        return isset($this->reviews[$this->position]);
    }
}