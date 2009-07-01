<pre>
<?php

/*

$feed = array( 
	'rss'      => array(
				// 'channel' => array('item'=>'item #1')
				'channel' => array('item'=>array('item #1','item #2','item #3')),
				'test' => array('null'=>'void')
				),
	// 'rss_attr' => array(
	// 			'xmlns:dc' => 'http://purl.org/dc/elements/1.1/',
	// 			'xmlns:atom' => 'http://www.w3.org/2005/Atom',
	// 			'xmlns:media' => 'http://search.yahoo.com/mrss/',
	// 			'version' => '2.0',
	// 			),
	'hello' => array('one','t&wo','th"ree') 
);
*/




include 'example_media_array.php';
// var_dump($feed);


function is_assoc($array) {
    return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
}

function xmlentities ( $string )
{
    return htmlentities($string,ENT_QUOTES,'UTF-8');
    // return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string );
}

function array2xml($array) { 

    function ia2xml($array,$level=0,$parent_key=false) { 
        $xml=""; 
		$level++;
		
		$tabs='';
		for ($i=1; $i < $level; $i++) { $tabs .= "\t"; }
		

        foreach ($array as $key=>$value) { 
			
			// delete any char not allowed in XML element names
			$key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);
			
			if (substr($key,-5) == '_attr') {
				// var_dump('Skipping: '.$key);
				continue;
			}
			
			
			$attributes = '';
			if (array_key_exists($key.'_attr',$array)) {
				foreach ((array) $array[$key.'_attr'] as $attr => $attr_value) {
					$attributes .= " $attr=\"".xmlentities($attr_value)."\"";
				}
				// var_dump("Found: $attributes");
			}
		
			
	
		    if (is_array($value) && !empty($value) && !is_assoc($value) ) { 
                $xml.= ia2xml($value,$level-1,$key);

			} else if (empty($value)) { 
				
				
				if ($parent_key)
					$xml.="$tabs<$parent_key$attributes/>\n";
				else
					$xml.="$tabs<$key$attributes/>\n";
				
					
            } else if (is_array($value)) { 
				
				
				if ($parent_key)
					$xml.="$tabs<$parent_key$attributes>\n".ia2xml($value,$level)."$tabs</$parent_key>\n";
				else
					$xml.="$tabs<$key$attributes>\n".ia2xml($value,$level)."$tabs</$key>\n"; 

            } else { 

				if ($parent_key)
					$xml.="$tabs<$parent_key$attributes>".xmlentities($value,$level)."</$parent_key>\n"; 
				else
					$xml.="$tabs<$key$attributes>".xmlentities($value,$level)."</$key>\n"; 
	
                
            } 
        } 
        return $xml; 
    } 
	$xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n". ia2xml($array);
	return $xml;
    // return simplexml_load_string("<$tag>".$xml."</$tag>"); 
} 



// $test['type']='lunch'; 
// $test['time']='12:30'; 
// $test['menu']=array('entree'=>'salad', 'maincourse'=>'steak'); 

echo htmlentities( simplexml_load_string(array2xml($feed))->asXML() );
// echo htmlentities( array2xml($feed) );
// echo array2xml($feed,"test")->asXML();

?>

</pre>