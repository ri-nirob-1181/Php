<?php
// Database connection and CRUD functions for SQLite

class Database {
    private $db;
    
    public function __construct() {
        try {
            // Create directory if it doesn't exist
            if (!is_dir('db')) {
                mkdir('db', 0755, true);
            }
            
            // Connect to SQLite database
            $this->db = new PDO('sqlite:db/ecommerce.db');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Initialize database tables if they don't exist
            $this->initializeTables();
            
            // Add sample data if tables are empty
            $this->addSampleData();
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    private function initializeTables() {
        // Create categories table
        $this->db->exec("CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE
        )");
        
        // Create products table
        $this->db->exec("CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            price REAL NOT NULL,
            image TEXT,
            category_id INTEGER,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        )");
        
        // Create orders table
        $this->db->exec("CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT,
            address TEXT,
            order_details TEXT NOT NULL,
            total REAL NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create admin table
        $this->db->exec("CREATE TABLE IF NOT EXISTS admin (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL
        )");
    }
    
    private function addSampleData() {
        // Check if categories table is empty
        $stmt = $this->db->query("SELECT COUNT(*) FROM categories");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            // Add sample categories
            $this->db->exec("INSERT INTO categories (name) VALUES ('Electronics')");
            $this->db->exec("INSERT INTO categories (name) VALUES ('Clothing')");
            $this->db->exec("INSERT INTO categories (name) VALUES ('Books')");
            
            // Add sample products
            $this->db->exec("INSERT INTO products (name, description, price, image, category_id) VALUES 
                ('Smartphone', 'Latest model with advanced features', 699.99, 'smartphone.jpg', 1),
                ('Laptop', 'High-performance laptop for work and gaming', 1299.99, 'laptop.jpg', 1),
                ('Headphones', 'Noise-cancelling wireless headphones', 199.99, 'headphones.jpg', 1),
                ('T-Shirt', 'Comfortable cotton t-shirt', 19.99, 'tshirt.jpg', 2),
                ('Jeans', 'Classic fit denim jeans', 49.99, 'jeans.jpg', 2),
                ('Jacket', 'Warm winter jacket', 89.99, 'jacket.jpg', 2),
                ('Fiction Book', 'Bestselling novel', 14.99, 'book1.jpg', 3),
                ('Programming Book', 'Learn to code in 30 days', 29.99, 'book2.jpg', 3),
                ('Cookbook', 'Delicious recipes from around the world', 24.99, 'book3.jpg', 3)");
            
            // Add admin user (password: admin123)
            $this->db->exec("INSERT INTO admin (username, password) VALUES ('admin', 'admin123')");
        }
    }
    
    // Category CRUD operations
    public function getCategories() {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategory($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addCategory($name) {
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
        return $stmt->execute([$name]);
    }
    
    public function updateCategory($id, $name) {
        $stmt = $this->db->prepare("UPDATE categories SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }
    
    public function deleteCategory($id) {
        // First update products with this category to have no category
        $stmt = $this->db->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
        $stmt->execute([$id]);
        
        // Then delete the category
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // Product CRUD operations
    public function getProducts($categoryId = null) {
        if ($categoryId) {
            $stmt = $this->db->prepare("SELECT p.*, c.name as category_name FROM products p 
                                       LEFT JOIN categories c ON p.category_id = c.id 
                                       WHERE p.category_id = ? ORDER BY p.name");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $this->db->query("SELECT p.*, c.name as category_name FROM products p 
                                     LEFT JOIN categories c ON p.category_id = c.id 
                                     ORDER BY p.name");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProduct($id) {
        $stmt = $this->db->prepare("SELECT p.*, c.name as category_name FROM products p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addProduct($name, $description, $price, $image, $categoryId) {
        $stmt = $this->db->prepare("INSERT INTO products (name, description, price, image, category_id) 
                                   VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $description, $price, $image, $categoryId]);
    }
    
    public function updateProduct($id, $name, $description, $price, $image, $categoryId) {
        $stmt = $this->db->prepare("UPDATE products SET name = ?, description = ?, price = ?, 
                                   image = ?, category_id = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $price, $image, $categoryId, $id]);
    }
    
    public function deleteProduct($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // Order operations
    public function addOrder($name, $email, $phone, $address, $orderDetails, $total) {
        $stmt = $this->db->prepare("INSERT INTO orders (name, email, phone, address, order_details, total) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $phone, $address, $orderDetails, $total]);
    }
    
    public function getOrders() {
        $stmt = $this->db->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Admin operations
    public function checkAdmin($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>