<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Fee Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1000px;
        }

        .header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .filter-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table thead th {
            background-color: #f1f5f9;
            border-top: none;
            font-weight: 600;
            color: #4a5568;
        }

        .status-paid {
            color: #28a745;
            font-weight: 600;
        }

        .status-unpaid {
            color: #dc3545;
            font-weight: 600;
        }

        .status-cell {
            cursor: pointer;
            transition: all 0.2s;
        }

        .status-cell:hover {
            transform: scale(1.05);
        }

        .filter-btn {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 2px;
        }

        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }

        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .btn-close {
            color: white;
        }

        #saveFeeBtn,
        #saveStudentBtn,
        #updateStudentBtn {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .action-buttons {
            white-space: nowrap;
        }

        .btn-add {
            background: linear-gradient(135deg, #17c964 0%, #20d85c 100%);
            border: none;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="header text-center">
            <h2>Student Management System</h2>
            <p class="mb-0">View and manage student </p>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mb-3">
            <h3>Student Records</h3>
            <button id="addStudentBtn" class="btn btn-add">
                <i class="fas fa-plus-circle"></i> Add New Student
            </button>
        </div>

        <!-- Filters -->
        <div class="card filter-card">
            <div class="card-body">
                <h5 class="card-title">Filter Students</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="feeFilter">Fee Status</label>
                            <select id="feeFilter" class="form-control">
                                <option value="">All Fees</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="classFilter">Class</label>
                            <input type="text" id="classFilter" class="form-control" placeholder="e.g., ND2, HND1">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="filterBtn" class="btn filter-btn w-100">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="statusMessage" class="alert alert-info d-none"></div>

        <!-- Student Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover" id="studentsTable">
                    <thead>
                        <tr>
                            <th>Reg No</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Fee Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center py-4">No data available. Click "Apply Filters" to load
                                records.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for updating fee -->
    <div class="modal fade" id="feeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Fee Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Update fee status for: <strong id="studentName"></strong></p>
                    <select id="newFeeStatus" class="form-control">
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button id="saveFeeBtn" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding/editing student -->
    <div class="modal fade" id="studentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentModalTitle">Add New Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="studentForm">
                        <input type="hidden" id="studentId">
                        <div class="form-group">
                            <label for="regNumber">Registration Number</label>
                            <input type="text" class="form-control" id="regNumber" required>
                        </div>
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="class">Class</label>
                            <input type="text" class="form-control" id="class" required>
                        </div>
                        <div class="form-group">
                            <label for="feeStatus">Fee Status</label>
                            <select class="form-control" id="feeStatus">
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button id="saveStudentBtn" class="btn btn-primary d-none">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Add Student
                    </button>
                    <button id="updateStudentBtn" class="btn btn-primary d-none">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Update Student
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete student: <strong id="deleteStudentName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button id="confirmDeleteBtn" class="btn btn-danger">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Delete Student
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const apiBase = 'http://api.istudyglobe.com';

        let selectedStudentId = null;
        let selectedStudentName = null;

        // Show/hide loading state
        function setLoading(loading) {
            if (loading) {
                $('#studentsTable tbody').html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading student data...</p>
                    </td>
                </tr>
            `);
            }
        }

        // Show status message
        function showMessage(message, type = 'info') {
            const alert = $('#statusMessage');
            alert.removeClass('d-none alert-info alert-success alert-danger');
            alert.addClass(`alert-${type}`).html(`<span>${message}</span>`);

            if (type === 'success') {
                setTimeout(() => {
                    alert.addClass('d-none');
                }, 5000);
            }
        }

        // Hide status message
        function hideMessage() {
            $('#statusMessage').addClass('d-none');
        }

        // error message extraction
        function getErrorMessage(xhr, status, error, defaultMessage) {
            if (xhr.responseJSON) {
                return xhr.responseJSON.error || xhr.responseJSON.message || JSON.stringify(xhr.responseJSON);
            }

            if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    return response.error || response.message || xhr.responseText;
                } catch (e) {
                    if (xhr.responseText.trim() !== '') {
                        return xhr.responseText;
                    }
                }
            }

            if (xhr.status === 404) {
                return 'Resource not found. Please check the URL.';
            } else if (xhr.status === 500) {
                return 'Server error. Please try again later.';
            } else if (xhr.status === 403 || xhr.status === 401) {
                return 'Authentication failed. Please check your credentials.';
            } else if (xhr.status === 400) {
                return 'Bad request. Please check your input data.';
            } else if (xhr.status === 0) {
                return 'Network error. Please check your internet connection.';
            }

            return `${defaultMessage} (Status: ${xhr.status} ${status})`;
        }

        // Fetch students from API
        function loadStudents() {
            setLoading(true);


            const feeStatus = $('#feeFilter').val();
            const classFilter = $('#classFilter').val().trim();

            let params = {};
            if (feeStatus) params.fee_status = feeStatus;
            if (classFilter) params.class = classFilter;

            const queryString = Object.keys(params)
                .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
                .join('&');

            const url = `${apiBase}/students${queryString ? '?' + queryString : ''}`;

            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {
                    const tbody = $('#studentsTable tbody');
                    tbody.empty();

                    if (!data || data.length === 0) {
                        tbody.append('<tr><td colspan="7" class="text-center py-4">No records found with the current filters</td></tr>');
                        showMessage('No student records found with the current filters.', 'info');
                        return;
                    }

                    data.forEach(student => {
                        const statusClass = student.fee_status === 'paid' ? 'status-paid' : 'status-unpaid';
                        tbody.append(`
                        <tr>
                            <td>${student.reg_number || 'N/A'}</td>
                            <td>${student.first_name || 'N/A'}</td>
                            <td>${student.last_name || 'N/A'}</td>
                            <td>${student.email || 'N/A'}</td>
                            <td>${student.class || 'N/A'}</td>
                            <td>
                                <span class="status-cell ${statusClass}" data-id="${student.id}" data-name="${student.first_name} ${student.last_name}">
                                    ${student.fee_status ? student.fee_status.charAt(0).toUpperCase() + student.fee_status.slice(1) : 'N/A'}
                                </span>
                            </td>
                            <td class="action-buttons">
                                <button class="btn btn-sm btn-primary edit-student action-btn" data-id="${student.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-student action-btn" data-id="${student.id}" data-name="${student.first_name} ${student.last_name}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                    });

                    showMessage(`Loaded ${data.length} student records.`, 'success');
                },
                error: function (xhr, status, error) {
                    const errorMessage = getErrorMessage(xhr, status, error, 'Error loading students');
                    $('#studentsTable tbody').html(`
                    <tr>
                        <td colspan="7" class="text-center text-danger py-4">${errorMessage}</td>
                    </tr>
                `);
                    showMessage(errorMessage, 'danger');
                }
            });
        }

        // Open add student modal
        $('#addStudentBtn').on('click', function () {
            $('#studentModalTitle').text('Add New Student');
            $('#studentForm')[0].reset();
            $('#studentId').val('');
            $('#saveStudentBtn').removeClass('d-none');
            $('#updateStudentBtn').addClass('d-none');
            $('#studentModal').modal('show');
        });

        // Add new student
        $('#saveStudentBtn').on('click', function () {
            const btn = $(this);
            const spinner = btn.find('.spinner-border');

            if (!$('#studentForm')[0].checkValidity()) {
                $('#studentForm').find(':input:visible').each(function () {
                    if (!this.checkValidity()) {
                        $(this).addClass('is-invalid');
                    }
                });
                return;
            }

            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            const studentData = {
                reg_number: $('#regNumber').val(),
                first_name: $('#firstName').val(),
                last_name: $('#lastName').val(),
                email: $('#email').val(),
                class: $('#class').val(),
                fee_status: $('#feeStatus').val()
            };

            $.ajax({
                url: `${apiBase}/students`,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(studentData),
                success: function (response) {
                    $('#studentModal').modal('hide');
                    showMessage(`Student ${response.first_name} ${response.last_name} added successfully!`, 'success');

                    setTimeout(() => {
                        loadStudents();
                    }, 1500);
                },
                error: function (xhr, status, error) {
                    const errorMessage = getErrorMessage(xhr, status, error, 'Failed to add student');
                    showMessage(errorMessage, 'danger');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Edit student
        $(document).on('click', '.edit-student', function () {
            const studentId = $(this).data('id');

            $.ajax({
                url: `${apiBase}/students/${studentId}`,
                method: 'GET',
                success: function (student) {
                    $('#studentModalTitle').text('Edit Student');
                    $('#studentId').val(student.id);
                    $('#regNumber').val(student.reg_number);
                    $('#firstName').val(student.first_name);
                    $('#lastName').val(student.last_name);
                    $('#email').val(student.email);
                    $('#class').val(student.class);
                    $('#feeStatus').val(student.fee_status);

                    $('#saveStudentBtn').addClass('d-none');
                    $('#updateStudentBtn').removeClass('d-none');
                    $('#studentModal').modal('show');
                },
                error: function (xhr, status, error) {
                    const errorMessage = getErrorMessage(xhr, status, error, 'Failed to load student details');
                    showMessage(errorMessage, 'danger');
                }
            });
        });

        // Update student
        $('#updateStudentBtn').on('click', function () {
            const btn = $(this);
            const spinner = btn.find('.spinner-border');
            const studentId = $('#studentId').val();

            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            const studentData = {
                reg_number: $('#regNumber').val(),
                first_name: $('#firstName').val(),
                last_name: $('#lastName').val(),
                email: $('#email').val(),
                class: $('#class').val(),
                fee_status: $('#feeStatus').val()
            };

            $.ajax({
                url: `${apiBase}/students/${studentId}`,
                method: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(studentData),
                success: function (response) {
                    $('#studentModal').modal('hide');
                    showMessage(`Student ${response.first_name} ${response.last_name} updated successfully!`, 'success');

                    setTimeout(() => {
                        loadStudents();
                    }, 1500);
                },
                error: function (xhr, status, error) {
                    const errorMessage = getErrorMessage(xhr, status, error, 'Failed to update student');
                    showMessage(errorMessage, 'danger');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Delete student
        $(document).on('click', '.delete-student', function () {
            selectedStudentId = $(this).data('id');
            selectedStudentName = $(this).data('name');

            $('#deleteStudentName').text(selectedStudentName);
            $('#deleteModal').modal('show');
        });

        // Confirm delete
        $('#confirmDeleteBtn').on('click', function () {
            const btn = $(this);
            const spinner = btn.find('.spinner-border');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                url: `${apiBase}/students/${selectedStudentId}`,
                method: 'DELETE',
                success: function () {
                    $('#deleteModal').modal('hide');
                    showMessage(`Student ${selectedStudentName} deleted successfully!`, 'success');

                    setTimeout(() => {
                        loadStudents();
                    }, 1500);
                },
                error: function (xhr, status, error) {
                    const errorMessage = getErrorMessage(xhr, status, error, 'Failed to delete student');
                    showMessage(errorMessage, 'danger');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Filter button click
        $('#filterBtn').on('click', loadStudents);

        // Pressing Enter in class filter triggers filter
        $('#classFilter').on('keypress', function (e) {
            if (e.which === 13) {
                loadStudents();
            }
        });

        // Click on fee status opens modal
        $(document).on('click', '.status-cell', function () {
            selectedStudentId = $(this).data('id');
            selectedStudentName = $(this).data('name');
            const currentStatus = $(this).text().toLowerCase();

            $('#newFeeStatus').val(currentStatus);
            $('#studentName').text(selectedStudentName);
            $('#feeModal').modal('show');
        });

        // Save new fee status
        $('#saveFeeBtn').on('click', function () {
            const newStatus = $('#newFeeStatus').val();
            const btn = $(this);
            const spinner = btn.find('.spinner-border');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                url: `${apiBase}/students/${selectedStudentId}/fee`,
                method: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify({ fee_status: newStatus }),
                success: function (updatedStudent) {
                    $('#feeModal').modal('hide');
                    showMessage(`Successfully updated fee status for ${selectedStudentName} to ${newStatus}.`, 'success');

                    setTimeout(() => {
                        loadStudents();
                    }, 1500);
                },
                error: function (xhr, status, error) {
                    const errorMessage = getErrorMessage(xhr, status, error, 'Failed to update fee status');
                    showMessage(errorMessage, 'danger');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Form validation
        $('#studentForm input').on('input', function () {
            if (this.checkValidity()) {
                $(this).removeClass('is-invalid');
            }
        });

        // Initial load when page is ready
        $(document).ready(function () {
            setTimeout(loadStudents, 500);
        });
    </script>

</body>

</html>