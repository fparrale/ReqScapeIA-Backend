<?php

class CourseEntity
{
    public $id;
    public $course_name;
    public $course_code;
    public $items_per_attempt;
    public $max_attempts;

    public function __construct($course_name, $course_code, $items_per_attempt, $max_attempts)
    {
        $this->course_name = $course_name;
        $this->course_code = $course_code;
        $this->items_per_attempt = $items_per_attempt;
        $this->max_attempts = $max_attempts;
    }
}
