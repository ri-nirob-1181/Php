<?php
session_start();
require_once 'database.php';

 $db = new Database();
 $categories = $db->getCategories();
 $products = $db->getProducts();

// Handle category filter
 $categoryId = isset($_GET['category']) ? $_GET['category'] : null;
if ($categoryId) {
    $products = $db->getProducts($categoryId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
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
            <?php if (isset($_GET['order_success'])): ?>
                <div class="alert alert-success">
                    Your order has been placed successfully! We will contact you soon.
                </div>
            <?php endif; ?>

            <section>
                <h1>Our Products</h1>
                
                <div class="category-filter">
                    <a href="index.php" class="btn <?php echo !$categoryId ? 'btn-primary' : ''; ?>">All</a>
                    <?php foreach ($categories as $category): ?>
                        <a href="index.php?category=<?php echo $category['id']; ?>" 
                           class="btn <?php echo $categoryId == $category['id'] ? 'btn-primary' : ''; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="product-image">
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                                <div class="product-actions">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                                    <button class="btn btn-primary add-to-cart" 
                                            data-id="<?php echo $product['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                            data-price="<?php echo $product['price']; ?>"
                                            data-image="<?php echo htmlspecialchars($product['image']); ?>">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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