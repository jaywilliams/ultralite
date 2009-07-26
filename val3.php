function checkPassword( $password )
{
	return strlen( $password ) != 8;
}

class myClass
{
	public function checkUsername( $username )
	{
		$names = array( 'freddy', 'billy', 'holly' );
		return in_array( $username, $names );
	}
}


/*** an array to mimic POST ***/
$POST = array(
		'username'	=>	'billy',
		'password'	=>	'0fubar123',
		'email_address'	=>	"mary@little.lamb",
		'email_subject'	=>	"My Subject\nBCC: wolf@little.lamb",
		'dob'		=>	'2007-04-21',
		'url'		=>	'http://phpro.org',
		'age'		=>	31,
		'ipv4_addy'	=>	'192.168.1.1',
		'ipv6_addy'	=>	'2001:0db8:3c4d:0015:0:0:abcd:ef12',
		'cost'		=>	21.34
		);


/*** a new validation instance ***/
$val = new validation;

/*** set the source ***/
$val->source = $POST;

$message = "This is a custom error message for username";
// a message if the username is already in use
$u_msg = 'Username is already in use, please choose another';
$val	->addValidator( array( 'name'=>'username', 'type'=>'string', 'required'=>true, 'message'=>$message ) )
	->addValidator( array( 'name'=>'username', 'type'=>'length', 'min'=>5, 'max'=>8 ) )
	->addValidator( array( 'name'=>'username', 'type'=>'regex', 'pattern'=>'^foo') )
	->addValidator( array( 'name'=>'email_address', 'type'=>'email', 'required'=>true ) )
	->addValidator( array( 'name'=>'email_subject', 'type'=>'injection', 'required'=>true ) )
	->addValidator( array( 'name'=>'age', 'type'=>'numeric', 'required'=>true, 'min'=>0, 'max'=>150 ) )
	->addValidator( array( 'name'=>'url', 'type'=>'url', 'required'=>false) )
	->addValidator( array( 'name'=>'url', 'type'=>'length', 'min'=>0, 'max'=>150 ) )
	->addValidator( array( 'name'=>'cost', 'type'=>'float' ) )
	->addValidator( array( 'name'=>'ipv4_addy', 'type'=>'ipv4' ) )
	->addValidator( array( 'name'=>'ipv6_addy', 'type'=>'ipv6' ) )
	->addValidator( array( 'name'=>'username', 'type'=>'callback', 'class'=>'myClass', 'function'=>'checkUsername', 'message'=>$u_msg ) )
	->addValidator( array( 'name'=>'password', 'type'=>'callback', 'function'=>'checkPassword') )
	->addValidator( array( 'name'=>'dob', 'type'=>'date', 'required'=>true, 'format'=>'YYYY-MM-DD' ) );

$val->run();

if( sizeof( $val->errors ) > 0 )
{
	print_r( $val->errors );
}
else
{
	echo "done\n";
}
