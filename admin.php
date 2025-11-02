<?php
session_start();
require_once 'database.php';

 $db = new Database();

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $admin = $db->checkAdmin($username, $password);
    
    if ($admin) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'Invalid username or password';
    }
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    header('Location: admin.php');
    exit;
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - E-Commerce Store</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>
            <div class="container">
                <div class="header-content">
                    <a href="index.php" class="logo">E-Commerce Store</a>
                </div>
            </div>
        </header>

        <main>
            <div class="container">
                <div class="login-form">
                    <h2>Admin Login</h2>
                    
                    <?php if (isset($login_error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $login_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <input type="hidden" name="action" value="login">
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                    
                    <p style="margin-top: 1rem; text-align: center;">
                        <a href="index.php">Back to Store</a>
                    </p>
                </div>
            </div>
        </main>

        <footer>
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> E-Commerce Store. All rights reserved.</p>
            </div>
        </footer>

        <script src="script.js"></script>
    </body>
    </html>
    <?php
    exit;
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Product operations
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_product') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $image = $_POST['image'];
            $categoryId = $_POST['category_id'];
            
            $db->addProduct($name, $description, $price, $image, $categoryId);
            $success_message = 'Product added successfully';
        } elseif ($_POST['action'] === 'update_product') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $image = $_POST['image'];
            $categoryId = $_POST['category_id'];
            
            $db->updateProduct($id, $name, $description, $price, $image, $categoryId);
            $success_message = 'Product updated successfully';
        } elseif ($_POST['action'] === 'delete_product') {
            $id = $_POST['id'];
            $db->deleteProduct($id);
            $success_message = 'Product deleted successfully';
        }
        
        // Category operations
        elseif ($_POST['action'] === 'add_category') {
            $name = $_POST['name'];
            $db->addCategory($name);
            $success_message = 'Category added successfully';
        } elseif ($_POST['action'] === 'update_category') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $db->updateCategory($id, $name);
            $success_message = 'Category updated successfully';
        } elseif ($_POST['action'] === 'delete_category') {
            $id = $_POST['id'];
            $db->deleteCategory($id);
            $success_message = 'Category deleted successfully';
        }
    }
}

// Get data for display
 $products = $db->getProducts();
 $categories = $db->getCategories();
 $orders = $db->getOrders();

