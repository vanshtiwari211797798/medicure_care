<?php
include("includes/header.php");
include("includes/conn.php");

// Fetch data from the 'aboutus' table
$query = "SELECT * FROM aboutus";
$result = mysqli_query($conn, $query);
$about = mysqli_fetch_assoc($result);
?>

<!-- About Us Section -->
<section class="about">
    <div class="about_container">
        <h2>About MediCare</h2>
        <div class="about-content">
            <div class="about-image">
                <img src="./admin/<?= htmlspecialchars($about['image']) ?>" alt="About MediCare" loading="lazy">
            </div>
            <div class="about-text">
                <h3><?= htmlspecialchars($about['title1']) ?></h3>
                <p><?= htmlspecialchars($about['content1']) ?></p>

                <h3><?= htmlspecialchars($about['title2']) ?></h3>
                <ul>
                    <li><?= htmlspecialchars($about['list_item1']) ?></li>
                    <li><?= htmlspecialchars($about['list_item2']) ?></li>
                    <li><?= htmlspecialchars($about['list_item3']) ?></li>
                    <li><?= htmlspecialchars($about['list_item4']) ?></li>
                    <li><?= htmlspecialchars($about['list_item5']) ?></li>
                </ul>

                <h3><?= htmlspecialchars($about['title3']) ?></h3>
                <p><?= htmlspecialchars($about['content2']) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<?php include("includes/footer.php"); ?>