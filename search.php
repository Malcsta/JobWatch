<?php
$categoriesQuery = "SELECT category_id, category_name FROM categories";
$categoriesStatement = $db->prepare($categoriesQuery);
$categoriesStatement->execute();
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="search">
    <form method="POST" action="searchResults.php">
        <label id="searchlabel" for="search">Search job listings:</label>
        <input type="text" id="searchinput" name="search" required>
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['category_id']) ?>">
                    <?= htmlspecialchars($category['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Search</button>
    </form>
</div>