<?php
/**
 * Any code used to create the variables and functions necessary 
 * for the post (image) template should go in here.
 * 
 * For example, the code which figures out the current, next, and previous images.
 * 
 * Proposed URL Structure:
 * 
 * # Show Image #3
 * ?view=post&id=3
 * 
 * # Show Image #3 (mod_rewrite)
 * /post/3
 * 
 * # Show Image #3 with optional slug (mod_rewrite)
 * /post/3/my-photo-title
 * 
 * Thought... the post view is special, so it should be the default view.  
 * If nothing else is specified, the script should fall back to that view.
 * So URLs like this should work in theory:
 *
 * # Show Image #3
 * ?id=3
 * 
 * @package ultralite
 **/




?>