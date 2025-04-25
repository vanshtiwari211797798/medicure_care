<?php
include("../includes/conn.php");
include("header.php");

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM customer_reviews WHERE id=$delete_id");
    header("Location: admin-reviews.php");
}

// Handle update form submission
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];
    $review_date = $_POST['review_date'];

    $update_query = "UPDATE customer_reviews SET 
        name='$name', 
        rating='$rating', 
        review_text='$review_text', 
        review_date='$review_date' 
        WHERE id=$id";

    mysqli_query($conn, $update_query);
    header("Location: admin-reviews.php");
}

// If editing, fetch review
$edit_mode = false;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_mode = true;
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customer_reviews WHERE id=$edit_id"));
}

// Fetch all reviews
$result = mysqli_query($conn, "SELECT * FROM customer_reviews ORDER BY review_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6bff;
            --danger-color: #ff4757;
            --success-color: #2ed573;
            --text-color: #333;
            --light-gray: #f8f9fa;
            --border-color: #e0e0e0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f5f7fb;
            padding: 0;
            margin: 0;
        }
        
        .admin-container {
            width: 1200px;
            margin-left: 20%;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        h3 {
            color: #2c3e50;
            margin-top: 30px;
        }
        
        /* Form Styling */
        .edit-form {
            background: var(--light-gray);
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.1);
        }
        
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        /* Button Styling */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #3a5bed;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #e8414d;
        }
        
        /* Table Styling */
        .reviews-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .reviews-table th {
            background-color: var(--primary-color);
            color: white;
            text-align: left;
            padding: 12px 15px;
        }
        
        .reviews-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }
        
        .reviews-table tr:nth-child(even) {
            background-color: var(--light-gray);
        }
        
        .reviews-table tr:hover {
            background-color: #e9f0ff;
        }
        
        .rating-stars {
            color: #ffc107;
            font-size: 14px;
        }
        
        .action-links a {
            color: var(--primary-color);
            margin-right: 10px;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .action-links a:hover {
            color: #3a5bed;
            text-decoration: underline;
        }
        
        .action-links a.delete-link {
            color: var(--danger-color);
        }
        
        .action-links a.delete-link:hover {
            color: #e8414d;
        }
        
        /* Responsive Table */
        @media (max-width: 768px) {
            .reviews-table {
                display: block;
                overflow-x: auto;
            }
            
            .edit-form {
                padding: 15px;
            }
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2><i class="fas fa-comment-alt"></i> Manage Customer Reviews</h2>

        <?php if ($edit_mode): ?>
            <div class="edit-form">
                <h3><i class="fas fa-edit"></i> Edit Review</h3>
                <form method="post">
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" name="name" id="name" value="<?= htmlspecialchars($edit_data['name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="rating">Rating (0-5):</label>
                        <input type="number" name="rating" id="rating" value="<?= $edit_data['rating'] ?>" min="0" max="5" step="0.5" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="review_text">Review:</label>
                        <textarea name="review_text" id="review_text" required><?= htmlspecialchars($edit_data['review_text']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="review_date">Date:</label>
                        <input type="date" name="review_date" id="review_date" value="<?= $edit_data['review_date'] ?>" required>
                    </div>
                    
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Review
                    </button>
                    <a href="admin-reviews.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </form>
            </div>
        <?php endif; ?>

        <table class="reviews-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <div class="rating-stars">
                                <?php 
                                $fullStars = floor($row['rating']);
                                $halfStar = ($row['rating'] - $fullStars) >= 0.5 ? 1 : 0;
                                $emptyStars = 5 - $fullStars - $halfStar;
                                
                                for ($i = 0; $i < $fullStars; $i++) {
                                    echo '<i class="fas fa-star"></i>';
                                }
                                if ($halfStar) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                }
                                for ($i = 0; $i < $emptyStars; $i++) {
                                    echo '<i class="far fa-star"></i>';
                                }
                                ?>
                                <span>(<?= $row['rating'] ?>)</span>
                            </div>
                        </td>
                        <td><?= nl2br(htmlspecialchars($row['review_text'])) ?></td>
                        <td><?= date('M j, Y', strtotime($row['review_date'])) ?></td>
                        <td class="action-links">
                            <a href="admin-reviews.php?edit=<?= $row['id'] ?>">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="admin-reviews.php?delete=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this review?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Confirm before deleting
        document.querySelectorAll('.delete-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this review?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>