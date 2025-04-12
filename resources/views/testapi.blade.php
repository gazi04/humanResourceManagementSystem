<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Employees Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .employee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .employee-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background: white;
            transition: transform 0.2s;
        }
        .employee-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .employee-name {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .employee-detail {
            margin: 5px 0;
        }
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-inactive {
            color: red;
            font-weight: bold;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
        }
        .pagination button {
            padding: 8px 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .pagination button:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
        .loading {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #7f8c8d;
        }
        .error {
            color: #e74c3c;
            text-align: center;
            padding: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>HR Employees</h1>
        <div id="loading" class="loading">Loading HR employees...</div>
        <div id="error" class="error" style="display: none;"></div>
        <div id="employees" class="employee-grid"></div>
        <div id="pagination" class="pagination" style="display: none;">
            <button id="prev-btn">Previous</button>
            <span id="page-info">Page 1 of 1</span>
            <button id="next-btn">Next</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeesContainer = document.getElementById('employees');
            const loadingElement = document.getElementById('loading');
            const errorElement = document.getElementById('error');
            const paginationElement = document.getElementById('pagination');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const pageInfo = document.getElementById('page-info');

            let currentPage = 1;
            let totalPages = 1;

            // Fetch HR employees
            function fetchHrs(page = 1) {
                loadingElement.style.display = 'block';
                errorElement.style.display = 'none';
                employeesContainer.innerHTML = '';
                paginationElement.style.display = 'none';

                fetch(`http://localhost:8000/get/hrs?page=${page}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            displayEmployees(data.data);
                            updatePagination(data.meta);
                        } else {
                            throw new Error(data.message || 'Failed to fetch HR employees');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        errorElement.textContent = `Error: ${error.message}`;
                        errorElement.style.display = 'block';
                    })
                    .finally(() => {
                        loadingElement.style.display = 'none';
                    });
            }

            // Display employees in the grid
            function displayEmployees(employees) {
                if (employees.length === 0) {
                    employeesContainer.innerHTML = '<div class="no-results">No HR employees found</div>';
                    return;
                }

                employees.forEach(employee => {
                    const card = document.createElement('div');
                    card.className = 'employee-card';

                    const name = document.createElement('div');
                    name.className = 'employee-name';
                    name.textContent = `${employee.firstName} ${employee.lastName}`;

                    const id = document.createElement('div');
                    id.className = 'employee-detail';
                    id.innerHTML = `<strong>ID:</strong> ${employee.employeeID}`;

                    const email = document.createElement('div');
                    email.className = 'employee-detail';
                    email.innerHTML = `<strong>Email:</strong> <a href="mailto:${employee.email}">${employee.email}</a>`;

                    const phone = document.createElement('div');
                    phone.className = 'employee-detail';
                    phone.innerHTML = `<strong>Phone:</strong> ${employee.phone || 'N/A'}`;

                    const hireDate = document.createElement('div');
                    hireDate.className = 'employee-detail';
                    hireDate.innerHTML = `<strong>Hire Date:</strong> ${employee.hireDate || 'N/A'}`;

                    const jobTitle = document.createElement('div');
                    jobTitle.className = 'employee-detail';
                    jobTitle.innerHTML = `<strong>Job Title:</strong> ${employee.jobTitle || 'N/A'}`;

                    const status = document.createElement('div');
                    status.className = `employee-detail status-${employee.status.toLowerCase()}`;
                    status.innerHTML = `<strong>Status:</strong> ${employee.status}`;

                    card.appendChild(name);
                    card.appendChild(id);
                    card.appendChild(email);
                    card.appendChild(phone);
                    card.appendChild(hireDate);
                    card.appendChild(jobTitle);
                    card.appendChild(status);

                    employeesContainer.appendChild(card);
                });
            }

            // Update pagination controls
            function updatePagination(meta) {
                currentPage = meta.current_page;
                totalPages = meta.last_page;

                pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
                prevBtn.disabled = currentPage === 1;
                nextBtn.disabled = currentPage === totalPages;

                if (totalPages > 1) {
                    paginationElement.style.display = 'flex';
                }
            }

            // Pagination event listeners
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    fetchHrs(currentPage - 1);
                }
            });

            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    fetchHrs(currentPage + 1);
                }
            });

            // Initial fetch
            fetchHrs();
        });
    </script>
</body>
</html>
