<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USP Equipment Booking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Montserrat', 'sans-serif'] }
            }
        }
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <script src="Theme.js"></script>
    <style>
        html, body, button, input, select, textarea, table, th, td, label {
            font-family: 'Montserrat', sans-serif !important;
        }
    </style>

</head>
<body class="theme-light-tilt min-h-screen flex flex-col">

    <nav style="background-color: #00313c;" class="bg-usp-dark text-white shadow-md relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: Logo & Title -->
                <div class="flex items-center space-x-3">
                    <img src="USP Logo White.png" alt="USP Logo" class="h-10 w-auto">
                    <span class="text-xl font-bold tracking-wider">Booking <span class="text-usp-teal">Equipment</span></span>
                </div>
                <div class="flex items-center space-x-4">
                    <select style="background-color: #00313c;" id="theme-selector" class="bg-usp-green text-white text-sm rounded px-2 py-1 outline-none border-none cursor-pointer">
                        <option value="theme-light-tilt">Light Tilt</option>
                        <option value="theme-white">White</option>
                        <option value="theme-dark-tilt">Dark Tilt</option>
                    </select>
                    <div id="nav-user-info" class="hidden items-center space-x-4">
                        <span id="nav-username" class="text-sm font-medium"></span>
                        <button id="btn-logout" class="text-sm bg-red-600 hover:bg-red-700 px-3 py-1 rounded transition">Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main id="app-container" class="flex-grow flex items-center justify-center p-4">
        
        <!-- LOGIN VIEW -->
        <div id="view-login" class="portal-card w-full max-w-md rounded-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-usp-green mb-2">Portal Login</h1>
                <p class="text-sm text-gray-500">The University of the South Pacific</p>
            </div>
            
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
                echo '<span class="block sm:inline">' . $_SESSION['error'] . '</span>';
                echo '</div>';
                unset($_SESSION['error']); // Clear the error after displaying it
            }
            ?>
            
            <form action="login.php" method="POST" class="space-y-5" onsubmit="return validateDomain(event)">
                
                <!-- Domain Selection Tabs -->
                <div>
                    <label class="block text-sm font-medium mb-2">Domain Selection</label>
                    <div class="flex border-b border-gray-200">
                        <button type="button" onclick="setRole('student')" id="tab-student" class="flex-1 pb-2 text-center font-bold text-usp-teal border-b-2 border-usp-teal transition">Student</button>
                        <button type="button" onclick="setRole('admin')" id="tab-admin" class="flex-1 pb-2 text-center font-medium text-gray-500 transition border-b-2 border-transparent hover:text-gray-700">Staff / Admin</button>
                    </div>
                    <!-- Hidden input to pass selected role to backend -->
                    <input type="hidden" name="role" id="selected-role" value="student">
                </div>

                <div>
                    <label id="username-label" class="block text-sm font-medium mb-1">Student ID (Username)</label>
                    <input type="text" name="username" id="login-username" placeholder="" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-usp-teal outline-none transition">
                    <p id="username-error" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" id="login-password" placeholder="" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-usp-teal outline-none transition">
                </div>
                <button style="background-color: #008c95;" type="submit" class="w-full bg-usp-green hover:bg-green-800 text-white font-bold py-2 px-4 rounded transition flex justify-center items-center">
                    <span id="login-btn-text">Log In</span>
                    <span id="login-loader" class="loader hidden ml-2"></span>
                </button>
            </form>
            
            <div class="mt-6 text-center text-xs text-gray-500">
                <p>Copyright &copy; 2026 USP, Fiji. All rights reserved.</p>
            </div> 
        </div>
    </main>

    <script>
        // Tab UI Update Script
        function setRole(role) {
            document.getElementById('selected-role').value = role;
            const studentTab = document.getElementById('tab-student');
            const adminTab = document.getElementById('tab-admin');
            const label = document.getElementById('username-label');
            const errorText = document.getElementById('username-error');

            if (role === 'student') {
                studentTab.className = "flex-1 pb-2 text-center font-bold text-usp-teal border-b-2 border-usp-teal transition";
                adminTab.className = "flex-1 pb-2 text-center font-medium text-gray-500 transition border-b-2 border-transparent hover:text-gray-700";
                label.textContent = "Student ID (Username)";
            } else {
                adminTab.className = "flex-1 pb-2 text-center font-bold text-usp-teal border-b-2 border-usp-teal transition";
                studentTab.className = "flex-1 pb-2 text-center font-medium text-gray-500 transition border-b-2 border-transparent hover:text-gray-700";
                label.textContent = "Staff Username";
            }
            
            // Hide any previous errors when switching tabs
            errorText.classList.add('hidden');
        }
        
        // Domain Credentials Validation Script
        function validateDomain(event) {
            const role = document.getElementById('selected-role').value;
            const username = document.getElementById('login-username').value.trim();
            const errorText = document.getElementById('username-error');
            
            // Check if input matches a Student ID format (starts with S followed by numbers)
            const isStudentIdFormat = /^S\d+$/i.test(username);

            // Error: Selected Staff but entered a Student ID
            if (role === 'admin' && isStudentIdFormat) {
                event.preventDefault(); // Stop form from submitting
                errorText.textContent = "Wrong domain selected. Please switch to the Student tab.";
                errorText.classList.remove('hidden');
                return false;
            }

            // Error: Selected Student but didn't enter a valid Student ID
            if (role === 'student' && !isStudentIdFormat) {
                event.preventDefault(); // Stop form from submitting
                errorText.textContent = "Invalid Student ID. Must start with 'S' followed by numbers.";
                errorText.classList.remove('hidden');
                return false;
            }

            return true; // Proceed to backend if validation passes
        }
    </script>
</body>
</html>