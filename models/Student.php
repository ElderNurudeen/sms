<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../lib/helpers.php';

class Student
{
    private $conn;
    private $table = 'students';

    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
    }

    // Get all students
    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    // Get student by ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $student = $stmt->fetch();
        if (!$student)
            jsonError("Student not found", 404);
        return $student;
    }

    // Create a new student
    public function create($data)
    {
        // Validation
        if (empty($data['reg_number']) || empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['class'])) {
            jsonError("All fields (reg_number, first_name, last_name, email, class) are required", 400);
        }

        // Check unique reg_number & email
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE reg_number = :reg_number OR email = :email");
        $stmt->execute(['reg_number' => $data['reg_number'], 'email' => $data['email']]);
        if ($stmt->fetch())
            jsonError("Reg number or email already exists", 400);

        // Insert
        $sql = "INSERT INTO {$this->table} (reg_number, first_name, last_name, email, class) 
                VALUES (:reg_number, :first_name, :last_name, :email, :class)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute($data)) {
            return $this->getById($this->conn->lastInsertId());
        } else {
            jsonError("Failed to create student", 500);
        }
    }

    // Update student
    public function update($id, $data)
    {
        $student = $this->getById($id); // check if exists

        $sql = "UPDATE {$this->table} SET 
                    reg_number = :reg_number,
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    class = :class
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $data['id'] = $id;
        if ($stmt->execute($data)) {
            return $this->getById($id);
        } else {
            jsonError("Failed to update student", 500);
        }
    }

    // Delete student
    public function delete($id)
    {
        $this->getById($id); // check if exists
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            jsonResponse(["message" => "Student deleted successfully"]);
        } else {
            jsonError("Failed to delete student", 500);
        }
    }

    // Update fee status
    public function updateFeeStatus($id, $status)
    {
        if (!in_array($status, ['paid', 'unpaid'])) {
            jsonError("Invalid fee status. Must be 'paid' or 'unpaid'", 400);
        }
        $this->getById($id); // check if exists
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET fee_status = :status WHERE id = :id");
        if ($stmt->execute(['status' => $status, 'id' => $id])) {
            return $this->getById($id);
        } else {
            jsonError("Failed to update fee status", 500);
        }
    }

    // Search by first or last name
    public function searchByName($name)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE first_name LIKE :name OR last_name LIKE :name");
        $stmt->execute(['name' => "%$name%"]);
        return $stmt->fetchAll();
    }
}
