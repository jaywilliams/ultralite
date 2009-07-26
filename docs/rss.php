<p>
<a href="index.php">Back To Index</a>
</p>

<p>
Creating RSS feeds for your site is a chore with the RSS class. You simply need to specify the array of elements and add them to the class, and the job is done. In this example, and array is used, but the data could come form a feed, a database or any source you like.
</p>

<p>
Here the rss value is echo'ed but on a live server, this would be written to a file to provide caching
</p>

<?php
$code='
<?php
/*** the first article item ***/
$item1 = array(
        \'title\'=>\'Zend Framework Example Site\',
        \'link\'=>\'http://www.phpro.org/articles/Zend-Framework-Example-Site.html\',
        \'description\'=>\'This example site hopes to introduce the newcomers to Zend Framework in a friendly way, by providing a simple modular site layout and can have the newcomer up and running in minutes.\',
        \'pubDate\'=>date(DATE_RSS),
        \'image\'=>array(\'link\'=>\'http://phpro.org\', \'url\'=>\'http://phpro.org/images/spork.jpg\', \'title\'=>\'SPORK\'),
        \'language\'=>\'en\');

/*** second article item ***/
$item2 = array(
        \'title\'=>\'Xajax-In An Object Oriented Environment\',
        \'link\'=>\'http://www.phpro.org/tutorials/Xajax-In-An-Object-Oriented-Environment.html\',
        \'description\'=>\'This tutorial takes the next step in development and shows, by way of example, how xajax can be utilized in an Object Oriented environment\',
        \'pubDate\'=>date(DATE_RSS),
        \'language\'=>\'en\');

/*** third article item ***/
$item3 = array(
        \'title\'=>\'Introduction to SPL DirectoryIterator\',
        \'link\'=>\'http://www.phpro.org/tutorials/Introduction-to-SPL-DirectoryIterator.html\',
        \'description\'=>\'The DirectoryIterator is one of the more oft used of the Iterator classes and this tutorial helps to expose the user to developing in a standardised and Object Oriented approach\',
        \'pubDate\'=>date(DATE_RSS),
        \'language\'=>\'en\');

/*** a new RSS instance, pass values to the constructor ***/
$rss = new rss(\'PHPRO.ORG\', \'http://phpro.org\', \'PHP Articles Tutorials Examples Classes\');


/*** add the items from above ***/
$rss    ->addItem($item1)
        ->addItem($item2)
        ->addItem($item3);

/*** show the RSS Feed ***/
echo $rss;

?>
';
highlight_string($code);
?>

<p>
Thats it. Thats all you need to do to provide a simple and effecting multi language implementation.
</p>
