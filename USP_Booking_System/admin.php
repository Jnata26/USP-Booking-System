<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If not, send them back to the login page
    header("Location: index.php"); 
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
    <script src="Theme.js"></script> 
    <link rel="stylesheet" href="admin_page2.css">

</head>
<body class="theme-light-tilt min-h-screen flex flex-col">

    <nav style="background-color: #00313c;" class="bg-usp-dark text-white shadow-md relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Left: Logo & Title -->
                <div class="flex items-center space-x-3">
                    <img src="USP Logo White.png" alt="USP Logo" class="h-10 w-auto">
                    <span class="text-xl font-bold tracking-wider">Booking <span class="text-usp-teal">Equipment</span></span>
                </div>
                
                <!-- Right: Admin Profile & Dropdown -->
                <div class="relative">
                    <button id="user-menu-button" onclick="toggleUserDropdown()" class="flex items-center space-x-2 bg-usp-dark hover:bg-opacity-90 px-3 py-2 rounded-md text-sm font-medium focus:outline-none transition">
                        <!-- User Icon -->
                        <svg class="w-5 h-5 text-usp-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-md shadow-lg py-1 z-50 border">
                        <div class="px-4 py-2 border-b text-xs text-gray-500 font-semibold">Preferences</div>
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-700">Theme</span>
                            <select style="background-color: #00313c;" id="theme-selector" class="bg-usp-dark text-white text-xs rounded px-2 py-1 outline-none border-none cursor-pointer">
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
        
        <!-- ADMIN VIEW -->
        <div id="view-admin" class="w-full max-w-6xl h-full flex-col">
            <div class="portal-card rounded-lg p-6 mb-6 min-h-[600px]">
                <h2 class="text-2xl font-bold mb-4 text-usp-green">Admin Console</h2>
                
                <div class="flex space-x-6 border-b border-gray-200 mb-6">
                    <button class="tab-btn active pb-2" data-target="admin-bookings">Manage Bookings</button>
                    <button class="tab-btn pb-2" data-target="admin-inventory">Inventory</button>
                    <button class="tab-btn pb-2" data-target="admin-settings">System Settings</button>
                </div>

                <div id="admin-bookings" class="tab-content">
                    <!-- Search Bar -->
                    <div class="mb-4">
                        <input type="text" id="admin-booking-search" placeholder="Search bookings by student, equipment, status, date..." onkeyup="filterAdminBookings()" class="w-full sm:w-80 px-3 py-2 border rounded-md text-sm outline-none focus:ring-2 focus:ring-usp-teal bg-white">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions & Penalties</th>
                                </tr>
                            </thead>
                            <tbody id="admin-bookings-table" class="bg-white divide-y divide-gray-200">
                                <!-- Admin bookings injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="admin-inventory" class="tab-content hidden">
                    <button onclick="openAddModal()" style="background-color: #00313c;" class="mb-6 bg-usp-teal text-white px-4 py-2 rounded hover:bg-teal-600 transition shadow-sm font-bold">+ Add New Equipment</button>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="admin-inventory-grid">
                        <!-- Admin inventory injected here -->
                    </div>
                </div>

