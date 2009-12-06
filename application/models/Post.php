<?php
/**
 *
 * @Model_Post Class
 *
 * @package Ultralite
 * @Author Dennis Mooibroek
 *
 */

class Model_Post
{
    /**
     *
     * The constructor, duh
     *
     */
    public function __construct()
    {
        $this->config = $config;
        $this->posts = $posts;
    }

    /**
     * Gets the information of an image specified by the image_id
     * as well as getting the previous and next image.
     *
     * Adds a new array post to the variable $this. This new array 
     * will contain the current, previous and next post information
     *
     * @param $id int id of the image to get. if is empty we grab the current image
     *
     * @access public
     *
     * @return $this->posts
     */
    public static function getDetails($id)
    {
        /**
         * Since the variables $config and $post are public we don't have to pass them
         * to the function
         */
        
        /**
         * Determine the image ID is a positive integer:
         */
        $id = ($id > 0) ? $id : 0;

        /**
         * Check if there is a Current Image, else get Current image
         */
        if (!is_object($this->posts['current'])) {
            if (empty($id)) {
                // If no ID is specified, grab the latest image:
                $sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT 0,1";
            } else {
                $sql = "SELECT * FROM `pixelpost` WHERE `id` = '$id' AND `published` <= '{$this->config->current_time}' LIMIT 0,1";
            }
            // Assign the current image to the $posts array
            $this->posts['current'] = Pixelpost_DB::get_row($sql);
        }

        /**
         * Verify that the image exists, either from a plugin or from the code above:
         */
        if (!is_object($this->posts['current'])) {
            // Error? Splash Screen?
            throw new Exception("Whoops, we don't have anything to show on this page right now, please to back to the <a href=\"?\">home page</a>.");
        }

        /**
         * Check if Next Image exists, else get Next image
         */
        if (!is_object($this->posts['next'])) {
            $sql = "SELECT * FROM `pixelpost` WHERE (`published` < '{$this->posts['current']->published}') and (`published` <= '{$this->config->current_time}') ORDER BY `published` DESC LIMIT 0,1";

            $this->posts['next'] = Pixelpost_DB::get_row($sql);

            /**
             * If we are on the last image, there isn't a next image, 
             * so we can wrap around to the first image:
             */
            if (!is_object($this->posts['next'])) {
                $sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT 0,1";

                $this->posts['next'] = Pixelpost_DB::get_row($sql);
            }
        }

        /**
         * Check if Previous Image exists, else get Previous image
         */
        if (!is_object($this->posts['previous'])) {
            $sql = "SELECT * FROM `pixelpost` WHERE (`published` > '{$this->posts['current']->published}') and (`published` <= '{$this->config->current_time}') ORDER BY `published` ASC LIMIT 0,1";

            $posts['previous'] = Pixelpost_DB::get_row($sql);

            /**
             * If the first image, we can't go back any further, 
             * so we can wrap around to the last image:
             */
            if (!is_object($this->posts['previous'])) {
                $sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` ASC LIMIT 0,1";

                $posts['previous'] = Pixelpost_DB::get_row($sql);
            }
        }
        return $this->posts;
    }
}
 /*** end of class ***/
