<?php

class UpdateCourseEntity
{
    public $course_name;
    public $items_per_attempt;
    public $max_attempts;

    public function __construct($course_name, $items_per_attempt, $max_attempts)
    {
        $this->course_name = $course_name;
        $this->items_per_attempt = $items_per_attempt;
        $this->max_attempts = $max_attempts;
    }
}