<div id="admin-settings" class="tab-content hidden space-y-8">
                        <div>
                            <h3 class="text-lg font-bold mb-4 text-gray-800">Booking Schedule & Calendar Controls</h3>
                            <form id="settings-form" class="space-y-4 max-w-lg bg-gray-50 p-6 rounded border">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Weekend Bookings</label>
                                    <select id="setting-weekends" class="w-full border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal">
                                        <option value="0">Blocked (Weekdays Only)</option>
                                        <option value="1">Activated (Allow Weekends)</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Daily Start Time</label>
                                        <input type="time" id="setting-start-time" class="w-full border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Daily End Time</label>
                                        <input type="time" id="setting-end-time" class="w-full border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Future Weeks Activation Window</label>
                                    <select id="setting-future-weeks" class="w-full border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal">
                                        <option value="1">Current Week Only</option>
                                        <option value="2">Up to 2 Weeks Ahead</option>
                                        <option value="4">Up to 4 Weeks Ahead (1 Month)</option>
                                        <option value="52">Unlimited Future Weeks</option>
                                    </select>
                                </div>
                                <button style="background-color: #00313c;" type="submit" class="bg-usp-green text-white px-4 py-2 rounded hover:bg-green-700 font-bold transition text-sm">Save Settings</button>
                            </form>
                        </div>

                        <!-- ADD THIS: Admin Time Slot Blocking Control Panel -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-bold mb-4 text-gray-800">Manage & Block Time Slots</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border bg-white rounded">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time Slot</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="admin-slots-table" class="divide-y divide-gray-200 text-sm">
                                        <!-- Dynamically injected slots -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit Equipment Modal -->
    <div id="add-equipment-modal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div class="portal-card rounded-lg max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl transform transition-all">
            <!-- Modal Header -->
            <div style="background-color: #00313c;" class="text-white p-4 flex justify-between items-center flex-shrink-0">
                <h3 id="modal-title-text" class="font-bold text-lg">Add New Equipment</h3>
                <button onclick="closeAddModal()" class="text-white hover:text-gray-300 font-bold text-xl">&times;</button>
            </div>
            
            <!-- Scrollable Modal Body -->
            <div class="p-6 overflow-y-auto flex-grow">
                <form id="add-equipment-form" class="space-y-4">
                    <!-- Hidden ID field for updates -->
                    <input type="hidden" id="inv-id" value="">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold mb-1">Equipment Name</label>
                            <input type="text" id="inv-name" required class="w-full border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal bg-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Category</label>
                            <select id="inv-category" class="w-full border rounded px-7 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal bg-transparent cursor-pointer">
                                <option value="all">All Categories</option>
                                <option value="Laptop">Laptops</option>
                                <option value="Camera">Cameras</option>
                                <option value="Audio">Headphone</option>
                                <option value="Mouse">Mouse</option>
                                <option value="Projectors">Projector</option>
                                <option value="Speaker">Speaker</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Quantity</label>
                            <input type="number" id="inv-qty" value="1" min="0" required class="w-full border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal bg-transparent">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold mb-1">Equipment Image</label>
                            <div id="drop-zone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50 transition relative">
                                
                                <!-- Added Image Preview Thumbnail Element -->
                                <img id="image-preview" class="hidden mx-auto h-32 w-32 object-cover rounded mb-3 shadow border" alt="Preview">
                                
                                <p class="text-sm text-gray-500 mb-2 font-medium">Drag & Drop an image here, or click to browse</p>
                                <input type="file" id="file-input" class="hidden" accept="image/*">
                                
                                <!-- Optional: You can add 'type="text"' or keep 'type="url"' so long Base64 strings don't trigger browser URL validation errors -->
                                <input type="text" id="inv-image" placeholder="https://example.com/image.jpg or drop file above" class="w-full border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal bg-transparent">
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold mb-1">Details & Specifications</label>
                            <textarea id="inv-desc" rows="3" class="w-full border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-usp-teal bg-transparent" placeholder="Enter specifications, included accessories, etc."></textarea>
                        </div>
                    </div>
                    <button style="background-color: #00313c;" type="submit" class="w-full mt-4 bg-usp-green text-white py-3 rounded hover:bg-green-700 font-bold transition shadow-md">Save Equipment</button>
                </form>
            </div>
        </div>
    </div>

<script>
let equipmentData = [];
let bookingsData = [];

// Tab Switching
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

// Load current settings into the admin form
async function loadSettings() {
    try {
        const res = await fetch('api_settings.php');
        const data = await res.json();
        if(data.allow_weekends !== undefined) {
            document.getElementById('setting-weekends').value = data.allow_weekends;
            document.getElementById('setting-start-time').value = data.start_time;
            document.getElementById('setting-end-time').value = data.end_time;
            document.getElementById('setting-future-weeks').value = data.future_weeks_allowed;
        }
    } catch(e) { console.error("Error loading settings", e); }
}

