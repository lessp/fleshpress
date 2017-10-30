<?php

    function render(string $template = null, array $params = [])
    {
        ob_start();
        if (file_exists($template)) 
        {
            include($template);
        }
        return print ob_get_clean();
    }

?>