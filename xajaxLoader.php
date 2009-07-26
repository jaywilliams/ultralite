<?php
class test
{
    function xajax_request_func()
    {
        
        /*
            do something here
            maybe call $this->do_something_intern()
        */
    
        
        return $objResponse;
 
    }
    
    /* internal function (on php5 private or protected */
    
    function do_something_intern()
    {
        /*your code here*/
    }
}
 
?>
 
<?
 
$testclass = new test();
 
$xajax = new xajax();
 
$xajax->registerFunction(array( 'xajax_request_func', $testclass , 'xajax_request_func' ) );
 
/* or do it on this way: */
 
$methods = get_class_methods( get_class( $testclass ) );
 
foreach ( $methods as $method )
{
    if ( preg_match( "/^xajax_request_/", $method) )
    {
        $xajax->registerFunction(array($method, $testclass, $method));
    }
}    
 
/* this means, that all class_methods of test, beginning with xajax_request will be registered as an javascriptfuntion named like the phpfunction*/
 
?>
