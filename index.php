<?php
include("./includes/header.php");
include("./includes/conn.php");
?>

<!-- Hero Banner -->
<div class="slider-container">
    <?php

    $res = mysqli_query($conn, "SELECT * FROM topbanner ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($res)) {
        ?>
        <div class="slide">
            <img src="./admin/uploads/<?= htmlspecialchars($row['image']) ?>"
                alt="<?= htmlspecialchars($row['caption']) ?>" />
            <div class="caption"><?= htmlspecialchars($row['caption']) ?></div>
        </div>
    <?php } ?>

    <span class="arrow prev" onclick="controller(-1)">&#10094;</span>
    <span class="arrow next" onclick="controller(1)">&#10095;</span>
</div>


<!-- Categories -->
<section class="categories">
    <div id="container">
        <h2>Shop by Category</h2>
        <div class="categories-grid">
            <?php
            $result = $conn->query("SELECT * FROM categories ORDER BY id DESC");

            while ($row = $result->fetch_assoc()) {
                // print_r($row);
                echo "
                    <div class='category-card'>
                        <a href='products.php?category={$row['name']}' aria-label='View {$row['name']} products'></a>
                        <div class='category-info'>
                            <img src='./admin/uploads/{$row['image']}' alt='{$row['name']}' loading='lazy'>
                            <h3>{$row['name']}</h3>
                        </div>
                    </div>";
            }
            ?>
        </div>
    </div>
</section>

<script>
    // Slider navigation functionality
    document.addEventListener('DOMContentLoaded', function () {
        const slider = document.querySelector('.category-slider');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        const cardWidth = 250; // Should match your CSS card width + gap
        const scrollAmount = cardWidth * 3; // Scroll 3 cards at a time

        prevBtn.addEventListener('click', () => {
            slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        // Hide buttons when at extremes
        const checkButtons = () => {
            prevBtn.style.display = slider.scrollLeft <= 0 ? 'none' : 'flex';
            nextBtn.style.display = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth ? 'none' : 'flex';
        };

        slider.addEventListener('scroll', checkButtons);
        checkButtons();

        // Add intersection observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.category-card').forEach(card => {
            observer.observe(card);
        });
    });
</script>


<section id="featured-products">
    <div class="container_featured-products">
        <h2>Latest Products</h2>

        <div class="product-grid">
            <?php

            $result = $conn->query("SELECT * FROM products WHERE category = 'Latest Products' ORDER BY id DESC LIMIT 8");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $product_id = $row['id'];
                    $product_name = $row['product_name'];
                    $desc = $row['description'];
                    $image = "admin/uploads/" . $row['image'];
                    $net_price = number_format($row['price'], 2);
                    $selling_price = number_format($row['sale_price'], 2);
                    $discount = $row['discount'];
                    $stock = $row['stock'];
                    ?>
                    <div class="product-card" onclick="window.location.href='product-detail.php?product_id=<?= $row['id'] ?>'"
                        style="cursor: pointer;">
                        <span class='discount-badge'><?= $discount ?>% OFF</span>
                        <img src='<?= $image ?>' alt='<?= $product_name ?>'>
                        <h3><?= $product_name ?></h3>
                        <p><?= $desc ?></p>
                        <p class='price'>
                            <span class='old-price'>₹<?= $net_price ?></span>
                            ₹<?= $selling_price ?>
                        </p>
                        <p><strong>Stock:</strong> <?= ($stock > 0 ? 'In Stock' : 'Out of Stock') ?></p>
                        <div class='btn-group'>
                            <a href="add_to_cart.php?table=products&id=<?= $product_id ?>" class="btn add-to-cart">Add to
                                Cart</a>
                            <a href="buy.php?table=products&product_id=<?= $product_id ?>" class="btn buy-now">Buy Now</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No Featured Products found.</p>";
            }
            ?>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="products.php" class="add-to-cart">View All Products</a>
        </div>
    </div>
</section>

