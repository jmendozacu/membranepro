<?php
/*
Plugin Name: Limit Posts
Plugin URI: http://labitacora.net/comunBlog/limit-post.phps
Description: Limits the displayed text length on the index page entries and generates a link to a page to read the full content if its bigger than the selected maximum length.
Usage: the_content_limit($max_charaters, $more_link)
Version: 1.1
Author: Alfonso Sánchez-Paus Díaz y Julián Simón de Castro
Author URI: http://labitacora.net/
License: GPL
Download URL: http://labitacora.net/comunBlog/limit-post.phps
Make:
    In file index.php
    replace the_content()
    with the_content_limit(1000, "more")
*/

function the_content_limit($max_char, $more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
    $content = get_the_content($more_link_text, $stripteaser, $more_file);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    $content = strip_tags($content);

   if (strlen($_GET['p']) > 0) {
      echo "<div>";
      echo $content;
      echo "</div>";
   }
   else if ((strlen($content)>$max_char) && ($espacio = strpos($content, " ", $max_char ))) {
        $content = substr($content, 0, $espacio);
        $content = $content;
        echo "<div>";
        echo $content;
        echo "...";
        echo "</div>";
   }
   else {
      echo "<div>";
      echo $content;
      echo "</div>";
   }
}

?>
