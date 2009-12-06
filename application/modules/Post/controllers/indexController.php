<?php
/**
 * Controller for the post page (shows image)
 *
 * @package Pixelpost
 * @author Dennis Mooibroek 
 * @author Jay Williams
 */


// namespace web2bb;

class Module_Post_indexController extends Module_Base_baseController implements Model_Interface
{
	/**
	 * Path to image directory
	 *
	 * @var string
	 */
	private $path = IMGPATH;

	public $posts = array('previous'=>null,'current'=>null,'next'=>null);

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction()
	{
        /**
         * Get the id of the image from the url and proced to 
         * get the details for the posts.
         */
        $this->id = (int)Pixelpost_Uri::fragment(1);
        $this->posts = Model_Post::getDetails($this->id, $this->posts, $this->config);

        /**
         * Run the posts through the Plugin system, and apply any 
         * necessary data before sending the array to the view.
         */
        $this->processPosts();

        /**
         * Assign the variables to be used in the view
         * $this->view->myVar can be accessed in the template as $myVar
         */
        $this->view->title = $this->posts['current']->title;
        $this->view->posts = $this->posts;

        /**
         * Inclusion of the actual template needed is handled in the destruct
         * function of the base controller.
         */

	}
}