<section class="health-cards">
    <div class="container">
        <div class="health-grid">
            <div class="health-card orange">
                <div class="health-content">
                    <h3>Mental Health Awareness</h3>
                    <p>Take care of your mental health</p>
                    <button class="btn dark-btn">Learn More</button>
                </div>
                <div class="health-image">
                    <img src="./images/medical.jpeg" alt="Mental Health" loading="lazy">
                </div>
            </div>
            <div class="health-card blue">
                <div class="health-content">
                    <h3>Respiratory Health</h3>
                    <p>Breathe easy with our products</p>
                    <button class="btn dark-btn">Shop Now</button>
                </div>
                <div class="health-image">
                    <img src="./images/lungs.jpeg" alt="Respiratory Health" loading="lazy">
                </div>
            </div>
            <div class="health-card yellow">
                <div class="health-content">
                    <h3>Oral Health</h3>
                    <p>Get up to 15% off</p>
                    <button class="btn dark-btn">Shop Now</button>
                </div>
                <div class="health-image">
                    <img src="./images/teath.jpeg" alt="Oral Health" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>


<section class="article-slider-wrap">
    <h3>Health Articles</h3>
    <p>Get up-to-date on our latest health updates</p>
    <img src="images/back.png" alt="Back" id="articleBackBtn">

    <div class="article-slider" id="scrollable-articles">
        <?php

        $res = $conn->query("SELECT * FROM healtharticles ORDER BY id DESC");
        while ($row = $res->fetch_assoc()) {
            echo '
            <div class="article-card">
                <img src="./admin/uploads/' . $row['image'] . '" alt="' . htmlspecialchars($row['heading']) . '">
                <h4>' . htmlspecialchars($row['heading']) . '</h4>
                <p>' . htmlspecialchars($row['para']) . '</p>
            </div>';
        }
        ?>
    </div>

    <img src="images/next.png" alt="Next" id="articleNextBtn">
</section>


<section id="featured-products">
    <div class="container_featured-products">
        <h2>Deals of the day</h2>
        <div class="product-grid">
            <?php

            $result = $conn->query("SELECT * FROM products WHERE category = 'Deal of the Day' ORDER BY id DESC LIMIT 8");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $product_id = $row['id'];
                    $product_name = $row['product_name'];
                    $desc = $row['description'];
                    $image = "admin/uploads/" . $row['image'];
                    $net_price = number_format($row['price'], 2);
                    $selling_price = number_format($row['sale_price'], 2);
                    $discount = $row['discount'];
                    $stock = $row['stock'];
                    ?>
                    <div class="product-card" onclick="window.location.href='product-detail.php?product_id=<?= $row['id'] ?>'"
                    style="cursor: pointer;">
                        <span class='discount-badge'><?= $discount ?>% OFF</span>
                        <img src='<?= $image ?>' alt='<?= $product_name ?>'>
                        <h3><?= $product_name ?></h3>
                        <p><?= $desc ?></p>
                        <p class='price'>
                            <span class='old-price'>₹<?= $net_price ?></span>
                            ₹<?= $selling_price ?>
                        </p>
                        <p><strong>Stock:</strong> <?= ($stock > 0 ? 'In Stock' : 'Out of Stock') ?></p>
                        <div class='btn-group'>
                            <a href="add_to_cart.php?table=products&id=<?= $product_id ?>" class="btn add-to-cart">Add to
                                Cart</a>
                            <a href="buy.php?table=products&product_id=<?= $product_id ?>" class="btn buy-now">Buy Now</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No Featured Products found.</p>";
            }
            ?>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="products.php" class="add-to-cart">View All Products</a>
        </div>
    </div>
</section>

