<?php
/**
 * Controller for the archive page
 *
 * @package Pixelpost
 * @author Dennis Mooibroek 
 *
 *
 */

// namespace web2bb;

class archiveController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		/**
		 * Get all the variables from the database
		 */
		$archive = new stdClass;
		$this->view->title = 'The Past';

		// If another controller has already created a query,
		// run with that, rather than create our own:
		if (!isset($archive->thumbnails))
		{
			if ($this->config->pagination > 0)
			{

				$sql = "SELECT count(`id`) FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}'";
				// Get total images publically available
				$image->total = (int)$db->get_var($sql);
				// Determine the total number of available pages
				$this->config->total_pages = (int)ceil($image->total / $this->config->pagination);

				// The page doesn't exist!
				if ($this->config->total_pages < $this->config->page)
				{
					throw new Exception("Sorry, we don't have anymore pages to show!");
				}

				// The database needs to know which row we need to start with:
				$range = (int)($this->config->page - 1) * $this->config->pagination;

				$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC LIMIT $range, $this->config->pagination";
			}
			else
			{
				$sql = "SELECT * FROM `pixelpost` WHERE `published` <= '{$this->config->current_time}' ORDER BY `published` DESC";
			}

			// Store the thumbnails array
			$archive->thumbnails = Pixelpost_DB::get_results($sql);

		}

		// Tack on thumbnail data to the thumbnails array
		foreach ($archive->thumbnails as $key => $thumbnail)
		{
			$image_info = getimagesize('content/images/thumb_' . $thumbnail->filename);

			$archive->thumbnails[$key]->width = $image_info[0];
			$archive->thumbnails[$key]->height = $image_info[1];
			$archive->thumbnails[$key]->dimensions = $image_info[3];
		}
		// create thumbnails list
		foreach ($archive->thumbnails as $thumbnail)
		{
			$archive->thumbnails_output .= ("<a href=\"post/" . $thumbnail->id . "\">" . "<img src=\"content/images/thumb_{$thumbnail->filename}\" alt=\"" . escape($thumbnail->title) . "\" width=\"{$thumbnail->width}\" height=\"{$thumbnail->height}\" />" . "</a>");
		}
		
		/**
		 * Assign the variables to be used in the view
		 * $this->view-myVar can be accessed in the template as $myVar
		 */
		
		$this->view->archive = $archive;
	}
}
