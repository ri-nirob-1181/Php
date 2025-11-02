<?php
require_once 'database.php';

 $db = new Database();

// Get product ID from URL
 $productId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$productId) {
    header('Location: index.php');
    exit;
}

 $product = $db->getProduct($productId);

if (!$product) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - E-Commerce Store</title>
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
                        <span class="cart-count">0</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <section class="product-detail">
                <div class="product-image-container">
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-detail-image">
                </div>
                <div class="product-detail-info">
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="product-category">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p class="product-detail-price">$<?php echo number_format($product['price'], 2); ?></p>
                    <p class="product-detail-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    
                    <div class="product-actions">
                        <button class="btn btn-primary add-to-cart" 
                                data-id="<?php echo $product['id']; ?>"
                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-price="<?php echo $product['price']; ?>"
                                data-image="<?php echo htmlspecialchars($product['image']); ?>">
                            Add to Cart
                        </button>
                        <a href="index.php" class="btn">Continue Shopping</a>
                    </div>
                </div>
            </section>
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