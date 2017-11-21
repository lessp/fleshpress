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
        $req->session->user = null;

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

    $app->get('/user/:forgot-password', function($req, $res) {
        
        if (! $req->params['forgot-password']) {
            $res->redirect('/auth');
        }

        $res->render_template('forgot-password.html');
    });

    $app->post('/user/:forgot-password', function($req, $res) {

        if (! $req->params['forgot-password']) {
            $res->redirect('/auth');
        }

        $userFound = User::find([
            'email' => $req->body['email']
        ]);

        if (! empty($userFound)) {

            $token = new PasswordToken(
                bin2hex(random_bytes(40)),
                $userFound['id'],
                new DateTime()
            );

            $tokenThatWasSaved = $token->save();

            $emailMsg = 'Follow the link below in order to reset your password. 
            http://05.tomekander.chas.academy/reset-password/' . $tokenThatWasSaved['token'] . 
            "\n\nThis token will expire in one hour.";

            $emailMsg = wordwrap($emailMsg, 70);
            $headers = 'From: tom.ekander@chasacademy.se' . "\r\n" .
            'Reply-To: tom.ekander@chasacademy.se' . "\r\n";

            mail($userFound['email'], "Reset Password - 05.tomekander.chas.academy", $emailMsg, $headers);

            $res->redirect('/auth');
        }

    });

    $app->get('/reset-password/:token', function($req, $res) {
        $tokenFound = PasswordToken::find(['token' => $req->params['token']]);

        if (! empty($tokenFound)) {
            if ($tokenFound['used'] || new DateTime() > date_create_from_format('Y-m-d H:i:s', $tokenFound['expires'])) {
                $res->redirect('/');
            }

            $user = User::findOneById($tokenFound['user_id']);

            $res->render_template('reset-password.html', [
                'token' => $tokenFound['token'], 
                'userId' => $user['id']
            ]);
        }
    });

    $app->post('/reset-password', function($req, $res) {
        $tokenFound = PasswordToken::find(['token' => $req->body['token']]);

        if (! empty($tokenFound)) {
            if ($tokenFound['used'] || new DateTime() > date_create_from_format('Y-m-d H:i:s', $tokenFound['expires'])) {
                $res->redirect('/');
            }

            $userFound = User::findOneById($req->body['user_id']);

            $hashedPassword = generateHashedPassword($req->body['password']);
            
            PasswordToken::findByIdAndUpdate($tokenFound['id'], ['used' => 1]);
            User::findByIdAndUpdate($userFound['id'], ['password' => $hashedPassword]);

            $res->redirect('/auth');

        }

    });

?>