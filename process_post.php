<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if post_type is set and not empty
    if (isset($_POST["post_type"]) && !empty($_POST["post_type"])) {
        // Get the submitted post type (learn or teach)
        $postType = $_POST["post_type"];
        // Get the submitted topic and description
        $topic = $_POST["post_topic"];
        $description = $_POST["post_description"];

        // Display the submitted post
        echo '<div class="card mb-4">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . $topic . '</h5>';
        echo '<p class="card-text">' . $description . '</p>';
        echo '</div>';
        echo '<div class="card-footer text-muted">';
        echo 'Post Type: ' . ucfirst($postType);
        echo '</div>';
        echo '</div>';
    }
}
?>
