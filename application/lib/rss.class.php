<?php

/**
 *
 * A class to create RSS feeds using DOM
 *
 * @Author Kevin Waterson
 *
 * @copyright 2009
 *
 * @license BSD
 *
 */

// namespace web2bb;

class rss extends DomDocument
{
	/**
	 * @ the RSS channel
	 */
	private $channel;

	/**
	 *
	 * @Constructor, duh! Set up the DOM environment
	 *
	 * @access public
	 *
	 * @param string $title The site title
	 *
	 * @param string $link The link to the site
	 *
	 * @param string $description The site description
	 *
	 */
	public function __construct($title, $link, $description)
	{
		// call the parent constructor
		parent::__construct();

		// format the created XML
		$this->formatOutput = true;

		// craete the root element
		$root = $this->appendChild($this->createElement('rss'));

		// set to rss2
		$root->setAttribute('version', '2.0');

		// set the channel node
		$this->channel = $root->appendChild($this->createElement('channel'));

		// set the title link and description elements 
		$this->channel->appendChild($this->createElement('title', $title));
		$this->channel->appendChild($this->createElement('link', $link));
		$this->channel->appendChild($this->createElement('description', $description));
	}


	/**
	 *
	 * @Add Items to the RSS Feed
	 *
	 * @access public
	 *
	 * @param array $items
	 *
	 */
	public function addItem($items)
	{
		// create an item
		$item = $this->createElement('item');
		foreach($items as $element=>$value)
		{
			switch($element)
			{
				// create sub elements here
				case 'image':
				case 'skipHour':
				case 'skipDay':
				$im = $this->createElement('image');
				$this->channel->appendChild($im);
				foreach( $value as $sub_element=>$sub_value )
				{
					$sub = $this->createElement($sub_element, $sub_value);
					$im->appendChild( $sub );
				}
				break;

				case 'title':
				case 'pubDate':
				case 'link':
				case 'description':
				case 'copyright':
				case 'managingEditor':
				case 'webMaster':
				case 'lastbuildDate':
				case 'category':
				case 'generator':
				case 'docs':
				case 'language':
				case 'cloud':
				case 'ttl':
				case 'rating':
				case 'textInput':
				case 'source':
				$new = $item->appendChild($this->createElement($element, $value));
				break;
			}
		}
		// append the item to the channel
		$this->channel->appendChild($item);

		// allow chaining 
		return $this;
	}

	/***
	 *
	 * @create the XML
	 *
	 * @access public
	 *
	 * @return string The XML string
	 *
	 */
	public function __toString()
	{
		return $this->saveXML();
	}
}

/*** the first article item ***/
$item1 = array(
	'title'=>'Zend Framework Example Site', 
	'link'=>'http://www.phpro.org/articles/Zend-Framework-Example-Site.html', 
	'description'=>'This example site hopes to introduce the newcomers to Zend Framework in a friendly way, by providing a simple modular site layout and can have the newcomer up and running in minutes.', 
	'pubDate'=>date(DATE_RSS),
	'image'=>array('link'=>'http://phpro.org', 'url'=>'http://phpro.org/images/spork.jpg', 'title'=>'SPORK'),
	'language'=>'en');

/*** second article item ***/
$item2 = array(
	'title'=>'Xajax-In An Object Oriented Environment',
	'link'=>'http://www.phpro.org/tutorials/Xajax-In-An-Object-Oriented-Environment.html',
	'description'=>'This tutorial takes the next step in development and shows, by way of example, how xajax can be utilized in an Object Oriented environment',
	'pubDate'=>date(DATE_RSS),
	'language'=>'en');

/*** third article item ***/
$item3 = array(
	'title'=>'Introduction to SPL DirectoryIterator',
	'link'=>'http://www.phpro.org/tutorials/Introduction-to-SPL-DirectoryIterator.html',
	'description'=>'The DirectoryIterator is one of the more oft used of the Iterator classes and this tutorial helps to expose the user to developing in a standardised and Object Oriented approach',
	'pubDate'=>date(DATE_RSS),
	'language'=>'en');

/*** a new RSS instance, pass values to the constructor ***/
$rss = new rss('PHPRO.ORG', 'http://phpro.org', 'PHP Articles Tutorials Examples Classes');

/*** add the items from above ***/
$rss	->addItem($item1)
	->addItem($item2)
	->addItem($item3);

/*** show the RSS Feed ***/
echo $rss;

?>
