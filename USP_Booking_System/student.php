<?php
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    // If not, send them back to the login page
    header("Location: login_page.html"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USP Equipment Booking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="student_page.css">
    <script src="Theme.js"></script>

</head>
<body class="theme-light-tilt min-h-screen flex flex-col">

    <nav style="background-color: #00313c;" class="text-white shadow-md relative z-25">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Left: Logo & Title -->
                <div class="flex items-center space-x-3">
                    <img src="USP Logo White.png" alt="USP Logo" class="h-10 w-auto">
                    <span class="text-xl font-bold tracking-wider">Booking <span class="text-usp-teal">Equipment</span></span>
                </div>
                
                <!-- Right: User Profile & Dropdown -->
                <div class="relative">
                    <button style="background-color: #00313c;" id="user-menu-button" onclick="toggleUserDropdown()" class="flex items-center space-x-2 bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium focus:outline-none transition">
                        <!-- User Icon -->
                        <svg class="w-5 h-5 text-usp-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        
                        <!-- UPDATED: Displays Full Name instead of Username -->
                        <span><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?> </span>
                        
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-md shadow-lg py-1 z-50 border">
                        <div class="px-4 py-2 border-b text-xs text-gray-500 font-semibold">Preferences</div>
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-700">Theme</span>
                            <select style="background-color: #00313c;" id="theme-selector" class="bg-usp-green text-white text-xs rounded px-2 py-1 outline-none border-none cursor-pointer">
                                <option value="theme-light-tilt">Light Tilt</option>
                                <option value="theme-white">White</option>
                                <option value="theme-dark-tilt">Dark Tilt</option>
                            </select>
                        </div>
                        <div class="border-t my-1"></div>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main id="app-container" class="flex-grow flex items-start justify-center p-4 pt-10">       

        <!-- STUDENT VIEW -->
        <div id="view-student" class="w-full max-w-6xl h-full flex-col">
            <!-- Hold Warning Banner -->
            <div id="hold-banner" class="hidden w-full max-w-6xl mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md flex justify-between items-center">
                <div>
                    <p class="font-bold">You have a hold</p>
                    <p class="text-sm">Your account has an outstanding late return penalty. You must settle your fine under "My Bookings" before you can book new equipment.</p>
                </div>
            </div>

            <!-- Portal Card Wrapper (Encloses all tabs) -->
            <div class="portal-card rounded-lg p-6 mb-6 min-h-[600px]">
                <h2 class="text-2xl font-bold mb-4">Student Dashboard</h2>
                
                <div class="flex space-x-6 border-b border-gray-200 mb-6">
                    <button class="tab-btn active pb-2" data-target="student-home">Home</button>
                    <button class="tab-btn pb-2" data-target="student-browse">Browse Equipment</button>
                    <button class="tab-btn pb-2" data-target="student-history">My Bookings</button>
                </div>

                <!-- HOME TAB -->
                <div id="student-home" class="tab-content space-y-6 text-gray-700">
                    <div style="background-color: #00313c;" class="bg-usp-dark text-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-bold mb-2 text-usp-teal">Welcome to the USP Equipment Booking Portal</h3>
                        <p class="text-sm text-gray-200 leading-relaxed">
                            This platform is designed to provide students with seamless access to academic and multimedia resources, including laptops, cameras, projectors, audio equipment, and accessories for campus studies and projects.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- How to Borrow -->
                        <div class="bg-gray-50 p-5 rounded-lg border shadow-sm">
                            <h4 class="font-bold text-usp-green mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                How to Borrow Equipment
                            </h4>
                            <ul class="text-sm space-y-2 list-disc list-inside text-gray-600">
                                <li>Navigate to the <strong>Browse Equipment</strong> tab.</li>
                                <li>Select your required category, date, and time slot to check real-time availability.</li>
                                <li>Click <strong>Info</strong> to view specifications and optional included gear (like mice or headphones for laptops).</li>
                                <li>Submit your booking request and collect your items from the counter by presenting your valid <strong>Student ID card</strong>.</li>
                            </ul>
                        </div>

                        <!-- Rules to Avoid Penalties -->
                        <div class="bg-gray-50 p-5 rounded-lg border shadow-sm">
                            <h4 class="font-bold text-red-600 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Rules & Penalty Guidelines
                            </h4>
                            <ul class="text-sm space-y-2 list-disc list-inside text-gray-600">
                                <li><strong>Duration:</strong> Maximum booking duration is strictly 2 hours per session.</li>
                                <li><strong>Returns:</strong> Items must be returned <strong>5 minutes before</strong> your time expires. A 5-minute grace period applies before late fines trigger.</li>
                                <li><strong>Late Penalties:</strong> Returning an item late results in a $5.00 fine and places an automatic hold on your account.</li>
                                <li><strong>Cancellations:</strong> Must be made at least <strong>1 hour prior</strong> to your scheduled time, or a $5.00 late cancellation fee will apply.</li>
                                <li><strong>Account Holds:</strong> Outstanding fines must be paid at the physical equipment counter to lift your hold before you can make new bookings.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Support & Contact -->
                    <div style="background-color: #03fd8848;" class="bg-usp-teal bg-opacity-10 border-l-4 border-usp-teal p-5 rounded-lg">
                        <h4 class="font-bold text-usp-teal mb-2">Need Assistance or Experiencing Technical Issues?</h4>
                        <p class="text-sm text-gray-600 mb-3">If you encounter issues with your reservation, damaged hardware, or require assistance clearing a hold, reach out to our team:</p>
                        <div class="text-sm space-y-1 text-gray-700 font-medium">
                            <p>📧 General Equipment Support: <a href="mailto:equipment.support@usp.ac.fj" class="text-usp-teal underline">equipment.support@usp.ac.fj</a></p>
                        </div>
                    </div>
                </div>

                <!-- BROWSE EQUIPMENT TAB -->
                <div id="student-browse" class="tab-content hidden">
                    <div class="flex flex-col sm:flex-row gap-4 mb-6 bg-gray-50 p-4 rounded border">
                        <div class="flex-grow">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Category</label>
                            <select id="filter-category" class="w-full px-3 py-2 border rounded-md outline-none bg-white">
                                <option value="all">All Categories</option>
                                <option value="Laptop">Laptops</option>
                                <option value="Camera">Cameras</option>
                                <option value="Headphones">Headphone</option>
                                <option value="Mouse">Mouse</option>
                                <option value="Projector">Projector</option>
                                <option value="Speakers">Speaker</option>
                            </select>
                        </div>
                        <div class="flex-grow">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Check Date</label>
                            <input type="date" id="filter-date" class="w-full px-3 py-2 border rounded-md outline-none bg-white">
                        </div>
                        <div class="flex-grow">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Check Time Slot Status</label>
                            <select id="filter-timeslot" class="w-full px-3 py-2 border rounded-md outline-none bg-white cursor-pointer">
                                <option value="all">All Slots (Overview)</option>
                            </select>
                        </div>                    
                        <div class="flex items-end">
                            <button style="background-color: #008c95;" onclick="renderEquipment()" class="w-full sm:w-auto bg-usp-teal text-white font-bold px-6 py-2 rounded-md hover:bg-teal-600 transition">
                                Check Availability
                            </button>
                        </div>
                    </div>
                    <div id="equipment-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Equipment cards injected here -->
                    </div>
                </div>

                <!-- MY BOOKINGS TAB -->
                <div id="student-history" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penalties</th>
                                </tr>
                            </thead>
                            <tbody id="student-bookings-table" class="bg-white divide-y divide-gray-200">
                                <!-- Bookings injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> <!-- Properly closes .portal-card enclosing all tabs -->
        </div>

    <!-- Dedicated Equipment Info Modal -->
    <div id="info-modal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-lg max-w-lg w-full overflow-hidden shadow-2xl transform transition-all">
            <div class="relative h-48 bg-gray-100">
                <img id="info-modal-img" src="" alt="" class="w-full h-full object-cover">
                <button onclick="closeInfoModal()" class="absolute top-3 right-3 bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-200 font-bold shadow-md">&times;</button>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 id="info-modal-title" class="text-2xl font-bold text-gray-900"></h3>
                        <p id="info-modal-category" class="text-sm text-gray-500 uppercase tracking-wide mt-1"></p>
                    </div>
                    <span id="info-modal-status" class="px-3 py-1 rounded text-sm font-bold shadow-sm"></span>
                </div>
                
                <!-- Description & Included Accessories -->
                <div class="mb-6 space-y-3">
                    <div>
                        <h4 class="text-xs font-bold text-gray-700 uppercase mb-1">Specifications & Description</h4>
                        <p id="info-modal-desc" class="text-sm text-gray-600 bg-gray-50 p-3 rounded border">No additional details provided.</p>
                    </div>
                    
                    <div class="bg-usp-teal bg-opacity-10 border-l-4 border-usp-teal p-3 rounded text-xs text-gray-700">
                        <p class="font-bold text-usp-teal mb-1">Included Accessories / Optional Gear:</p>
                        <p id="info-modal-extras">• Laptops come with a mouse and headphones upon request.</p>
                        <p>• Please handle all accessories with care and return them alongside the main equipment.</p>
                    </div>
                </div>

                <!-- Book Button inside Info Modal -->
                <button style="background-color: #008c95;" id="info-modal-book-btn" class="w-full py-3 rounded text-white font-bold transition">Proceed to Book</button>
            </div>
        </div>
    </div>        

    <!-- Equipment Details & Booking Modal -->
    <div id="equipment-modal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-lg max-w-lg w-full overflow-hidden shadow-2xl transform transition-all">
            <div class="relative h-48 bg-gray-100">
                <img id="modal-img" src="" alt="" class="w-full h-full object-cover">
                <button onclick="closeModal()" class="absolute top-3 right-3 bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-200 font-bold shadow-md">&times;</button>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 id="modal-title" class="text-2xl font-bold text-gray-900"></h3>
                        <p id="modal-category" class="text-sm text-gray-500 uppercase tracking-wide mt-1"></p>
                    </div>
                    <span id="modal-status" class="px-3 py-1 rounded text-sm font-bold shadow-sm"></span>
                </div>
                
                <!-- Booking Form -->
                <div id="modal-booking-section">
                    <div style="background-color: #80ffb95e;" class="bg-usp-green bg-opacity-10 border-l-4 border-usp-green p-3 mb-4 text-xs text-gray-700">
                        <p class="font-bold text-usp-green mb-1">Important Instructions & Policies:</p>
                        <p>• Please bring your valid <strong>Student ID card</strong> when collecting equipment.</p>
                        <p>• Items must be returned <strong>5 minutes before</strong> your time expires.</p>
                        <p>• Cancellations must be made at least <strong>1 hour prior</strong> to your scheduled time, or a $5.00 penalty fee will apply.</p>
                    </div>

                    <p class="text-gray-700 mb-2 text-sm font-semibold" id="modal-slot-date-label">Select up to 2 time slots:</p>
                    
                    <!-- Date picker to switch days -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Choose Date</label>
                        <input type="date" id="book-date" onchange="fetchAvailableSlots()" class="w-full border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal" required>
                    </div>

                    <!-- Interactive Clickable Time Slots Grid (Matching your layout design) -->
                    <div id="slots-grid-container" class="grid grid-cols-2 gap-3 mb-6 max-h-60 overflow-y-auto p-1">
                        <!-- Dynamic slot buttons inject here -->
                    </div>

                    <!-- Hidden inputs holding selected slot IDs (Max 2) -->
                    <div id="selected-slot-inputs"></div>

                    <button style="background-color: #008c95;" id="modal-book-btn" class="w-full py-3 rounded text-white font-bold transition">Confirm Selected Slots</button>
                </div>

                <!-- Success Message (Hidden by Default) -->
                <div id="modal-success-msg" class="hidden text-center py-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-500 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Request Succesful!</h4>
                    <p class="text-sm text-gray-500">Your equipment booking has been successfully requested.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- AI CHATBOT -->
    <!-- Chat Toggle Button (Fixed to bottom right) -->
    <button id="chatbot-toggle" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; background-color: #0d9488; color: white; border: none; width: 56px; height: 56px; border-radius: 50%; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
    </button>

    <!-- Chat Window Popup (Centered initially, detached from the button) -->
    <div id="chatbot-window" class="hidden" style="position: fixed; z-index: 10000; width: 350px; height: 420px; background: white; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); flex-direction: column; overflow: hidden;">
        
        <!-- Drag Handle (Header) -->
        <div id="chatbot-header" style="background-color: #008c95; color: white; padding: 12px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; cursor: grab;">
            <span>USP AI Assistant</span>
            <button id="chatbot-close" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer; font-weight: bold;">&times;</button>
        </div>
        
        <div id="chat-messages" style="flex-grow: 1; padding: 12px; overflow-y: auto; background-color: #f8fafc; font-size: 14px; display: flex; flex-direction: column; gap: 10px;">
            <div style="background-color: #e2e8f0; color: #1e293b; padding: 8px 12px; border-radius: 6px; max-width: 85%; align-self: flex-start;">
                Bula! <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>, I am the USP Equipment Assistant. Ask me about our available gear!
            </div>
        </div>
        
        <div style="padding: 10px; border-top: 1px solid #e2e8f0; background: white; display: flex;">
            <input type="text" id="chat-input" placeholder="Type your question..." style="flex-grow: 1; padding: 8px; border: 1px solid #030303; border-radius: 4px 0 0 4px; outline: none; font-size: 14px;">
            <button id="chat-send" style="background-color: #008c95; color: white; border: none; padding: 8px 14px; border-radius: 0 4px 4px 0; font-size: 14px; cursor: pointer;">Send</button>
        </div>
    </div>

<script>
let equipmentData = [];
let myBookings = [];
let currentModalEquipmentId = null;
let selectedSlotIds = [];
const maxAllowedSlots = 2;
let hasAccountHold = false;

// Tab Switching Logic
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const targetId = e.target.getAttribute('data-target');
        
        Array.from(e.target.parentElement.children).forEach(sibling => {
            sibling.classList.remove('active', 'border-usp-teal', 'text-usp-teal');
        });
        e.target.classList.add('active', 'border-usp-teal', 'text-usp-teal');
        
        const view = e.target.closest('.portal-card');
        view.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
        document.getElementById(targetId).classList.remove('hidden');
    });
});

