// Tab Switching Logic
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const targetId = e.target.getAttribute('data-target');
        
        // Remove active class from siblings
        Array.from(e.target.parentElement.children).forEach(sibling => {
            sibling.classList.remove('active', 'border-usp-teal', 'text-usp-teal');
        });
        e.target.classList.add('active', 'border-usp-teal', 'text-usp-teal');
        
        // Hide all tab contents and show the target
        const view = e.target.closest('.portal-card');
        view.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
        document.getElementById(targetId).classList.remove('hidden');
    });
});

// Load Equipment Inventory
async function loadInventory() {
    try {
        const response = await fetch('api_equipment.php');
        const equipment = await response.json();
        
        const grid = document.getElementById('admin-inventory-grid');
        grid.innerHTML = ''; // Clear current grid
        
        equipment.forEach(eq => {
            grid.innerHTML += `
                <div class="bg-white border rounded p-3 text-sm shadow-sm">
                    <div class="font-bold">${eq.name}</div>
                    <div class="text-gray-500 mb-2">Category: ${eq.category} | Qty: ${eq.qty}</div>
                </div>
            `;
        });
    } catch (error) {
        console.error("Error loading inventory:", error);
    }
}

// Add New Equipment
document.getElementById('add-equipment-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const newEquipment = {
        name: document.getElementById('inv-name').value,
        category: document.getElementById('inv-category').value,
        qty: document.getElementById('inv-qty').value
    };

    try {
        const response = await fetch('api_equipment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(newEquipment)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('add-equipment-form').reset();
            loadInventory(); // Refresh the grid to show the new item
        } else {
            alert("Error adding item.");
        }
    } catch (error) {
        console.error("Error saving equipment:", error);
    }
});

// Initial load
loadInventory();