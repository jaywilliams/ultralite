<?php

$options = array(
					'scan'           =>Zend_Translate::LOCALE_FILENAME,
					// 'disableNotices' => true,
					// 'clear' => true,
				);
$translate = new Zend_Translate('array', APPPATH.'languages/', null, $options );
 
// $translate->setLocale('en_US');
// $translate->setLocale('nl');

var_dump($translate->getList());
var_dump($translate->getLocale());

echo $translate->_('ADMIN');

