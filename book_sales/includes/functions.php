<?php
require_once 'config.php';

// Get all books with inventory
function getBooksWithInventory($conn) {
    $sql = "SELECT b.*, i.stock_quantity 
            FROM books b 
            LEFT JOIN inventory i ON b.book_id = i.book_id 
            ORDER BY b.title";
    $result = mysqli_query($conn, $sql);
    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    return $books;
}

// Get single book
function getBook($conn, $book_id) {
    $sql = "SELECT b.*, i.stock_quantity 
            FROM books b 
            LEFT JOIN inventory i ON b.book_id = i.book_id 
            WHERE b.book_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $book_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Add new book
function addBook($conn, $isbn, $title, $author, $category, $price, $stock_quantity) {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert book
        $sql = "INSERT INTO books (isbn, title, author, category, price) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssd", $isbn, $title, $author, $category, $price);
        mysqli_stmt_execute($stmt);
        $book_id = mysqli_insert_id($conn);
        
        // Insert inventory
        $sql = "INSERT INTO inventory (book_id, stock_quantity) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $book_id, $stock_quantity);
        mysqli_stmt_execute($stmt);
        
        mysqli_commit($conn);
        return ['success' => true, 'message' => 'Book added successfully'];
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return ['success' => false, 'message' => 'Error adding book: ' . $e->getMessage()];
    }
}

// Update book
function updateBook($conn, $book_id, $isbn, $title, $author, $category, $price, $stock_quantity) {
    mysqli_begin_transaction($conn);
    
    try {
        // Update book
        $sql = "UPDATE books SET isbn=?, title=?, author=?, category=?, price=? WHERE book_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssdi", $isbn, $title, $author, $category, $price, $book_id);
        mysqli_stmt_execute($stmt);
        
        // Update inventory
        $sql = "UPDATE inventory SET stock_quantity=? WHERE book_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $stock_quantity, $book_id);
        mysqli_stmt_execute($stmt);
        
        mysqli_commit($conn);
        return ['success' => true, 'message' => 'Book updated successfully'];
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return ['success' => false, 'message' => 'Error updating book: ' . $e->getMessage()];
    }
}

// Delete book
function deleteBook($conn, $book_id) {
    $sql = "DELETE FROM books WHERE book_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $book_id);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Book deleted successfully'];
    } else {
        return ['success' => false, 'message' => 'Error deleting book'];
    }
}

// Process sale
function processSale($conn, $items) {
    mysqli_begin_transaction($conn);
    
    try {
        // Calculate total
        $total = 0;
        foreach ($items as $item) {
            $total += $item['subtotal'];
        }
        
        // Insert sale
        $sql = "INSERT INTO sales (total_amount) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "d", $total);
        mysqli_stmt_execute($stmt);
        $sale_id = mysqli_insert_id($conn);
        
        // Insert sale items and update inventory
        foreach ($items as $item) {
            // Insert sale item
            $sql = "INSERT INTO sale_items (sale_id, book_id, quantity, unit_price, subtotal) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiidd", $sale_id, $item['book_id'], 
                                  $item['quantity'], $item['unit_price'], $item['subtotal']);
            mysqli_stmt_execute($stmt);
            
            // Update inventory
            $sql = "UPDATE inventory SET stock_quantity = stock_quantity - ? WHERE book_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $item['quantity'], $item['book_id']);
            mysqli_stmt_execute($stmt);
        }
        
        mysqli_commit($conn);
        return ['success' => true, 'message' => 'Sale completed successfully', 'sale_id' => $sale_id];
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return ['success' => false, 'message' => 'Error processing sale: ' . $e->getMessage()];
    }
}

// Get sales history
function getSalesHistory($conn) {
    $sql = "SELECT s.*, COUNT(si.sale_item_id) as item_count 
            FROM sales s 
            LEFT JOIN sale_items si ON s.sale_id = si.sale_id 
            GROUP BY s.sale_id 
            ORDER BY s.sale_date DESC";
    $result = mysqli_query($conn, $sql);
    $sales = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sales[] = $row;
    }
    return $sales;
}

// Get sale details
function getSaleDetails($conn, $sale_id) {
    $sql = "SELECT si.*, b.title, b.author 
            FROM sale_items si 
            JOIN books b ON si.book_id = b.book_id 
            WHERE si.sale_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $sale_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    return $items;
}
?>