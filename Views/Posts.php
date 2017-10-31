<?php

    /* Header */
    include('Layout/header.php');

    echo '<h1>posts.php</h1>';

    echo '<pre>';
        print_r($params);
    echo '</pre>';

    foreach($params['posts'] as $post)
    {
        echo '<h2>' . $post['title'] . '</h2>';
        echo '<span>' . $post['author'] . '</span>';
        echo '<p>' . $post['content'] . '</p>';
    }

    /* Footer */
    include('Layout/footer.php');
?>