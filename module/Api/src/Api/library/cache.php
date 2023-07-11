<?php
namespace Api\library;

class cache
{

    public function cache_top(){
        $url = $_SERVER["SCRIPT_NAME"];
        $break = Explode('/', $url);
        $file = $break[count($break) - 1];
        $cachefile = 'cached-'.substr_replace($file ,"",-4).'.html';
        $cachetime = 18000;
        // Serve from the cache if it is younger than $cachetime
        if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
            echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
            include($cachefile);
            exit;
        }
        ob_start(); // Start the output buffer

    }
    public function cache_bottom(){
        $url = $_SERVER["SCRIPT_NAME"];
        $break = Explode('/', $url);
        $file = $break[count($break) - 1];
        $cachefile = 'cached-'.substr_replace($file ,"",-4).'.html';
        // Cache the contents to a file
        $cached = fopen($cachefile, 'w');
        fwrite($cached, ob_get_contents());
        fclose($cached);
        ob_end_flush(); // Send the output to the browser
    }

}