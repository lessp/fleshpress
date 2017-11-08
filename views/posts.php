<?php

$app->get('/posts', function($req, $res) {

    $post = Post::findById(1);
    
    $posts = Post::findAll();
    $categories = Category::findAll();
    $postCategories = PostCategory::findAll();

    foreach ($posts as $key => &$newPost) {
        foreach ($postCategories as $postCategory) {
            if ($newPost['id'] === $postCategory['post_id']) {
                foreach($categories as $category) {
                    if($category['id'] === $postCategory['category_id']) {
                        $newPost['category'] = $category['name'];
                        $newPost['category_id'] = $category['id'];
                    }
                }
            }
        }
    }

    $updatedPost = Post::findByIdAndUpdate(1, [
        'title' => 'Woohoo, brand new title!',
        'content' => 'And some kind of, half new content!'
    ], true);

    $res->render_template('posts.html', [
        'posts' => $posts, 
        'post' => $post, 
        'updatedPost' => $updatedPost,
        'categories' => $categories,
        'isAuthed' => $req->isAuthed
    ]);
});

$app->post('/posts', 'requireLogin', function($req, $res) {
    try {

        if (! empty($req->body['title']) &&
            ! empty($req->body['content'])) {

                $newPost = new Post(
                    $req->body['title'], 
                    $req->body['content'],
                    $req->cookies['id']
                );
                
                $postToReturn = $newPost->save();
    
                $addPostCategory = new PostCategory(
                    $postToReturn['id'],
                    $req->body['category_id']
                );
    
                $addPostCategory->save();

                $res->redirect('/posts');
        }

        $res->redirect('/auth');

    } catch (Exception $err) {
        $res->json($err->getMessage(), 400);
    }
});

$app->get('/post/:id', 'isAuthed', function($req, $res) {
    try {

        $post = Post::findOneById($req->params['id']);
        $categories = Category::findAll();
        $postCategory = PostCategory::find(['post_id' => $req->params['id']]);

        $res->render_template('post.html', [
            'post' => $post, 
            'categories' => $categories,
            'postCategory' => $postCategory['category_id'],
            'req' => $req, 
            'isAuthed' => $req->isAuthed]
        );

    } catch (Exception $err) {
        $res->render_template('error.html', [
            'status_code' => 500, 
            'message' => $err->getMessage()
        ], 500);
    }
});

$app->put('/post/:id', 'requireLogin', function($req, $res) {
    try {

        $postId = $req->params['id'];
        $categoryId = $req->body['category_id'];

        $updatedPost = Post::findByIdAndUpdate($postId, [
            'title' => $req->body['title'],
            'content' => $req->body['content'],
            'user_id' => $req->session->user['id']
        ]);

        $updatedPostCategory = PostCategory::findByIdAndUpdate($postId, [
            'category_id' => $req->body['category_id']
        ], false, 'post_id');

        $res->redirect('/post/' . $postId);

    } catch (Exception $err) {
        $res->render_template('error.html', [
            'status_code' => 500, 
            'message' => $err->getMessage()
        ], 500);
    }
});

$app->get('/category/:id', function($req, $res) {
    try {

        $categoryID = $req->params['id'];
        
        $postCategories = PostCategory::find(['category_id' => $categoryID]);
        $categoryName = Category::findOneById($categoryID);

        $categories = Category::findAll();

        $posts = [];
        if (! empty($postCategories)) {
            $i = 0;
            foreach($postCategories as $postCategory) {
                $posts[$i] = Post::findOneById($postCategory['post_id']);
                $posts[$i]['category'] = $categoryName['name'];
                $i++;
            }
        }

        $res->render_template('category.html', [
            'posts' => $posts, 
            'categories' => $categories, 
            'categoryId' => $categoryID,
            'categoryName' => $categoryName['name'],
            'req' => $req
        ]);

    } catch (Exception $err) {
        $res->render_template('error.html', [
            'status_code' => 500, 
            'message' => $err->getMessage()
        ], 500);
    }
});

$app->get('/posts/:userID/:categoryID', function($req, $res) {
    try {

        $userID = $req->params['userID'];
        $categoryID = $req->params['categoryID'];

        $postCategories = PostCategory::find(['category_id' => $categoryID]);

        if (empty($postCategories)) {
            throw new Exception("Woops. The arguments are probably out of range. Try again!");
        }

        $postsWithUserAndCategory = [];
        foreach($postCategories as $postCategory) {
            $postsWithUserAndCategory[] = Post::find(['id' => $postCategory['post_id'], 'user_id' => $userID]);
        }
        
        $category = Category::find(['id' => $categoryID]);   
        
        foreach($postsWithUserAndCategory as &$newPost) {
            $newPost['category'] = $category['name'];
        }
        
        $res->json(['posts' => $postsWithUserAndCategory], 200);

    } catch (Exception $err) {
        $res->render_template('error.html', [
            'status_code' => 500, 
            'message' => $err->getMessage()
        ], 500);
        // $res->json($err->getMessage(), 400);
    }
});

?>