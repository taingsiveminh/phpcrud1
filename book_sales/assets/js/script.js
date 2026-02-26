// Responsive menu toggle
document.addEventListener('DOMContentLoaded', function() {
    // Add mobile menu toggle button
    const navbar = document.querySelector('.navbar .container');
    const navLinks = document.querySelector('.nav-links');
    
    if (navbar && navLinks) {
        // Create menu toggle button
        const menuToggle = document.createElement('button');
        menuToggle.className = 'menu-toggle';
        menuToggle.innerHTML = '☰ Menu';
        menuToggle.setAttribute('aria-label', 'Toggle navigation menu');
        
        // Insert toggle button before nav links
        navbar.insertBefore(menuToggle, navLinks);
        
        // Toggle menu on click
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('show');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!navbar.contains(event.target)) {
                navLinks.classList.remove('show');
            }
        });
        
        // Close menu when window resizes to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                navLinks.classList.remove('show');
            }
        });
    }
    
    // Add data-label attributes to table cells for mobile view
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        const headers = [];
        const headerCells = table.querySelectorAll('thead th');
        
        headerCells.forEach(header => {
            headers.push(header.textContent);
        });
        
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
                if (headers[index]) {
                    cell.setAttribute('data-label', headers[index]);
                }
            });
        });
    });
});

// Global functions
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.setAttribute('role', 'alert');
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto dismiss after 3 seconds
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transition = 'opacity 0.3s ease';
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 300);
    }, 3000);
}

function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Sale page functionality
class SaleManager {
    constructor() {
        this.items = [];
        this.init();
    }
    
    init() {
        this.addButton = document.getElementById('addItem');
        this.itemsContainer = document.getElementById('itemsContainer');
        this.totalElement = document.getElementById('totalAmount');
        this.saleForm = document.getElementById('saleForm');
        
        if (this.addButton) {
            this.addButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.addItem();
            });
        }
        
        if (this.saleForm) {
            this.saleForm.addEventListener('submit', (e) => this.submitSale(e));
        }
        
        // Initialize quantity input
        this.quantityInput = document.getElementById('itemQuantity');
        if (this.quantityInput) {
            this.quantityInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.addItem();
                }
            });
        }
    }
    
    async addItem() {
        const select = document.getElementById('bookSelect');
        const quantity = document.getElementById('itemQuantity');
        
        if (!select.value) {
            showAlert('Please select a book', 'danger');
            select.focus();
            return;
        }
        
        if (!quantity.value || quantity.value < 1) {
            showAlert('Please enter valid quantity', 'danger');
            quantity.focus();
            return;
        }
        
        const bookId = select.value;
        const bookName = select.options[select.selectedIndex].text.split(' - ')[0];
        const qty = parseInt(quantity.value);
        
        // Check if book already added
        const existingItem = this.items.find(item => item.book_id === bookId);
        if (existingItem) {
            showAlert('This book is already added to the sale', 'warning');
            return;
        }
        
        // Get selected option data
        const selectedOption = select.options[select.selectedIndex];
        const maxStock = parseInt(selectedOption.dataset.stock);
        const price = parseFloat(selectedOption.dataset.price);
        
        if (qty > maxStock) {
            showAlert(`Only ${maxStock} items in stock`, 'danger');
            return;
        }
        
        const subtotal = price * qty;
        const item = {
            book_id: bookId,
            title: bookName,
            quantity: qty,
            unit_price: price,
            subtotal: subtotal
        };
        
        this.items.push(item);
        this.renderItem(item);
        this.updateTotal();
        
        // Clear inputs
        select.value = '';
        quantity.value = '1';
        
        // Show success message
        showAlert('Item added to sale', 'success');
    }
    
    renderItem(item) {
        const row = document.createElement('div');
        row.className = 'sale-item-row';
        row.dataset.bookId = item.book_id;
        
        row.innerHTML = `
            <div><strong>${item.title}</strong></div>
            <div>Qty: ${item.quantity}</div>
            <div>Price: $${item.unit_price.toFixed(2)}</div>
            <div>Subtotal: $${item.subtotal.toFixed(2)}</div>
            <div>
                <button type="button" class="btn btn-danger btn-sm" onclick="saleManager.removeItem('${item.book_id}')" style="width: 100%;">
                    Remove
                </button>
            </div>
        `;
        
        this.itemsContainer.appendChild(row);
    }
    
    removeItem(bookId) {
        if (confirm('Remove this item from sale?')) {
            this.items = this.items.filter(item => item.book_id !== bookId);
            const row = document.querySelector(`.sale-item-row[data-book-id="${bookId}"]`);
            if (row) {
                row.remove();
            }
            this.updateTotal();
            showAlert('Item removed', 'info');
        }
    }
    
    updateTotal() {
        const total = this.items.reduce((sum, item) => sum + item.subtotal, 0);
        this.totalElement.textContent = `$${total.toFixed(2)}`;
    }
    
    async submitSale(e) {
        e.preventDefault();
        
        if (this.items.length === 0) {
            showAlert('Please add at least one item to the sale', 'danger');
            return;
        }
        
        // Disable submit button to prevent double submission
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';
        
        try {
            const response = await fetch('api/process-sale.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ items: this.items })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('Sale completed successfully!', 'success');
                setTimeout(() => {
                    window.location.href = 'sales.php';
                }, 1500);
            } else {
                showAlert(result.message, 'danger');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Error processing sale. Please try again.', 'danger');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }
}

// Initialize sale manager when on sale page
if (document.getElementById('saleForm')) {
    window.saleManager = new SaleManager();
}

// Search functionality for tables
function initializeTableSearch() {
    const searchInput = document.getElementById('tableSearch');
    if (!searchInput) return;
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const table = document.querySelector('table');
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

// Call search initialization if element exists
if (document.getElementById('tableSearch')) {
    initializeTableSearch();
}

// Add touch-friendly enhancements
document.addEventListener('touchstart', function(){}, {passive: true});