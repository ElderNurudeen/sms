<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../lib/helpers.php';

class StudentController
{
    private $student;

    public function __construct()
    {
        $this->student = new Student();
    }

    // GET /students
    public function index()
    {
        $fee_status = $_GET['fee_status'] ?? null;
        $class = $_GET['class'] ?? null;

        $students = $this->student->getAll($fee_status, $class);
        jsonResponse($students);
    }

    // GET /students/{id}
    public function show($id)
    {
        $student = $this->student->getById($id);
        jsonResponse($student);
    }

    // POST /students
    public function store($data)
    {
        $student = $this->student->create($data);
        $emailStatus = 'sent';

        require_once __DIR__ . '/../lib/Mailer.php';
        $mailer = new Mailer();

        $subject = "Welcome to Our School";
        $body = "
        <h2>Welcome, {$student['first_name']}!</h2>
        <p>Your registration was successful.</p>
        <p><strong>Reg Number:</strong> {$student['reg_number']}</p>
        <p><strong>Class:</strong> {$student['class']}</p>
        <p>We’re excited to have you onboard.</p>
    ";

        try {
            $mailer->send($student['email'], $subject, $body);
        } catch (\Exception $e) {
            $emailStatus = 'failed: ' . $e->getMessage();
        }

        $student['email_status'] = $emailStatus;

        jsonResponse($student, 201);
    }




    // PUT /students/{id}
    public function update($id, $data)
    {
        $student = $this->student->update($id, $data);
        jsonResponse($student);
    }

    // DELETE /students/{id}
    public function destroy($id)
    {
        $this->student->delete($id);
    }

    // PUT /students/{id}/fee
    public function updateFee($id, $data)
    {
        // Must have fee_status
        if (!isset($data['fee_status'])) {
            jsonError("fee_status is required", 400);
        }

        // not allow extra fields
        $allowed = ['fee_status'];
        $extra = array_diff(array_keys($data), $allowed);
        if (!empty($extra)) {
            jsonError("Only 'fee_status' is allowed on this route", 400);
        }

        $student = $this->student->updateFeeStatus($id, $data['fee_status']);
        jsonResponse($student, 200);
    }


    // GET /students/search?name=...
    public function search($name)
    {
        // prevent empty name
        if (trim($name) === '') {
            jsonError("Query parameter 'name' is required", 400);
        }

        $students = $this->student->searchByName($name);

        if (!$students || count($students) === 0) {
            jsonError("Record not found", 404);
        }

        jsonResponse($students);
    }


}
