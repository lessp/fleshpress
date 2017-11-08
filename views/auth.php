<?php

    $app->get('/auth', 'isAuthed', function($req, $res) {
        $categories = Category::findAll();
        $tags = Tags::findAll();

        $res->render_template('auth.html', [
            'user' => $req->session->user, 
            'req' => $req, 
            'categories' => $categories,
            'tags' => $tags
        ]);
    });

    $app->get('/auth/logout', 'isAuthed', function($req, $res) {
        unset($req->session->user);

        $res->redirect('/auth');
    });

    $app->post('/auth', function($req, $res) {
        try {

            $userFound = User::find([
                'username' => $req->body['username']
            ]);

            $password = $req->body['password'];
            
            if (password_verify($password, $userFound['password'])) {

                $req->session->user = [
                    'id' => $userFound['id'],
                    'first_name' => $userFound['first_name'],
                    'last_name' => $userFound['last_name'],
                    'username' => $userFound['username']
                ];
            
            }
            
            $res->redirect('/auth');

        } catch (Exception $err) {
            $res->render_template('error.html', [
                'status_code' => 400, 
                'message' => $err->getMessage()
            ], 400);
        }
    });

    $app->post('/user', 'requireLogin', function($req, $res) {
        try {

            $hashedPassword = generateHashedPassword($req->body['password']);
            
            $newUser = new User(
                $req->body['first_name'],
                $req->body['last_name'],
                $req->body['username'],
                $hashedPassword
            );

            $newUser->save();

            $res->redirect('/auth');

        } catch (Exception $err) {
            $res->render_template('error.html', ['message' => $err->getMessage()], 500);
        }
    });

    $app->get('/psst', 'requireLogin', function ($req, $res) {
        try {
            $userFound = User::findOneById($req->session->user['id']);
    
            $res->render_template('psst.html', ['user' => $req->session->user]);
        } catch (Exception $err) {
            $res->json(["message" => "Oops. Something went wrong."]);
        }
    });

?>