document.getElementById('settings-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
        allow_weekends: document.getElementById('setting-weekends').value,
        start_time: document.getElementById('setting-start-time').value,
        end_time: document.getElementById('setting-end-time').value,
        future_weeks: document.getElementById('setting-future-weeks').value
    };

    try {
        const res = await fetch('api_settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        if(result.success) {
            alert("System schedule and calendar settings updated successfully!");
        } else {
            alert("Error updating settings.");
        }
    } catch(e) { alert("Failed to save settings."); }
});

loadSettings();

// Helper for dynamic images
function getImageUrl(category, customUrl) {
    // If a custom URL or Base64 image string is provided, use it
    if (customUrl && customUrl.trim() !== '') return customUrl;
    
    // Category-specific fallbacks
    if (category === 'Laptop') return 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500&q=80';
    if (category === 'Camera') return 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=500&q=80';
    if (category === 'Audio') return 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=500&q=80';
    if (category === 'Mouse') return 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=500&q=80';
    if (category === 'Projector' || category === 'Projectors') return 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500&q=80';
    if (category === 'Speaker' || category === 'Speakers') return 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=500&q=80';
    
    // Default fallback image if category doesn't match
    return 'https://images.unsplash.com/photo-1555664424-778a1e5e1b48?w=500&q=80'; 
}

// --- Drag and Drop Logic ---
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const imgInput = document.getElementById('inv-image');
const imgPreview = document.getElementById('image-preview');

// Drag events
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-usp-teal', 'bg-usp-green', 'bg-opacity-10');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-usp-teal', 'bg-usp-green', 'bg-opacity-10');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-usp-teal', 'bg-usp-green', 'bg-opacity-10');
    
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        processFile(e.dataTransfer.files[0]);
    }
});

// Click to browse events
dropZone.addEventListener('click', (e) => {
    // Prevent triggering file explorer if clicking the URL input box
    if (e.target !== imgInput) {
        fileInput.click();
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files && e.target.files[0]) {
        processFile(e.target.files[0]);
    }
});

