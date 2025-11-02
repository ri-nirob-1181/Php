<?php
session_start();
require_once 'database.php';

 $db = new Database();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_order') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $orderDetails = $_POST['order_details'];
    $total = $_POST['total'];
    
    // Add order to database
    $success = $db->addOrder($name, $email, $phone, $address, $orderDetails, $total);
    
    if ($success) {
        // Clear cart
        setcookie('cart', '', time() - 3600, '/');
        
        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Return error response
        echo json_encode(['success' => false]);
    }
    exit;
}

// Get cart items
 $cart = json_decode($_COOKIE['cart'] ?? '[]', true) ?: [];

// Get product details for cart items
 $cartItems = [];
 $total = 0;

foreach ($cart as $item) {
    $product = $db->getProduct($item['id']);
    if ($product) {
        $product['quantity'] = $item['quantity'];
        $cartItems[] = $product;
        $total += $product['price'] * $item['quantity'];
    }
}

// Redirect if cart is empty
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E-Commerce Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">E-Commerce Store</a>
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="admin.php">Admin</a></li>
                        <li>
                            <button class="theme-toggle" id="theme-toggle">
                                <span id="theme-icon">🌙</span>
                            </button>
                        </li>
                    </ul>
                </nav>
                <div class="cart-icon">
                    <a href="cart.php">
                        🛒
                        <span class="cart-count"><?php echo count($cart); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <h1>Checkout</h1>
            
            <div class="checkout-container">
                <div class="checkout-form-container">
                    <form id="checkout-form" class="checkout-form">
                        <h2>Billing Information</h2>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Shipping Address</label>
                            <textarea id="address" name="address" rows="4" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Place Order</button>
                    </form>
                </div>
                
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    
                    <div class="summary-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="summary-item">
                                <div class="summary-item-info">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Quantity: <?php echo $item['quantity']; ?></p>
                                </div>
                                <div class="summary-item-price">
                                    $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>$5.00</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax:</span>
                            <span>$<?php echo number_format($total * 0.1, 2); ?></span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Total:</span>
                            <span>$<?php echo number_format($total + 5 + ($total * 0.1), 2); ?></span>
                        </div>
                    </div>
                </div>
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