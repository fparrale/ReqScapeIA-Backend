<?php
require_once 'config/Database.php';
require_once 'entities/CourseEntity.php';
require_once 'entities/GameConfigEntity.php';
require_once 'services/CourseService.php';
require_once 'services/UserService.php';

class CourseController
{
    public static function createCourse($id, $email)
    {
        $isAdmin = UserService::isAdmin($email);

        if (!$isAdmin) {
            http_response_code(400);
            echo json_encode(['message' => 'Acceso denegado. Solo los administradores pueden crear cursos.']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $course_code = $data['course_code'] ?? null;
        $course_name = $data['course_name'] ?? null;
        $items_per_attempt = $data['items_per_attempt'] ?? null;
        $max_attempts = $data['max_attempts'] ?? null;

        $language = $data['language'] ?? null;
        $additional_context = $data['additional_context'] ?? null;

        if (empty($course_name)) {
            http_response_code(400);
            echo json_encode(['message' => 'Nombre de curso es requerido.']);
            return;
        }

        if (empty($course_code)) {
            http_response_code(400);
            echo json_encode(['message' => 'Código de curso es requerido.']);
            return;
        }

        $course_exists = CourseService::getByCode($course_code);

        if ($course_exists) {
            http_response_code(400);
            echo json_encode(['message' => 'El código de curso ya existe.']);
            return;
        }

        $courseEntity = new CourseEntity($course_name, $course_code, $items_per_attempt, $max_attempts);
        $gameConfigEntity = new GameConfigEntity($language, $additional_context);

        try {
            $createdCourse = CourseService::create($courseEntity, $gameConfigEntity, $id);
            http_response_code(201);
            echo json_encode($createdCourse);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    public static function getAllCourses($id, $email)
    {
        $courses = CourseService::getAllByUserId($id);
        http_response_code(200);
        echo json_encode($courses);
    }

    public static function getEnrolledCourses($id, $email)
    {
        $courses = CourseService::getAllEnrolledByUserId($id);
        http_response_code(200);
        echo json_encode($courses);
    }

    public static function enroll($id, $email)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $course_code = $data['course_code'] ?? null;

        if (!$course_code) {
            http_response_code(400);
            echo json_encode(['message' => 'Código de curso no proporcionado.']);
            return;
        }

        $course = CourseService::getByCode($course_code);

        if (!$course) {
            http_response_code(400);
            echo json_encode(['message' => 'El curso con el código proporcionado no existe.']);
            return;
        }

        $enrolled = CourseService::enroll($id, $course['id']);

        if (!$enrolled) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al inscribirse en el curso.']);
            return;
        }

        http_response_code(200);
        echo json_encode(['message' => 'Inscripción exitosa.']);
    }

    public static function deleteCourse($id, $email)
    {
        $isAdmin = UserService::isAdmin($email);

        if (!$isAdmin) {
            http_response_code(400);
            echo json_encode(['message' => 'Acceso denegado. Solo los administradores pueden crear cursos.']);
            return;
        }

        $courseId = $_GET['params'][0] ?? null;

        if (!$courseId) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de curso no proporcionado.']);
            return;
        }

        $deleted = CourseService::remove($courseId);

        if (!$deleted) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al eliminar el curso.']);
            return;
        }

        http_response_code(200);
        echo json_encode(['message' => 'Curso eliminado exitosamente.']);
    }
}
