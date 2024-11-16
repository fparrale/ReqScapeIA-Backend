<?php
require_once 'entities/UserEntity.php';
require_once 'services/UserService.php';
require_once 'services/RoomService.php';

class SeedService
{
    public function seedDatabase()
    {
        $this->cleanDatabase();

        $adminId = $this->createAdminUser();
        $this->createStudents();
        $this->createRooms($adminId);
    }

    private function cleanDatabase()
    {
        RoomService::deleteAll();
        UserService::deleteAll();
    }

    private function createAdminUser()
    {
        $user = new UserEntity([
            'first_name' => 'Root',
            'last_name' => 'Admin',
            'email' => 'admin@ug.edu.ec',
            'password' => password_hash('holamundo', PASSWORD_DEFAULT),
            'role' => 'admin',
        ]);

        $adminId = UserService::createUser($user);
        return $adminId;
    }

    private function createStudents()
    {
        $students = [
            [
                'first_name' => 'Juan',
                'last_name' => 'Perez',
                'email' => 'juan@ug.edu.ec',
                'password' => password_hash('holamundo', PASSWORD_DEFAULT),
                'role' => 'student',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Gomez',
                'email' => 'maria@ug.edu.ec',
                'password' => password_hash('holamundo', PASSWORD_DEFAULT),
                'role' => 'student',
            ],
            [
                'first_name' => 'Pedro',
                'last_name' => 'Lopez',
                'email' => 'pedro@ug.edu.ec',
                'password' => password_hash('holamundo', PASSWORD_DEFAULT),
                'role' => 'student',
            ],
        ];

        foreach ($students as $student) {
            UserService::createUser(new UserEntity($student));
        }
    }

    private function createRooms($adminId)
    {
        $rooms = [
            [
                'room_code' => 'SOF-S-MA-1-1',
                'room_name' => 'Ingeniería de Requerimientos',
                'max_attempts' => 3,
            ],
            [
                'room_code' => 'SOF-S-MA-1-2',
                'room_name' => 'Ingeniería de Software',
                'max_attempts' => 4,
            ],
            [
                'room_code' => 'SOF-S-MA-1-3',
                'room_name' => 'Procesos de Software',
                'max_attempts' => 5,
            ],
            [
                'room_code' => 'SOF-S-MA-1-4',
                'room_name' => 'Metodologías de Desarrollo de Software',
                'max_attempts' => 3,
            ],
        ];

        foreach ($rooms as $room) {
            RoomService::create(new RoomEntity(
                $room['room_name'],
                $room['room_code'],
                $room['max_attempts']
            ), $adminId);
        }
    }
}
