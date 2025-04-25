<?php include 'header.php'; ?>

<div style="padding: 20px; margin-left: 20%;">
    <h1>Settings</h1>
    <p>Use this section to manage your site settings.</p>

    <!-- Example Settings Form -->
    <form action="save_settings.php" method="post">
        <div style="margin-bottom: 15px;">
            <label for="site_title" style="display:block; margin-bottom:5px;">Site Title:</label>
            <input type="text" name="site_title" id="site_title" placeholder="Enter site title" style="width:100%; padding:8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="site_description" style="display:block; margin-bottom:5px;">Site Description:</label>
            <textarea name="site_description" id="site_description" rows="4" placeholder="Enter site description" style="width:100%; padding:8px;"></textarea>
        </div>
        <div style="margin-bottom: 15px;">
            <label for="contact_email" style="display:block; margin-bottom:5px;">Contact Email:</label>
            <input type="email" name="contact_email" id="contact_email" placeholder="Enter contact email" style="width:100%; padding:8px;">
        </div>
        <button type="submit" style="padding: 10px 15px; background-color: #2c3e50; color: #fff; border: none; cursor: pointer;">Save Settings</button>
    </form>
</div>

</div> 
</body>
</html>