// Convert image to Base64 for the API
function processFile(file) {
    if (!file.type.startsWith('image/')) {
        alert('Please drop a valid image file.');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        const base64String = e.target.result;
        imgInput.value = base64String; // Pass base64 data to the existing input
        imgPreview.src = base64String;
        imgPreview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

// Show preview if a URL is manually pasted
imgInput.addEventListener('input', (e) => {
    if (e.target.value) {
        imgPreview.src = e.target.value;
        imgPreview.classList.remove('hidden');
    } else {
        imgPreview.classList.add('hidden');
    }
});

// Load & Render Admin Inventory
async function loadInventory() {
    try {
        const response = await fetch('api_equipment.php');
        equipmentData = await response.json();
        
        const grid = document.getElementById('admin-inventory-grid');
        grid.innerHTML = ''; 
        
        equipmentData.forEach(eq => {
            const imgUrl = getImageUrl(eq.category, eq.image_url);
            grid.innerHTML += `
                <div class="bg-white border rounded-lg overflow-hidden shadow-sm flex flex-col">
                    <div class="h-32 bg-gray-100 relative">
                        <img src="${imgUrl}" class="w-full h-full object-cover">
                        <div class="absolute top-2 right-2 bg-white px-2 py-1 text-xs font-bold rounded shadow">Qty: ${eq.qty}</div>
                    </div>
                    <div class="p-3">
                        <h3 class="font-bold text-gray-800 truncate">${eq.name}</h3>
                        <p class="text-xs text-gray-500 uppercase">${eq.category}</p>
                        <div class="flex justify-between items-center mt-3">
                            <button onclick="openEditModal(${eq.id})" class="text-usp-teal hover:underline text-xs font-bold">Edit</button>
                            <button onclick="deleteEquipment(${eq.id})" class="text-red-600 hover:underline text-xs font-bold">Remove</button>
                        </div>
                    </div>
                </div>
            `;
        });
    } catch (error) { console.error("Error loading inventory:", error); }
}

async function loadAdminSlots() {
    try {
        const res = await fetch('api_admin_slots.php');
        const slots = await res.json();
        const tbody = document.getElementById('admin-slots-table');
        tbody.innerHTML = '';

        if(slots.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">No time slots found.</td></tr>`;
            return;
        }

        slots.forEach(slot => {
            let statusBadge = slot.is_blocked == 1 ? '<span class="text-red-600 font-bold">Blocked</span>' : 
                              slot.is_booked == 1 ? '<span class="text-orange-600 font-bold">Booked</span>' : 
                              '<span class="text-green-600 font-bold">Available</span>';
            
            let actionBtn = slot.is_blocked == 1 ? 
                `<button onclick="toggleSlotBlock(${slot.id}, 0)" class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-green-700">Unblock</button>` :
                `<button onclick="toggleSlotBlock(${slot.id}, 1)" class="bg-red-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-red-700">Block</button>`;

            tbody.innerHTML += `
                <tr>
                    <td class="px-4 py-2">${slot.slot_date}</td>
                    <td class="px-4 py-2 font-semibold">${slot.formatted_time}</td>
                    <td class="px-4 py-2">${statusBadge}</td>
                    <td class="px-4 py-2">${slot.is_booked == 1 ? '<span class="text-gray-400 text-xs italic">In Use</span>' : actionBtn}</td>
                </tr>
            `;
        });
    } catch(e) { console.error("Error loading admin slots", e); }
}

async function toggleSlotBlock(slotId, blockStatus) {
    try {
        const res = await fetch('api_admin_slots.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ slot_id: slotId, is_blocked: blockStatus })
        });
        const result = await res.json();
        if(result.success) {
            loadAdminSlots();
        } else {
            alert("Failed to update slot status.");
        }
    } catch(e) { alert("Connection error."); }
}

// Call loadAdminSlots when admin switches to settings tab
document.querySelector('[data-target="admin-settings"]').addEventListener('click', loadAdminSlots);

// Load & Render Bookings with full request details and fine clear actions
async function loadBookings() {
    try {
        const response = await fetch('api_admin_bookings.php');
        const data = await response.json();
        allBookingsData = data.bookings || data; // Handle array or object response safely
        renderAdminBookingsTable(allBookingsData);
    } catch (error) { console.error("Error fetching admin bookings:", error); }
}

function renderAdminBookingsTable(bookings) {
    const tbody = document.getElementById('admin-bookings-table');
    tbody.innerHTML = '';
    
    if (!bookings || bookings.length === 0 || bookings.error) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No bookings found.</td></tr>`;
        return;
    }

    bookings.forEach(b => {
        let actionButtons = '';
        if(b.status === 'Pending') {
            actionButtons = `
                <button onclick="updateBooking(${b.id}, 'Approved', ${b.equipment_id})" class="text-green-600 hover:underline mr-2 text-xs font-bold">Approve</button>
                <button onclick="updateBooking(${b.id}, 'Rejected', ${b.equipment_id})" class="text-red-600 hover:underline text-xs font-bold">Reject</button>
            `;
        } else if(b.status === 'Approved') {
            actionButtons = `<button onclick="updateBooking(${b.id}, 'Returned', ${b.equipment_id})" class="text-blue-600 hover:underline text-xs font-bold">Mark Returned</button>`;
        }

        let fineAction = '';
        if (b.fine_amount > 0 && b.fine_status === 'Unpaid') {
            fineAction = `<button onclick="markFinePaid(${b.id})" class="ml-2 bg-usp-green text-white px-2 py-1 rounded text-xs hover:bg-green-700">Mark Fine Paid (Clear Hold)</button>`;
        }

        let statusColor = b.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                          b.status === 'Approved' ? 'bg-green-100 text-green-800' :
                          b.status === 'Returned' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800';

        tbody.innerHTML += `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${b.student_username}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${b.equipment_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${b.use_date || 'N/A'} @ ${b.use_time || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${b.duration_hours ? b.duration_hours + ' Hour(s)' : 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm"><span class="px-2 inline-flex text-xs font-semibold rounded-full ${statusColor}">${b.status}</span></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    ${actionButtons}
                    ${fineAction}
                </td>
            </tr>
        `;
    });
}

// Handler for admin to clear the fine when paid at the counter
async function markFinePaid(bookingId) {
    if (!confirm("Confirm that the student has paid their fine at the counter?")) return;
    try {
        const response = await fetch('api_admin_bookings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId, action: 'pay_fine' })
        });
        const result = await response.json();
        if (result.success) {
            alert("Fine marked as paid. Student hold has been removed.");
            loadBookings();
        } else {
            alert("Error updating fine status.");
        }
    } catch(e) {
        console.error("Error clearing fine:", e);
    }
}

// Update Booking
window.updateBooking = async (bookingId, newStatus, eqId) => {
    if (!confirm(`Mark this booking as ${newStatus}?`)) return;
    try {
        const response = await fetch('api_admin_bookings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId, status: newStatus, equipment_id: eqId })
        });
        const result = await response.json();
        if (result.success) { loadBookings(); loadInventory(); } 
        else { alert("Error updating booking."); }
    } catch (error) { console.error("Error updating booking:", error); }
};

// --- Modal Logic (Add & Edit) ---
function openAddModal() { 
    document.getElementById('add-equipment-form').reset();
    document.getElementById('inv-id').value = ''; 
    
    // Hide and clear preview for new items
    if(imgPreview) {
        imgPreview.src = '';
        imgPreview.classList.add('hidden');
    }
    
    document.getElementById('modal-title-text').textContent = 'Add New Equipment';
    document.getElementById('add-equipment-modal').classList.remove('hidden'); 
}

function openEditModal(id) {
    const eq = equipmentData.find(e => e.id == id);
    if(!eq) return;
    
    document.getElementById('inv-id').value = eq.id;
    document.getElementById('inv-name').value = eq.name;
    document.getElementById('inv-category').value = eq.category;
    document.getElementById('inv-qty').value = eq.qty;
    document.getElementById('inv-image').value = eq.image_url || '';
    document.getElementById('inv-desc').value = eq.description || '';
    
    // Display preview if an image URL or Base64 string already exists
    if (eq.image_url && eq.image_url.trim() !== '') {
        imgPreview.src = eq.image_url;
        imgPreview.classList.remove('hidden');
    } else {
        imgPreview.classList.add('hidden');
    }
    
    document.getElementById('modal-title-text').textContent = 'Edit Equipment';
    document.getElementById('add-equipment-modal').classList.remove('hidden');
}

function closeAddModal() { 
    document.getElementById('add-equipment-modal').classList.add('hidden'); 
}

// Save (Add or Update) Equipment Submit
document.getElementById('add-equipment-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('inv-id').value;
    const method = id ? 'PUT' : 'POST'; // Use PUT if updating, POST if new
    
    const payload = {
        name: document.getElementById('inv-name').value,
        category: document.getElementById('inv-category').value,
        qty: document.getElementById('inv-qty').value,
        image_url: document.getElementById('inv-image').value,
        description: document.getElementById('inv-desc').value
    };
    
    if (id) payload.id = id;

    try {
        const response = await fetch('api_equipment.php', {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        if (result.success) {
            closeAddModal();
            loadInventory(); 
        } else {
            alert("Error saving item.");
        }
    } catch (error) { console.error("Error saving equipment:", error); }
});

// Delete Equipment
window.deleteEquipment = async (id) => {
    if(!confirm("Are you sure you want to permanently remove this equipment?")) return;
    try {
        const response = await fetch('api_equipment.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        const result = await response.json();
        if(result.success) loadInventory();
        else alert("Error deleting item.");
    } catch (error) { console.error("Error deleting equipment:", error); }
};

// --- Theme Handling ---
function setTheme(themeName) {
    document.body.classList.remove('theme-light-tilt', 'theme-white', 'theme-dark-tilt');
    document.body.classList.add(themeName);
    localStorage.setItem('usp_theme', themeName);
}

document.addEventListener("DOMContentLoaded", () => {
    const savedTheme = localStorage.getItem('usp_theme') || 'theme-light-tilt';
    setTheme(savedTheme);

    const themeSelector = document.getElementById('theme-selector');
    if (themeSelector) {
        themeSelector.value = savedTheme;
        themeSelector.addEventListener('change', (e) => {
            setTheme(e.target.value);
        });
    }
});

// Toggle Admin Profile Dropdown Menu
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

// Initial load
loadInventory();
loadBookings();
</script>

</body>
</html>