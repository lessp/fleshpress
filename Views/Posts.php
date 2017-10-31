<?php

    /* Header */
    // include('Layout/header.php');

    echo '<h1>template -- ./views/posts.php</h1>';
    echo '<hr>';

    echo '<h2>Post::findAll()</h2>';
    foreach($params['posts'] as $post) {
        echo '<span>ID: ' . $post['id'] . '</span>';
        echo '<h3>' . $post['title'] . '</h3>';
        echo '<span>' . $post['author'] . '</span>';
        echo '<p>' . $post['content'] . '</p>';
    }

    echo '<hr>';

    echo '<h2>Post::findById(1)</h2>';
    foreach($params['post'] as $post) {
        echo '<span>ID: ' . $post['id'] . '</span>';
        echo '<h3>' . $post['title'] . '</h3>';
        echo '<span>' . $post['author'] . '</span>';
        echo '<p>' . $post['content'] . '</p>';
    }

    echo '<hr>';

    echo '<h2>$params</h2>';
    echo '<pre>';
        print_r($params);
    echo '</pre>';

    /* Footer */
    // include('Layout/footer.php');
?>