// Get current tab
 $tab = isset($_GET['tab']) ? $_GET['tab'] : 'products';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - E-Commerce Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">E-Commerce Store</a>
                <nav>
                    <ul>
                        <li><a href="index.php">Store</a></li>
                        <li><a href="admin.php?action=logout">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <h1>Admin Panel</h1>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="admin-container">
                <div class="admin-sidebar">
                    <ul class="admin-menu">
                        <li><a href="admin.php?tab=products" class="<?php echo $tab === 'products' ? 'active' : ''; ?>">Products</a></li>
                        <li><a href="admin.php?tab=categories" class="<?php echo $tab === 'categories' ? 'active' : ''; ?>">Categories</a></li>
                        <li><a href="admin.php?tab=orders" class="<?php echo $tab === 'orders' ? 'active' : ''; ?>">Orders</a></li>
                    </ul>
                </div>
                
                <div class="admin-content">
                    <?php if ($tab === 'products'): ?>
                        <div class="admin-card">
                            <h2>Products</h2>
                            
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo $product['id']; ?></td>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'None'); ?></td>
                                            <td class="admin-actions">
                                                <button class="btn" onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="action" value="delete_product">
                                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <button class="btn btn-primary" onclick="showAddProductForm()">Add New Product</button>
                        </div>
                        
                        <!-- Add/Edit Product Form -->
                        <div id="product-form-container" class="admin-card" style="display: none;">
                            <h2 id="product-form-title">Add New Product</h2>
                            
                            <form method="post" id="product-form">
                                <input type="hidden" name="action" id="product-action" value="add_product">
                                <input type="hidden" name="id" id="product-id">
                                
                                <div class="form-group">
                                    <label for="product-name">Name</label>
                                    <input type="text" id="product-name" name="name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="product-description">Description</label>
                                    <textarea id="product-description" name="description" rows="4"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="product-price">Price</label>
                                    <input type="number" id="product-price" name="price" step="0.01" min="0" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="product-image">Image Filename</label>
                                    <input type="text" id="product-image" name="image">
                                </div>
                                
                                <div class="form-group">
                                    <label for="product-category">Category</label>
                                    <select id="product-category" name="category_id">
                                        <option value="">None</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn" onclick="hideProductForm()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    <?php elseif ($tab === 'categories'): ?>
                        <div class="admin-card">
                            <h2>Categories</h2>
                            
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo $category['id']; ?></td>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td class="admin-actions">
                                                <button class="btn" onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')">Edit</button>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="action" value="delete_category">
                                                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <button class="btn btn-primary" onclick="showAddCategoryForm()">Add New Category</button>
                        </div>
                        
                        <!-- Add/Edit Category Form -->
                        <div id="category-form-container" class="admin-card" style="display: none;">
                            <h2 id="category-form-title">Add New Category</h2>
                            
                            <form method="post" id="category-form">
                                <input type="hidden" name="action" id="category-action" value="add_category">
                                <input type="hidden" name="id" id="category-id">
                                
                                <div class="form-group">
                                    <label for="category-name">Name</label>
                                    <input type="text" id="category-name" name="name" required>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn" onclick="hideCategoryForm()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    <?php elseif ($tab === 'orders'): ?>
                        <div class="admin-card">
                            <h2>Orders</h2>
                            
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['name']); ?></td>
                                            <td><?php echo htmlspecialchars($order['email']); ?></td>
                                            <td>$<?php echo number_format($order['total'], 2); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                            <td class="admin-actions">
                                                <button class="btn" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">View</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Order Details Modal -->
                        <div id="order-details-modal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <span class="close" onclick="closeOrderDetails()">&times;</span>
                                <h2>Order Details</h2>
                                <div id="order-details-content"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> E-Commerce Store. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Product form functions
        function showAddProductForm() {
            document.getElementById('product-form-container').style.display = 'block';
            document.getElementById('product-form-title').textContent = 'Add New Product';
            document.getElementById('product-action').value = 'add_product';
            document.getElementById('product-form').reset();
        }
        
        function hideProductForm() {
            document.getElementById('product-form-container').style.display = 'none';
        }
        
        function editProduct(id) {
            // Fetch product data via AJAX
            fetch('admin.php?action=get_product&id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('product-form-container').style.display = 'block';
                    document.getElementById('product-form-title').textContent = 'Edit Product';
                    document.getElementById('product-action').value = 'update_product';
                    document.getElementById('product-id').value = data.id;
                    document.getElementById('product-name').value = data.name;
                    document.getElementById('product-description').value = data.description;
                    document.getElementById('product-price').value = data.price;
                    document.getElementById('product-image').value = data.image;
                    document.getElementById('product-category').value = data.category_id;
                });
        }
        
        // Category form functions
        function showAddCategoryForm() {
            document.getElementById('category-form-container').style.display = 'block';
            document.getElementById('category-form-title').textContent = 'Add New Category';
            document.getElementById('category-action').value = 'add_category';
            document.getElementById('category-form').reset();
        }
        
        function hideCategoryForm() {
            document.getElementById('category-form-container').style.display = 'none';
        }
        
        function editCategory(id, name) {
            document.getElementById('category-form-container').style.display = 'block';
            document.getElementById('category-form-title').textContent = 'Edit Category';
            document.getElementById('category-action').value = 'update_category';
            document.getElementById('category-id').value = id;
            document.getElementById('category-name').value = name;
        }
        
        // Order details function
        function viewOrderDetails(id) {
            // Fetch order data via AJAX
            fetch('admin.php?action=get_order&id=' + id)
                .then(response => response.json())
                .then(data => {
                    let html = '<div class="order-info">';
                    html += '<p><strong>Order ID:</strong> ' + data.id + '</p>';
                    html += '<p><strong>Name:</strong> ' + data.name + '</p>';
                    html += '<p><strong>Email:</strong> ' + data.email + '</p>';
                    html += '<p><strong>Phone:</strong> ' + data.phone + '</p>';
                    html += '<p><strong>Address:</strong> ' + data.address + '</p>';
                    html += '<p><strong>Total:</strong> $' + parseFloat(data.total).toFixed(2) + '</p>';
                    html += '<p><strong>Date:</strong> ' + new Date(data.created_at).toLocaleString() + '</p>';
                    html += '</div>';
                    
                    html += '<h3>Order Items</h3>';
                    html += '<table class="admin-table">';
                    html += '<thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr></thead>';
                    html += '<tbody>';
                    
                    const items = JSON.parse(data.order_details);
                    items.forEach(item => {
                        html += '<tr>';
                        html += '<td>' + item.name + '</td>';
                        html += '<td>$' + parseFloat(item.price).toFixed(2) + '</td>';
                        html += '<td>' + item.quantity + '</td>';
                        html += '<td>$' + (parseFloat(item.price) * parseInt(item.quantity)).toFixed(2) + '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    
                    document.getElementById('order-details-content').innerHTML = html;
                    document.getElementById('order-details-modal').style.display = 'block';
                });
        }
        
        function closeOrderDetails() {
            document.getElementById('order-details-modal').style.display = 'none';
        }
        
        // Handle AJAX requests for getting product/order data
        <?php if (isset($_GET['action']) && $_GET['action'] === 'get_product' && isset($_GET['id'])): ?>
            <?php
            $product = $db->getProduct($_GET['id']);
            header('Content-Type: application/json');
            echo json_encode($product);
            exit;
            ?>
        <?php endif; ?>
        
        <?php if (isset($_GET['action']) && $_GET['action'] === 'get_order' && isset($_GET['id'])): ?>
            <?php
            $orders = $db->getOrders();
            $order = null;
            
            foreach ($orders as $o) {
                if ($o['id'] == $_GET['id']) {
                    $order = $o;
                    break;
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode($order);
            exit;
            ?>
        <?php endif; ?>
    </script>
</body>
</html>