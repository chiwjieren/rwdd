// Marketplace functionality
document.addEventListener('DOMContentLoaded', function() {
    // Modal Elements
    const swapModal = document.getElementById('swapModal');
    const addItemModal = document.getElementById('addItemModal');
    const closeButtons = document.querySelectorAll('.close-modal');
    const addItemBtn = document.getElementById('addItemBtn');

    // Add Item Button
    if (addItemBtn) {
        addItemBtn.addEventListener('click', openAddItemModal);
    }

    // Close Modal Buttons
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            swapModal.classList.remove('active');
            addItemModal.classList.remove('active');
        });
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === swapModal) {
            swapModal.classList.remove('active');
        }
        if (e.target === addItemModal) {
            addItemModal.classList.remove('active');
        }
    });
});

// Open swap modal with selected item
function openSwapModal(itemId) {
    const modal = document.getElementById('swapModal');
    const selectedItem = document.querySelector(`[data-id="${itemId}"]`).cloneNode(true);
    
    // Clear previous content
    const selectedItemContainer = modal.querySelector('.selected-item');
    selectedItemContainer.innerHTML = '';
    selectedItemContainer.appendChild(selectedItem);

    // Remove swap button from cloned item
    selectedItemContainer.querySelector('.btn-swap').remove();

    // Populate user's items (this would normally come from a database)
    const yourItems = modal.querySelector('.your-items');
    yourItems.innerHTML = `
        <article class="product" data-id="user1">
            <div class="product-image">
                <img src="../media/user-item1.jpg" alt="Your Item 1">
            </div>
            <div class="product-info">
                <h3>Your Item 1</h3>
                <button class="btn btn-secondary btn-select">Select</button>
            </div>
        </article>
        <article class="product" data-id="user2">
            <div class="product-image">
                <img src="../media/user-item2.jpg" alt="Your Item 2">
            </div>
            <div class="product-info">
                <h3>Your Item 2</h3>
                <button class="btn btn-secondary btn-select">Select</button>
            </div>
        </article>
    `;

    // Add selection functionality
    const selectButtons = yourItems.querySelectorAll('.btn-select');
    selectButtons.forEach(button => {
        button.addEventListener('click', function() {
            selectButtons.forEach(btn => btn.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    modal.classList.add('active');
}

function closeSwapModal() {
    document.getElementById('swapModal').classList.remove('active');
}

function openAddItemModal() {
    document.getElementById('addItemModal').classList.add('active');
}

function closeAddItemModal() {
    document.getElementById('addItemModal').classList.remove('active');
}

function proposeSwap() {
    const selectedButton = document.querySelector('.btn-select.selected');
    if (!selectedButton) {
        alert('Please select an item to swap');
        return;
    }
    
    // Here you would normally send this to your backend
    alert('Swap proposal sent! The other user will be notified.');
    closeSwapModal();
}

function submitNewItem() {
    const form = document.getElementById('addItemForm');
    if (!form.checkValidity()) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Here you would normally send this to your backend
    alert('Item added successfully!');
    closeAddItemModal();
}
