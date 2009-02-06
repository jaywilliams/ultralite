<?php

/* E X A M P L E -----------------------------------------------
    $feed = new RSS();
    $feed->title       = 'RSS Feed Title';
    $feed->link        = "http://website.com";
    $feed->description = 'Recent articles on your website.';

    $db->query($query);
    $result = $db->result;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        $item = new RSSItem();
        $item->title = $title;
        $item->link  = $link;
        $item->setPubDate($create_date);
        $item->description = $html;
        $feed->addItem($item);
    }
    $feed->serve();

    Or, you can eliminate the while loop above by using the
    built-in loadRecordset() method. It takes a MySQL result
    and the column names you want to use for the title, link,
    description, and pub-date.
---------------------------------------------------------------- */

class RSS
{
	public $title;
	public $link;
	public $description;
	public $language = 'en-us';
	public $pubDate;
	public $items;
	public $tags;
	
	function __construct()
	{
		$this->items = array();
		$this->tags  = array();
	}
	
	function addItem($item)
	{
		$this->items[] = $item;
	}
	
	function setPubDate($when)
	{
		if(empty($when) || ctype_digit($date) === true)
			$this->pubDate = date("D, d M Y H:i:s O", $when);
		else
			$this->pubDate = date("D, d M Y H:i:s O", strtotime($when));
	}
	
	function getPubDate()
	{
		if(empty($this->pubDate))
            return date("D, d M Y H:i:s O");
        else
            return $this->pubDate;
    }

    function addTag($tag, $value)
    {
        $this->tags[$tag] = $value;
    }

    function loadRecordset($result, $title, $link, $description, $pub_date)
    {
        while($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $item = new RSSItem();
            $item->title       = $row[$title];
            $item->link        = $row[$link];
            $item->description = "<![CDATA[ " . $row[$description] . "]]>";
            $item->setPubDate($row[$pub_date]);
            $this->addItem($item);
        }
    }

    function out()
    {
        $out  = $this->header();
        $out .= "<channel>\n";
        $out .= "<title>" . $this->title . "</title>\n";
        $out .= "<link>" . $this->link . "</link>\n";
        $out .= "<description>" . $this->description . "</description>\n";
        $out .= "<language>" . $this->language . "</language>\n";
        $out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";
        $out .= '<atom:link href="' . $this->full_url() . '" rel="self" type="application/rss+xml" />' . "\n";

        foreach($this->tags as $key => $val) $out .= "<$key>$val</$key>\n";
        foreach($this->items as $item) $out .= $item->out();

        $out .= "</channel>\n";

        $out .= $this->footer();

        return $out;
    }

    function serve($contentType = 'application/xml')
    {
        $xml = $this->out();
        header("Content-type: $contentType");
        echo $xml;
    }

    function header()
    {
        $out  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $out .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        return $out;
    }

    function footer()
    {
        return '</rss>';
    }

    function full_url()
    {
        $s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
        $protocol = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos(strtolower($_SERVER['SERVER_PROTOCOL']), '/')) . $s;
        $port = ($_SERVER['SERVER_PORT'] == '80') ? '' : (":".$_SERVER['SERVER_PORT']);
        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
    }
}

class RSSItem
{
    public $title;
    public $link;
    public $description;
    public $pubDate;
    public $guid;
    public $tags;
    public $attachment;
    public $length;
    public $type;

    function __construct()
    {
        $this->tags = array();
    }

    function setPubDate($when)
    {
        if(empty($when) || strtotime($when) == false)
            $this->pubDate = date("D, d M Y H:i:s O", $when);
        else
            $this->pubDate = date("D, d M Y H:i:s O", strtotime($when));
    }

    function getPubDate()
    {
        if(empty($this->pubDate))
            return date("D, d M Y H:i:s O");
        else
            return $this->pubDate;
    }

    function addTag($tag, $value)
    {
        $this->tags[$tag] = $value;
    }

    function out()
    {
        $bad  = array('&', '<' , '>');
        $good = array('&amp;', '&lt;', '&gt;');

        $title = str_replace($bad, $good, $this->title);
		$link  = str_replace($bad, $good, $this->link);
		// $description = str_replace($bad, $good, $this->description);
		// We enclose the description within CDATA tags. No need to escape special chars.
        $description = $this->description;

        $out  = "<item>\n";
        $out .= "<title>" . $title . "</title>\n";
        $out .= "<link>" . $link . "</link>\n";
        $out .= "<description><![CDATA[ " . $description . " ]]></description>\n";
        $out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";

        if(empty($this->guid)) $this->guid = $link;
        	$out .= "<guid>" . $this->guid . "</guid>\n";

        if($this->attachment != '')
            $out .= "<enclosure url='{$this->attachment}' length='{$this->length}' type='{$this->type}' />\n";

        foreach($this->tags as $key => $val) $out .= "<$key>$val</$key>\n";
        $out .= "</item>\n";
        return $out;
    }

    function enclosure($url, $type, $length)
    {
        $this->attachment = $url;
        $this->type       = $type;
        $this->length     = $length;
    }
}

?>