// Toggle User Profile Dropdown Menu
function toggleUserDropdown() {
    const dropdown = document.getElementById('user-dropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
window.addEventListener('click', function(e) {
    const button = document.getElementById('user-menu-button');
    const dropdown = document.getElementById('user-dropdown');
    if (!button.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

// Automatically load and refresh time slot availability when the filter date changes
async function loadFilterTimeSlots() {
    const dateVal = document.getElementById('filter-date').value || new Date().toISOString().split('T')[0];
    const slotSelect = document.getElementById('filter-timeslot');
    
    if (!slotSelect) return;
    slotSelect.innerHTML = '<option value="all">Loading slots...</option>';

    try {
        const res = await fetch(`api_get_slots.php?date=${dateVal}`);
        const slots = await res.json();
        
        slotSelect.innerHTML = '<option value="all">All Slots (Overview)</option>';
        
        if (slots && slots.length > 0) {
            slots.forEach(slot => {
                let statusText = '🟢 Available';
                if (slot.is_blocked == 1) statusText = '🔴 Blocked by Admin';
                else if (slot.is_booked == 1) statusText = '🔴 Taken / Booked';

                slotSelect.innerHTML += `
                    <option value="${slot.id}" ${slot.is_booked == 1 || slot.is_blocked == 1 ? 'disabled class="text-gray-400"' : ''}>
                        ${slot.formatted_time} — [${statusText}]
                    </option>
                `;
            });
        }
    } catch (e) {
        slotSelect.innerHTML = '<option value="all">Failed to load slots</option>';
    }
}

// Fetch Equipment
async function loadEquipment() {
    try {
        const response = await fetch('api_equipment.php');
        equipmentData = await response.json();
        renderEquipment(); 
    } catch (error) {
        console.error("Error fetching equipment:", error);
        const grid = document.getElementById('equipment-grid');
        if (grid) grid.innerHTML = '<p class="text-red-500">Failed to load inventory.</p>';
    }
}

// Update loadBookings to parse the hold status
async function loadBookings() {
    try {
        const response = await fetch('api_my_bookings.php');
        const data = await response.json();
        myBookings = data.bookings || [];
        hasAccountHold = data.has_hold;
        
        const banner = document.getElementById('hold-banner');
        if (banner) {
            if (hasAccountHold) {
                banner.classList.remove('hidden');
            } else {
                banner.classList.add('hidden');
            }
        }

        renderBookings();
        renderEquipment(); 
    } catch (error) {
        console.error("Error fetching bookings:", error);
    }
}

// Assign images based on category or custom upload
function getImageUrl(category, customUrl) {
    if (customUrl && customUrl.trim() !== '') return customUrl;
    if (category === 'Laptop') return 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500&q=80';
    if (category === 'Camera') return 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=500&q=80';
    if (category === 'Audio' || category === 'Speakers') return 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=500&q=80';
    if (category === 'Mouse') return 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=500&q=80';
    if (category === 'Projector') return 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500&q=80';
    return 'https://images.unsplash.com/photo-1555664424-778a1e5e1b48?w=500&q=80'; 
}

// Render Equipment Cards
function renderEquipment() {
    const grid = document.getElementById('equipment-grid');
    const filterCat = document.getElementById('filter-category');
    if (!grid || !filterCat) return;

    const filterCatVal = filterCat.value;
    grid.innerHTML = ''; 
    
    const filteredData = equipmentData.filter(eq => {
        return filterCatVal === 'all' || eq.category === filterCatVal;
    });

    if (filteredData.length === 0) {
        grid.innerHTML = '<p class="text-gray-500 col-span-3 text-center py-8">No equipment found.</p>';
        return;
    }

    filteredData.forEach(eq => {
        const available = parseInt(eq.qty) > 0;
        const imgUrl = getImageUrl(eq.category, eq.image_url);
        
        grid.innerHTML += `
            <div class="bg-white border rounded-lg overflow-hidden shadow-sm flex flex-col transition hover:shadow-md">
                <div class="h-48 bg-gray-100 overflow-hidden relative">
                    <img src="${imgUrl}" alt="${eq.name}" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="text-xs px-2 py-1 rounded shadow-sm font-bold ${available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${available ? eq.qty + ' in Stock' : 'Out of Stock'}
                        </span>
                    </div>
                </div>
                <div class="p-4 flex-grow flex flex-col justify-between">
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800 text-lg">${eq.name}</h3>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mt-1">${eq.category}</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="openInfoModal('${eq.id}')" class="w-1/3 py-2 rounded text-sm font-medium transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                            Info
                        </button>
                        <button style="background-color: #008c95;" onclick="openModal('${eq.id}')" ${!available || hasAccountHold ? 'disabled' : ''} class="w-2/3 py-2 rounded text-sm font-medium transition ${available && !hasAccountHold ? 'bg-usp-dark text-white hover:bg-green-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed'}">
                            ${hasAccountHold ? 'Account on Hold' : (available ? 'Book' : 'Unavailable')}
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
}

const filterCategoryElem = document.getElementById('filter-category');
if (filterCategoryElem) {
    filterCategoryElem.addEventListener('change', renderEquipment);
}

// Open Dedicated Info Modal
function openInfoModal(eqId) {
    const eq = equipmentData.find(e => e.id == eqId);
    if (!eq) return;
    
    const available = parseInt(eq.qty) > 0;
    const imgUrl = getImageUrl(eq.category, eq.image_url);
    
    document.getElementById('info-modal-img').src = imgUrl;
    document.getElementById('info-modal-title').textContent = eq.name;
    document.getElementById('info-modal-category').textContent = eq.category;
    document.getElementById('info-modal-desc').textContent = eq.description || 'Standard university equipment item.';
    
    const extrasBox = document.getElementById('info-modal-extras');
    if (eq.category === 'Laptop') {
        extrasBox.textContent = '• Laptops come with a mouse and headphones upon request.';
    } else if (eq.category === 'Camera') {
        extrasBox.textContent = '• Cameras include a standard lens, battery pack, and strap upon request.';
    } else {
        extrasBox.textContent = '• Comes with standard required cables and power adapters.';
    }

    const statusBadge = document.getElementById('info-modal-status');
    statusBadge.textContent = available ? eq.qty + ' Available' : 'Out of Stock';
    statusBadge.className = `px-3 py-1 rounded text-sm font-bold shadow-sm ${available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
    
    const bookBtn = document.getElementById('info-modal-book-btn');
    bookBtn.textContent = hasAccountHold ? 'Account on Hold' : (available ? 'Proceed to Book' : 'Unavailable');
    bookBtn.className = `w-full py-3 rounded text-white font-bold transition ${available && !hasAccountHold ? 'bg-usp-dark hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'}`;
    bookBtn.disabled = !available || hasAccountHold;
    bookBtn.onclick = () => {
        closeInfoModal();
        openModal(eq.id);
    };
    
    document.getElementById('info-modal').classList.remove('hidden');
}

function closeInfoModal() {
    document.getElementById('info-modal').classList.add('hidden');
}

async function fetchAvailableSlots() {
    const dateInput = document.getElementById('book-date');
    const container = document.getElementById('slots-grid-container');
    if (!dateInput || !container) return;

    const dateVal = dateInput.value;
    container.innerHTML = '<p class="text-xs text-gray-500 col-span-2">Loading available time slots...</p>';
    selectedSlotIds = [];
    updateSlotFormInputs();

    if (!currentModalEquipmentId) return;

    try {
        const res = await fetch(`api_get_slots.php?date=${dateVal}&equipment_id=${currentModalEquipmentId}`);
        const slots = await res.json();

        container.innerHTML = '';
        if (slots.length === 0) {
            container.innerHTML = '<p class="text-xs text-gray-500 col-span-2">No time slots available for this date.</p>';
            return;
        }

        slots.forEach(slot => {
            const isDisabled = slot.is_blocked == 1 || slot.is_booked == 1;
            container.innerHTML += `
                <button type="button" 
                    class="slot-btn border rounded-lg p-3 text-sm font-bold text-center transition ${isDisabled ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed line-through' : 'bg-white text-gray-800 border-gray-300 hover:border-usp-teal'}"
                    data-id="${slot.id}"
                    ${isDisabled ? 'disabled' : ''}
                    onclick="toggleStudentSlot(this, '${slot.id}')">
                    ${slot.formatted_time}
                </button>
            `;
        });
    } catch(e) {
        container.innerHTML = '<p class="text-xs text-red-500 col-span-2">Failed to load slots.</p>';
    }
}

function toggleStudentSlot(btn, slotId) {
    if (btn.classList.contains('selected')) {
        btn.classList.remove('selected', 'bg-usp-dark', 'text-white');
        btn.classList.add('bg-white', 'text-gray-800');
        selectedSlotIds = selectedSlotIds.filter(id => id !== slotId);
    } else {
        if (selectedSlotIds.length >= maxAllowedSlots) {
            alert("You can only select a maximum of 2 time slots per booking.");
            return;
        }
        btn.classList.add('selected', 'bg-usp-dark', 'text-white');
        btn.classList.remove('bg-white', 'text-gray-800');
        selectedSlotIds.push(slotId);
    }
    updateSlotFormInputs();
}

function updateSlotFormInputs() {
    const container = document.getElementById('selected-slot-inputs');
    if (!container) return;
    container.innerHTML = '';
    selectedSlotIds.forEach(id => {
        container.innerHTML += `<input type="hidden" name="slot_ids[]" value="${id}">`;
    });
}

async function openModal(eqId) {
    if (hasAccountHold) {
        alert("You cannot book equipment while your account has an active hold. Please clear your fine under 'My Bookings'.");
        return;
    }

    const eq = equipmentData.find(e => e.id == eqId);
    if (!eq) return;

    currentModalEquipmentId = eqId;

    let settings = { allow_weekends: '0', future_weeks_allowed: '1' };
    try {
        const res = await fetch('api_settings.php');
        settings = await res.json();
    } catch(e) {}

    const available = parseInt(eq.qty) > 0;
    const imgUrl = getImageUrl(eq.category, eq.image_url);

    document.getElementById('modal-booking-section').classList.remove('hidden');
    document.getElementById('modal-success-msg').classList.add('hidden');

    const today = new Date();
    const minStr = today.toISOString().split('T')[0];
    const weeksAhead = parseInt(settings.future_weeks_allowed) || 1;
    const futureLimit = new Date();
    futureLimit.setDate(today.getDate() + (weeksAhead * 7));
    const maxStr = futureLimit.toISOString().split('T')[0];

    const dateInput = document.getElementById('book-date');
    dateInput.min = minStr;
    dateInput.max = maxStr;
    dateInput.value = minStr;

    dateInput.onchange = function() {
        const selected = new Date(this.value);
        const day = selected.getDay();
        if (settings.allow_weekends === '0' && (day === 0 || day === 6)) {
            alert("Weekend bookings are currently disabled by the administrator.");
            this.value = minStr;
        }
        fetchAvailableSlots(); 
    };

    document.getElementById('modal-img').src = imgUrl;
    document.getElementById('modal-title').textContent = eq.name;
    document.getElementById('modal-category').textContent = eq.category;

    const statusBadge = document.getElementById('modal-status');
    statusBadge.textContent = available ? eq.qty + ' in Stock' : 'Out of Stock';
    statusBadge.className = `px-3 py-1 rounded text-sm font-bold shadow-sm ${available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;

    const bookBtn = document.getElementById('modal-book-btn');
    bookBtn.textContent = available ? 'Confirm Selected Slots' : 'Unavailable';
    bookBtn.className = `w-full py-3 rounded text-white font-bold transition ${available ? 'bg-usp-dark hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'}`;
    bookBtn.disabled = !available;
    bookBtn.onclick = () => confirmBooking(eq.id);

    fetchAvailableSlots(); 
    document.getElementById('equipment-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('equipment-modal').classList.add('hidden');
}

async function confirmBooking(eqId) {
    const bookDateStr = document.getElementById('book-date').value;

    if (!bookDateStr) {
        alert("Please select a date for your booking.");
        return;
    }

    if (selectedSlotIds.length === 0) {
        alert("Please select at least 1 time slot (maximum 2 slots).");
        return;
    }

    const selectedDate = new Date(bookDateStr);
    const dayOfWeek = selectedDate.getDay();
    if (dayOfWeek === 0 || dayOfWeek === 6) {
        alert("Bookings are closed on weekends. Please choose a weekday (Monday to Friday).");
        return;
    }
    
    try {
        const response = await fetch('api_book.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                equipment_id: eqId,
                use_date: bookDateStr,
                slot_ids: selectedSlotIds
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('modal-booking-section').classList.add('hidden');
            document.getElementById('modal-success-msg').classList.remove('hidden');
            
            loadEquipment(); 
            loadBookings();  
            
            setTimeout(() => { closeModal(); }, 2000);
        } else {
            alert("Error: " + result.error);
        }
    } catch(e) {
        alert("Failed to connect to the booking system.");
    }
}

function renderBookings() {
    const tbody = document.getElementById('student-bookings-table');
    if (!tbody) return;
    tbody.innerHTML = '';
    
    if (myBookings.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No bookings found.</td></tr>`;
        return;
    }

    myBookings.forEach(b => {
        let statusColor = b.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                          b.status === 'Approved' ? 'bg-green-100 text-green-800' :
                          b.status === 'Returned' ? 'bg-gray-100 text-gray-800' : 
                          b.status === 'Cancelled' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800';
        
        let fineDisplay = b.fine_amount > 0 ? `$${b.fine_amount} (${b.fine_status})` : 'None';
        if (b.fine_amount > 0 && b.fine_status === 'Unpaid') {
            fineDisplay += `<br><button onclick="payFine(${b.id})" class="mt-1 bg-red-600 text-white px-2 py-0.5 rounded text-[10px] hover:bg-red-700">Pay Fine</button>`;
        }

        let cancelAction = (b.status === 'Pending' || b.status === 'Approved') ?
            `<button onclick="cancelBooking(${b.id})" class="ml-2 bg-gray-600 text-white px-2 py-1 rounded text-xs hover:bg-gray-700">Cancel</button>` : '';

        tbody.innerHTML += `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${b.equipment_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${b.use_date}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${b.use_time}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${b.duration_hours} Hour(s)</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">${b.status}</span>
                    ${cancelAction}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold">
                    ${fineDisplay}
                </td>
            </tr>
        `;
    });
}

async function cancelBooking(bookingId) {
    if (!confirm("Are you sure you want to cancel this booking? Cancellations made less than 1 hour before scheduled time incur a $5.00 penalty fee.")) return;
    
    try {
        const response = await fetch('api_cancel_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId })
        });
        const result = await response.json();
        
        if (result.success) {
            if (result.penalised) {
                alert("Booking cancelled. Because you cancelled less than 1 hour before your scheduled time, a $5.00 penalty fee has been applied.");
            } else {
                alert("Booking successfully cancelled.");
            }
            loadBookings();
            loadEquipment();
        } else {
            alert("Error: " + result.error);
        }
    } catch(e) {
        alert("Failed to connect to cancellation service.");
    }
}

async function payFine(bookingId) {
    if (!confirm("Proceed to pay your $5.00 late/cancellation fine?")) return;
    try {
        const response = await fetch('api_pay_fine.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId })
        });
        const result = await response.json();
        if (result.success) {
            alert("Fine paid successfully! Your account hold has been lifted.");
            loadBookings();
        } else {
            alert("Error: " + result.error);
        }
    } catch(e) { 
        alert("Payment connection failed."); 
    }
}

// --- Chatbot Logic ---
const chatbotToggle = document.getElementById('chatbot-toggle');
const chatbotWindow = document.getElementById('chatbot-window');
const chatbotClose = document.getElementById('chatbot-close');
const chatbotHeader = document.getElementById('chatbot-header');
const chatSend = document.getElementById('chat-send');
const chatInput = document.getElementById('chat-input');
const chatMessages = document.getElementById('chat-messages');

// 1. Draggable Toggle Icon Logic
let iconIsDragging = false;
let iconMoved = false;
let iconDragOffsetX = 0;
let iconDragOffsetY = 0;

if (chatbotToggle) {
    chatbotToggle.addEventListener('mousedown', (e) => {
        iconIsDragging = true;
        iconMoved = false; // Reset move flag on click
        const rect = chatbotToggle.getBoundingClientRect();
        iconDragOffsetX = e.clientX - rect.left;
        iconDragOffsetY = e.clientY - rect.top;
        chatbotToggle.style.cursor = 'grabbing';
        e.preventDefault(); 
    });

    document.addEventListener('mousemove', (e) => {
        if (!iconIsDragging) return;
        iconMoved = true; // If the mouse moves, it's a drag, not a click
        
        let newX = e.clientX - iconDragOffsetX;
        let newY = e.clientY - iconDragOffsetY;
        
        const winWidth = window.innerWidth;
        const winHeight = window.innerHeight;
        const iconSize = 56; // Width/height of the button
        
        // Keep icon inside screen bounds
        newX = Math.max(0, Math.min(newX, winWidth - iconSize));
        newY = Math.max(0, Math.min(newY, winHeight - iconSize));
        
        // Clear the hardcoded CSS bottom/right to allow free movement
        chatbotToggle.style.right = 'auto';
        chatbotToggle.style.bottom = 'auto';
        chatbotToggle.style.left = newX + 'px';
        chatbotToggle.style.top = newY + 'px';
    });

    document.addEventListener('mouseup', () => {
        if (iconIsDragging) {
            iconIsDragging = false;
            chatbotToggle.style.cursor = 'pointer';
        }
    });

    // 2. Open Chatbot Window (Anchored to the Icon)
    chatbotToggle.addEventListener('click', (e) => {
        // Prevent window from opening if the user was just dragging the icon
        if (iconMoved) {
            e.preventDefault();
            return;
        }
        
        chatbotWindow.classList.remove('hidden');
        chatbotWindow.style.display = 'flex';
        
        // Calculate dynamic popup position based on where the icon currently is
        const iconRect = chatbotToggle.getBoundingClientRect();
        const winWidth = window.innerWidth;
        const winHeight = window.innerHeight;
        const chatWidth = 350; 
        const chatHeight = 420;
        
        // Position the window just above and aligned to the left of the icon
        let newLeft = iconRect.left - chatWidth + iconRect.width;
        let newTop = iconRect.top - chatHeight - 15;
        
        // Fallback checks to ensure the window doesn't get pushed off-screen 
        // if the icon was moved to the extreme top or left edge
        newLeft = Math.max(10, Math.min(newLeft, winWidth - chatWidth - 10));
        newTop = Math.max(10, Math.min(newTop, winHeight - chatHeight - 10));
        
        chatbotWindow.style.left = newLeft + 'px';
        chatbotWindow.style.top = newTop + 'px';
    });
}

// 3. Close Chatbot
if (chatbotClose && chatbotWindow) {
    chatbotClose.addEventListener('click', () => {
        chatbotWindow.classList.add('hidden');
        chatbotWindow.style.display = 'none';
    });
}

// 4. Draggable Chat Window Header Logic
let winIsDragging = false;
let winDragOffsetX = 0;
let winDragOffsetY = 0;

if (chatbotHeader) {
    chatbotHeader.addEventListener('mousedown', (e) => {
        winIsDragging = true;
        chatbotHeader.style.cursor = 'grabbing';
        
        const rect = chatbotWindow.getBoundingClientRect();
        winDragOffsetX = e.clientX - rect.left;
        winDragOffsetY = e.clientY - rect.top;
        
        e.preventDefault(); 
    });
}

document.addEventListener('mousemove', (e) => {
    if (!winIsDragging) return;
    
    let newX = e.clientX - winDragOffsetX;
    let newY = e.clientY - winDragOffsetY;
    
    const winWidth = window.innerWidth;
    const winHeight = window.innerHeight;
    const chatWidth = 350; 
    const chatHeight = 420;
    
    // Keep window inside screen bounds
    newX = Math.max(0, Math.min(newX, winWidth - chatWidth));
    newY = Math.max(0, Math.min(newY, winHeight - chatHeight));
    
    chatbotWindow.style.left = newX + 'px';
    chatbotWindow.style.top = newY + 'px';
});

document.addEventListener('mouseup', () => {
    if (winIsDragging) {
        winIsDragging = false;
        chatbotHeader.style.cursor = 'grab';
    }
});

// RESTORED: The actual messaging logic that got deleted
function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

async function sendChatMessage() {
    if (!chatInput || !chatMessages) return;
    const text = chatInput.value.trim();
    if (!text) return;

    chatMessages.innerHTML += `
        <div style="background-color: #008c95; color: #ffffff; padding: 8px 12px; border-radius: 6px; max-width: 85%; align-self: flex-end; word-break: break-word;">
            ${escapeHtml(text)}
        </div>
    `;
    chatInput.value = '';
    chatMessages.scrollTop = chatMessages.scrollHeight;

    const typingId = 'typing-' + Date.now();
    chatMessages.innerHTML += `
        <div id="${typingId}" style="background-color: #e2e8f0; color: #475569; padding: 8px 12px; border-radius: 6px; max-width: 85%; align-self: flex-start; font-style: italic; font-size: 12px;">
            Assistant is typing...
        </div>
    `;
    chatMessages.scrollTop = chatMessages.scrollHeight;

    try {
        const response = await fetch('api_chatbot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        });
        const data = await response.json();
        
        const typingElem = document.getElementById(typingId);
        if (typingElem) typingElem.remove();

        chatMessages.innerHTML += `
            <div style="background-color: #e2e8f0; color: #1e293b; padding: 8px 12px; border-radius: 6px; max-width: 85%; align-self: flex-start; white-space: pre-line; word-break: break-word;">
                ${escapeHtml(data.reply)}
            </div>
        `;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    } catch (e) {
        const typingElem = document.getElementById(typingId);
        if (typingElem) typingElem.remove();
        chatMessages.innerHTML += `
            <div style="background-color: #fee2e2; color: #b91c1c; padding: 8px 12px; border-radius: 6px; max-width: 85%; align-self: flex-start;">
                Sorry, I couldn't connect to the assistant service.
            </div>
        `;
    }
}

if (chatSend) chatSend.addEventListener('click', sendChatMessage);
if (chatInput) {
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendChatMessage();
    });
}

// Initial Page Execution on DOM Load
document.addEventListener("DOMContentLoaded", () => {
    const filterDateInput = document.getElementById('filter-date');
    if (filterDateInput) {
        filterDateInput.value = new Date().toISOString().split('T')[0];
        filterDateInput.addEventListener('change', loadFilterTimeSlots);
        loadFilterTimeSlots();
    }
    loadEquipment();
    loadBookings();
});
</script>

</body>
</html>