<div class="gallery-wrap">
    <h3>Featured Brands</h3>
    <p>Pick from our favourite brands</p>
    <img src="images/back.png" alt="Back" id="backbtn">

    <div class="gallery" id="scrollable-gallery">
        <?php
        $result = $conn->query("SELECT * FROM featuredbrands ORDER BY id DESC");

        $count = 0;
        echo "<div>"; // open first group
        
        while ($row = $result->fetch_assoc()) {
            echo "<span><img src='admin/uploads/{$row['image']}' alt='Brand'></span>";
            $count++;

            // Create a new group of <div> every 3 items (optional: adjust as needed)
            if ($count % 3 == 0 && !$result->fetch_assoc() === null) {
                echo "</div><div>";
            }
        }

        echo "</div>"; // close last group
        ?>
    </div>

    <img src="images/next.png" alt="Next" id="nextbtn">
</div>



<!-- Customer Reviews -->
<section class="customer-reviews">
    <div class="customer-reviewscontainer">
        <div class="section-header">
            <h2>What our Customers say</h2>
            <div class="rating">
                <?php for ($i = 1; $i <= 5; $i++)
                    echo '<i class="fas fa-star"></i>'; ?>
                <span>4.8/5 on Trustpilot</span>
            </div>
        </div>

        <div class="reviews-grid">
            <?php

            $query = "SELECT * FROM customer_reviews ORDER BY review_date DESC LIMIT 6";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['name'];
                $rating = $row['rating'];
                $review_text = $row['review_text'];
                $review_date = date('j M, Y', strtotime($row['review_date']));

                echo '<div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <h4>' . htmlspecialchars($name) . '</h4>
                            <div class="rating">';

                for ($i = 1; $i <= floor($rating); $i++)
                    echo '<i class="fas fa-star"></i>';
                if ($rating - floor($rating) >= 0.5)
                    echo '<i class="fas fa-star-half-alt"></i>';
                for ($i = ceil($rating); $i < 5; $i++)
                    echo '<i class="far fa-star"></i>';

                echo '      </div>
                        </div>
                        <span class="review-date">' . $review_date . '</span>
                    </div>
                    <p class="review-text">"' . htmlspecialchars($review_text) . '"</p>
                </div>';
            }
            ?>
        </div>
    </div>
</section>


<!-- Promotional Offers -->
<section class="promo-offers">
    <div class="container">
        <div class="offers-grid">
            <div class="offer-card dark">
                <div class="offer-content">
                    <div class="offer-badge">NEW OFFER</div>
                    <h3>Free Delivery on orders above RS 225</h3>
                    <p>Valid for 2 hours</p>
                    <button class="btn light-btn">Shop Now</button>
                </div>
                <div class="offer-image">
                    <img src="https://i.pinimg.com/736x/de/37/57/de3757fcb63f6e201dde0cdafe0e74c9.jpg"
                        alt="Delivery Person">
                </div>
            </div>
            <div class="offer-card green">
                <div class="offer-content">
                    <div class="offer-badge">RECOMMENDED</div>
                    <h3>Subscribe & get a 5% discount on your health card</h3>
                    <button class="btn light-btn">Subscribe</button>
                </div>
                <div class="offer-image">
                    <img src="https://i.pinimg.com/736x/72/e5/70/72e5709238cf9f95f067df4286fbd420.jpg"
                        alt="Health Card">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="faq-sectioncontainer">
        <h2>Got questions?</h2>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>How do I order medicines online?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>You can order medicines by uploading your prescription or searching for the medicine on our
                        website or app. Add the items to your cart and proceed to checkout.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Are your online doctor consultations valid?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Yes, all our online doctor consultations are valid and conducted by licensed medical
                        professionals.</p>
                </div>
            </div>
            <div class="faq-item active">
                <div class="faq-question">
                    <h3>What happens if I don't get a response from a doctor?</h3>
                    <span class="faq-toggle"><i class="fas fa-minus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>If you don't get a response from a doctor within the specified time, we will refund your
                        consultation fee or reschedule your appointment at your convenience.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Can I get an online doctor consultation on Medicare?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Yes, you can get an online doctor consultation on Medicare. Our platform supports Medicare for
                        eligible consultations.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include("./includes/footer.php");
?>