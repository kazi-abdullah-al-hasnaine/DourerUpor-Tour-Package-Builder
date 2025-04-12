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

interface ReviewIterator {
    public function hasNext(): bool;
    public function next(): ?Reviews;
}

class ReviewCollectionIterator implements ReviewIterator {
    private $reviews;
    private $position = 0;
    
    public function __construct(array $reviews) {
        $this->reviews = $reviews;
    }
    
    public function hasNext(): bool {
        return $this->position < count($this->reviews);
    }
    
    public function next(): ?Reviews {
        if (!$this->hasNext()) {
            return null;
        }
        return $this->reviews[$this->position++];
    }
}

class ReviewCollection {
    private $reviews = [];
    
    public function __construct($reviews = []) {
        $this->reviews = $reviews;
    }
    
    public function addReview(Reviews $review): void {
        $this->reviews[] = $review;
    }
    
    public function createIterator(): ReviewIterator {
        return new ReviewCollectionIterator($this->reviews);
    }
}