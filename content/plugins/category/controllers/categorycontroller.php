<?php

/**
 * Example Controller
 *
 * @package Pixelpost
 * @author Jay Williams 
 */

class categoryController extends baseController implements IController
{
	private $path = __IMAGE_PATH;

	public function index()
	{
		/**
		 * only interested in the latest category as we can get the rest
		 * from SQL if needed
		 */
		$cats = new Pixelpost_Hierarchy('categories');
		$Uri_fragment = Web2BB_Uri::numberOfFragments() - 1;
		$category = ucfirst(Web2BB_Uri::fragment($Uri_fragment));
		if ($category != 'Category')
		{
			// show the images from the category
			// in case it isn't a leaf we need to select the subcategories as well
			$sql = "SELECT left_node, right_node FROM categories WHERE name='" . $category . "'";
			$result = (array )Pixelpost_DB::get_row($sql);
			if (empty($result)) throw new Exception("Sorry, that category doesn't exists!");
			$sql = "SELECT pixelpost.* FROM img2cat, categories, pixelpost 
			WHERE categories.left_node BETWEEN " . $result['left_node'] . " 
				AND " . $result['right_node'] . "
				AND pixelpost.published <= '{$this->config->current_time}'
				AND categories.category_id = img2cat.category_id AND img2cat.image_id = pixelpost.id
				ORDER BY pixelpost.published";
			$this->posts = (array )Pixelpost_DB::get_results($sql);
			
			
			/**
			 * Run the posts through the Plugin system, and apply any 
			 * necessary data before sending the array to the view.
			 */
			$this->processPosts();
			
			
			//var_dump($this->posts);
			$this->view->title = 'Viewing category ' . $category;
		}
		else
		{
			throw new Exception("Here comes the album view");

		}

		// Page Title

		$this->view->thumbnails = $this->_thumbnails();
		$this->view->posts = $this->posts;
	}

	protected function _thumbnails()
	{
		// create thumbnails list
		$thumbnails = '';
		foreach ($this->posts as $post)
		{
			$thumbnails .= "<a href=\"$post->permalink\">" . "<img src=\"{$post->thumb_uri}\" alt=\"" . escape($post->title) . "\" width=\"{$post->thumb_width}\" height=\"{$post->thumb_height}\" />" . "</a>";
		}
		return $thumbnails;
	}
}
