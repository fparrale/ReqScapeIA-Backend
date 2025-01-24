<?php
require_once 'services/UserService.php';

class StudentService
{
    public static function getStudentGameHistory($email)
    {
        if (UserService::isAdmin($email)) {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden']);
            exit;
        }

        $user = UserService::getByEmail($email);

        $query = "SELECT 
            a.id as id,
            a.user_id as user_id,
            a.course_id as course_id,
            a.totalreq,
            a.movements,
            a.score,
            a.status,
            a.time,
            a.created_at as created_at,
            c.course_name as course_name,
            c.course_code as course_code,
            c.max_attempts as max_attempts,
            c.items_per_attempt as items_per_attempt
        FROM attempts a
        JOIN courses c ON a.course_id = c.id 
        WHERE a.user_id = :user_id
        ORDER BY a.created_at DESC";
        
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->execute();
        $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $attempts;
    }

    public static function getStudentGameHistoryByCourse($userId, $courseId)
    {
        $query = "SELECT 
            a.id as id,
            a.user_id as user_id,
            a.course_id as course_id,
            a.totalreq,
            a.movements,
            a.score,
            a.status,
            a.time,
            a.created_at as created_at,
            c.course_name as course_name,
            c.course_code as course_code,
            c.max_attempts as max_attempts,
            c.items_per_attempt as items_per_attempt
        FROM attempts a
        JOIN courses c ON a.course_id = c.id 
        WHERE a.user_id = :user_id AND a.course_id = :course_id 
        ORDER BY a.created_at DESC";

        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $attempts;
    }
}
