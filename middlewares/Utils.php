<?php

function requireLogin($req, $res) {

    if (isset($req->session->user)) {
        $req->isAuthed = true;
    } else {
        $req->isAuthed = false;
    }

    if (! $req->isAuthed) {
        $res->render_template('error.html', [
            'status_code' => 401, 
            'message' => "Oops, seems as though you're not authorized to view this page."
        ], 401);
    }
}

?>