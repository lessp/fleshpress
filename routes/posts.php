<?php

$app->get('/posts', function($req, $res) {

    $post = Post::findOneById(1);
    
    $categories = Category::findAll();
    $posts = Post::findAll();

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

                $tags = [];
                foreach($req->body['tag'] as $key => $tag) {
                    $tags[$key] = $tag;
                }

                $newPost = new Post(
                    $req->body['title'], 
                    $req->body['content'],
                    $req->session->user['id']
                );
                
                $postToReturn = $newPost->save();
    
                $addPostCategory = new PostCategory(
                    $postToReturn['id'],
                    $req->body['category_id']
                );

                $addPostCategory->save();

                foreach($tags as $key => $tag) {
                    $tagToSave = new PostTags(
                        $postToReturn['id'],
                        $key
                    );
                    $tagToSave->save();
                }
    
                $res->redirect('/posts');
        }

        $res->redirect('/auth');

    } catch (Exception $err) {
        $res->json($err->getMessage(), 400);
    }
});

$app->get('/post/:id', 'isAuthed', function($req, $res) {
    try {

        // $post = Post::findOneById($req->params['id']);
        $post = Post::findOneById($req->params['id']);
        $categories = Category::findAll();
        $tags = Tags::findAll();

        $res->render_template('post.html', [
            'post' => $post, 
            'categories' => $categories,
            'tags' => $tags,
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
        $tags = $req->body['tag'];

        $updatedPost = Post::findByIdAndUpdate($postId, [
            'title' => $req->body['title'],
            'content' => $req->body['content'],
            'user_id' => $req->session->user['id']
        ]);

        $updatedPostCategory = PostCategory::findByIdAndUpdate($postId, [
            'category_id' => $req->body['category_id']
        ], false, 'post_id');

        $deleteCurrentTags = PostTags::delete(['post_id' => (int) $postId]);

        foreach($tags as $key => $tag) {
            $tagToSave = new PostTags(
                $postId,
                $key
            );
            $tagToSave->save();
        }

        $res->redirect('/post/' . $postId);

    } catch (Exception $err) {
        $res->render_template('error.html', [
            'status_code' => 500, 
            'message' => $err->getMessage()
        ], 500);
    }
});

$app->delete('/post/:id', 'requireLogin', function($req, $res) {
    try {
        $deleted = Post::deleteOneById((int) $req->params['id']);

        if ($deleted) {
            $res->redirect('/posts');
        }
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
            'status_code' => 404, 
            'message' => $err->getMessage()
        ], 500);
    }
});

$app->get('/tag/:id', function($req, $res) {
    try {

        $tagID = $req->params['id'];
        
        $postsToFind = PostTags::findById($tagID);
        $tags = Tags::findAll();
        $tagName = Tags::findOneById($tagID);

        $posts = [];
        foreach($postsToFind as $post) {
            $posts[] = Post::findOneById($post['post_id']);
        }

        $res->render_template('tag.html', [
            'posts' => $posts, 
            'tags' => $tags, 
            'tagId' => $tagID,
            'tagName' => $tagName['name'],
            'req' => $req
        ]);

    } catch (Exception $err) {
        $res->render_template('error.html', [
            'status_code' => 404, 
            'message' => "Pretty sure that route does not exist. Duh!"
        ], 404);
    }
});

// $app->get('/posts/:user/:userId', function($req, $res) {

//     if ($req->params['user'] !== 'user') {
//         $res->render_template('error.html', [
//             'status_code' => 404, 
//             'message' => "Pretty sure that route does not exist. Duh!"
//         ], 404);
//     }

//     try {

//         $res->send('Yeah yeah..');

//     } catch (Exception $err) {
//         $res->render_template('error.html', [
//             'status_code' => 500, 
//             'message' => $err->getMessage()
//         ], 500);   
//     }
// });

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