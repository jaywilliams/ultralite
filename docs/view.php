<p>
<a href="index.php">Back To Index</a>
</p>

<p>
The view class is called in by the controller, and sets up the content to include in the layout. The layout contains a variable called "$content" which takes the view data, and renders it. This following example is taken from the index controllers, index method.
</p>

<p>
Note that template, or view files, use the extention .phtml. This is to avoid confusion with other files.
</p>

<?php
$code='
<?php
                /*** a new view instance ***/
                $tpl = new view;

                /*** turn caching on for this page ***/
                $view->setCaching(true);

                /*** set the template dir ***/
                $tpl->setTemplateDir(__APP_PATH . \'/modules/index/views\');

                /*** the include template ***/
                $tpl->include_tpl = __APP_PATH . \'/views/index/index.phtml\';

                /*** a view variable ***/
                $this->view->title = \'WEB2BB - Development Made Easy\';
                $this->view->heading = \'WEB2BB\';

                /*** the cache id is based on the file name ***/
                $cache_id = md5( \'admin/index.phtml\' );

                /*** fetch the template ***/
                $this->content = $tpl->fetch( \'index.phtml\',
?>
';
highlight_string($code);
?>
