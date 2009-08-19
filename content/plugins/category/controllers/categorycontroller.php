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
		$category = Web2BB_Uri::fragment($Uri_fragment);

		$sql = "SELECT pixelpost.* FROM img2cat, categories, pixelpost WHERE categories.name='" . $category . "' AND categories.category_id = img2cat.category_id AND img2cat.image_id = pixelpost.id";
		$this->posts = (array )Pixelpost_DB::get_results($sql);
		if (empty($this->posts)) throw new Exception("Sorry, that category doesn't exists!");

		// Tack on image data to the posts array
		foreach ($this->posts as $key => $post)
		{
			$this->posts[$key]->id = (int)$this->posts[$key]->id;
			$this->posts[$key]->permalink = $this->config->url . 'post/' . $post->id;

			$image_info = getimagesize($this->path . '/' . $post->filename);

			$this->posts[$key]->width = $image_info[0];
			$this->posts[$key]->height = $image_info[1];
			$this->posts[$key]->type = $image_info['mime'];
			$this->posts[$key]->uri = $this->config->url . $this->path . '/' . $post->filename;

			$image_info = getimagesize($this->path . '/thumb_' . $post->filename);

			$this->posts[$key]->thumb_width = $image_info[0];
			$this->posts[$key]->thumb_height = $image_info[1];
			$this->posts[$key]->thumb_type = $image_info['mime'];
			$this->posts[$key]->thumb_uri = $this->config->url . $this->path . '/thumb_' . $post->filename;

		}

		/**
		 * Allow any plugins to modify to adjust the posts before we apply the filters:
		 */
		Pixelpost_Plugin::executeAction('hook_posts', $this->posts);

		foreach ($this->posts as $key => $post)
		{
			Pixelpost_Plugin::executeFilter('filter_permalink', $this->posts[$key]->permalink);
			Pixelpost_Plugin::executeFilter('filter_title', $this->posts[$key]->title);
			Pixelpost_Plugin::executeFilter('filter_description', $this->posts[$key]->description);
			Pixelpost_Plugin::executeFilter('filter_filename', $this->posts[$key]->filename);
			Pixelpost_Plugin::executeFilter('filter_published', $this->posts[$key]->published);
		}
		//var_dump($this->posts);
		
		// Page Title
		$this->view->title = 'Viewing category '.$category;
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
	}}
