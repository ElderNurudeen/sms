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

        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    // Get all students
    public function getAll($fee_status = null, $class = null)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE 1=1";
            $params = [];

            if ($fee_status) {
                if (!in_array($fee_status, ['paid', 'unpaid'])) {
                    jsonError("Invalid fee_status filter. Must be 'paid' or 'unpaid'", 400);
                }
                $sql .= " AND fee_status = :fee_status";
                $params['fee_status'] = $fee_status;
            }

            if ($class) {
                $sql .= " AND class = :class";
                $params['class'] = $class;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            jsonError("Database error: " . $e->getMessage(), 500);
        }
    }


    // Get student by ID
    public function getById($id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $student = $stmt->fetch();
            if (!$student) {
                jsonError("Student not found", 404);
            }
            return $student;
        } catch (PDOException $e) {
            jsonError("Database error: " . $e->getMessage(), 500);
        }
    }

    // Create a new student
    public function create($data)
    {
        try {
            // Validation: required fields
            if (empty($data['reg_number']) || empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['class'])) {
                jsonError("All fields (reg_number, first_name, last_name, email, class) are required", 400);
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                jsonError("Invalid email format", 400);
            }

            // Check unique reg_number & email
            $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE reg_number = :reg_number OR email = :email");
            $stmt->execute([
                'reg_number' => $data['reg_number'],
                'email' => $data['email']
            ]);
            if ($stmt->fetch()) {
                jsonError("Reg number or email already exists", 400);
            }

            // Insert
            $sql = "INSERT INTO {$this->table} (reg_number, first_name, last_name, email, class) 
                VALUES (:reg_number, :first_name, :last_name, :email, :class)";
            $stmt = $this->conn->prepare($sql);
            $params = [
                'reg_number' => $data['reg_number'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'class' => $data['class']
            ];
            if ($stmt->execute($params)) {
                return $this->getById($this->conn->lastInsertId());
            } else {
                jsonError("Failed to create student", 500);
            }
        } catch (PDOException $e) {
            jsonError("Database error: " . $e->getMessage(), 500);
        }
    }


    // Update student
    public function update($id, $data)
    {
        try {
            $existing = $this->getById($id);

            // Prevent reg_number or email change
            if (isset($data['reg_number']) && $data['reg_number'] !== $existing['reg_number']) {
                jsonError("Reg number cannot be changed", 400);
            }
            if (isset($data['email']) && $data['email'] !== $existing['email']) {
                jsonError("Email cannot be changed", 400);
            }

            // Use existing values if fields are not provided
            $first_name = $data['first_name'] ?? $existing['first_name'];
            $last_name = $data['last_name'] ?? $existing['last_name'];
            $class = $data['class'] ?? $existing['class'];

            $sql = "UPDATE {$this->table} SET 
                    first_name = :first_name,
                    last_name  = :last_name,
                    class      = :class
                WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $params = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'class' => $class,
                'id' => $id
            ];

            if ($stmt->execute($params)) {
                return $this->getById($id);
            } else {
                jsonError("Failed to update student", 500);
            }
        } catch (PDOException $e) {
            jsonError("Database error: " . $e->getMessage(), 500);
        }
    }


    // Delete student
    public function delete($id)
    {
        try {
            $this->getById($id); // check if exists
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
            if ($stmt->execute(['id' => $id])) {
                jsonResponse(["message" => "Student deleted successfully"]);
            } else {
                jsonError("Failed to delete student", 500);
            }
        } catch (PDOException $e) {
            jsonError("Database error: " . $e->getMessage(), 500);
        }
    }

    // Update fee status
    public function updateFeeStatus($id, $status)
    {
        try {
            if (!in_array($status, ['paid', 'unpaid'])) {
                jsonError("Invalid fee status. Must be 'paid' or 'unpaid'", 400);
            }
            $this->getById($id);
            $stmt = $this->conn->prepare("UPDATE {$this->table} SET fee_status = :status WHERE id = :id");
            if ($stmt->execute(['status' => $status, 'id' => $id])) {
                return $this->getById($id);
            } else {
                jsonError("Failed to update fee status", 500);
            }
        } catch (PDOException $e) {
            jsonError("Database error: " . $e->getMessage(), 500);
        }
    }

    // Search by first or last name
    public function searchByName($name)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM {$this->table} 
             WHERE LOWER(first_name) LIKE :name 
                OR LOWER(last_name) LIKE :name"
            );
            $stmt->execute(['name' => "%" . strtolower($name) . "%"]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            jsonError("Database error: " . $e->getMessage(), 500);
        }
    }

}
