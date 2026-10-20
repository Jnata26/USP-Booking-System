let equipmentData = [];

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

// Fetch Equipment from Database
async function loadEquipment() {
    try {
        const response = await fetch('api_equipment.php');
        equipmentData = await response.json();
        renderEquipment(); 
    } catch (error) {
        console.error("Error fetching equipment:", error);
        document.getElementById('equipment-grid').innerHTML = '<p class="text-red-500">Failed to connect to database.</p>';
    }
}

// Assign images based on category
function getImageUrl(category) {
    if(category === 'Laptop') return 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500&q=80';
    if(category === 'Camera') return 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=500&q=80';
    if(category === 'Audio') return 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=500&q=80';
    return 'https://images.unsplash.com/photo-1555664424-778a1e5e1b48?w=500&q=80'; 
}

// Render Equipment Cards to Grid
function renderEquipment() {
    const grid = document.getElementById('equipment-grid');
    const filter = document.getElementById('filter-category').value;
    const search = document.getElementById('search-equipment').value.toLowerCase();
    
    grid.innerHTML = ''; 
    
    const filteredData = equipmentData.filter(eq => {
        const matchCat = filter === 'all' || eq.category === filter;
        const matchSearch = eq.name.toLowerCase().includes(search);
        return matchCat && matchSearch;
    });

    if (filteredData.length === 0) {
        grid.innerHTML = '<p class="text-gray-500 col-span-3 text-center py-8">No equipment found matching your search.</p>';
        return;
    }

    filteredData.forEach(eq => {
        const available = parseInt(eq.qty) > 0;
        const imgUrl = getImageUrl(eq.category);
        
        grid.innerHTML += `
            <div class="bg-white border rounded-lg overflow-hidden shadow-sm flex flex-col transition hover:shadow-md">
                <div class="h-48 bg-gray-100 overflow-hidden relative">
                    <img src="${imgUrl}" alt="${eq.name}" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="text-xs px-2 py-1 rounded shadow-sm font-bold ${available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${available ? eq.qty + ' Available' : 'Out of Stock'}
                        </span>
                    </div>
                </div>
                <div class="p-4 flex-grow flex flex-col justify-between">
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800 text-lg">${eq.name}</h3>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mt-1">${eq.category}</p>
                    </div>
                    <button onclick="requestBooking('${eq.id}', '${eq.name}')" ${!available ? 'disabled' : ''} class="w-full py-2 rounded text-sm font-medium transition ${available ? 'bg-usp-dark text-white hover:bg-green-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed'}">
                        ${available ? 'Request Booking' : 'Unavailable'}
                    </button>
                </div>
            </div>
        `;
    });
}

// Listen for search and filter changes
document.getElementById('search-equipment').addEventListener('input', renderEquipment);
document.getElementById('filter-category').addEventListener('change', renderEquipment);

// Placeholder for booking
window.requestBooking = async (eqId, eqName) => {
    alert("Booking logic coming next! You selected: " + eqName);
};

// Start load
loadEquipment();