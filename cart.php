<?php
require_once 'database.php';

 $db = new Database();
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - E-Commerce Store</title>
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
            <h1>Shopping Cart</h1>
            
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <p>Your cart is empty.</p>
                    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <div class="cart-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item">
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="cart-item-image">
                                <div class="cart-item-info">
                                    <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="cart-item-price">$<?php echo number_format($item['price'], 2); ?></p>
                                    <div class="cart-item-quantity">
                                        <button class="quantity-btn quantity-minus" data-id="<?php echo $item['id']; ?>">-</button>
                                        <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" 
                                               min="1" data-id="<?php echo $item['id']; ?>">
                                        <button class="quantity-btn quantity-plus" data-id="<?php echo $item['id']; ?>">+</button>
                                        <button class="btn btn-danger remove-from-cart" data-id="<?php echo $item['id']; ?>">Remove</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <h2>Order Summary</h2>
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
                        <a href="checkout.php" class="btn btn-primary btn-block">Proceed to Checkout</a>
                    </div>
                </div>
            <?php endif; ?>
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