<?php
/**
* Generic validation class for validation from most sources
* 
* @copyright	2009-09-09
* @link		http://phpro.org
* @author	Kevin Waterson
* @version:	$ID$
(
*/

// namespace web2bb;

class validation
{
	/**
	* @var		$errors	The array of errors
	* @access	public
	*/
	public $errors = array();

	/**
	* @var	$validators	The array of validators
	* @access	public
	*/
	public $validators = array();

	/**
	*
	* Settor
	*
	* @access	public
	* @param	string	$name
	* @param	string	$value
	*
	*/
	public function __set( $name, $value )
	{
		switch( $name )
		{
			case 'source':
			if( !is_array( $value ) )
			{
				throw new Exception( 'Source must be an array' );
			}
			$this->source = $value;
			break;

			default:
			$this->name = $value;
		}
	}

	/**
	*
	* Getter
	*
	* @access	public
	* @param	string	$name
	* @return	string
	*
	*/
	public function __get( $name )
	{
		return $this->$name;
	}

	/**
	* Add a rule for a validator
	*
	* @access	public
	* @param	array	$validator [name, type, required, [min], [max] ]
	* @return	object	Instance of self to allow chaining
	*
	*/
	public function addValidator( $validator )
	{
		/*** set the validator name if it does not exist ***/
		if( !isset( $this->validators[$validator['name']] ) )
		{
			$this->validators[$validator['name']] = array();
		}
		$val = array();
		foreach( $validator as $key=>$value )
		{
			$val[$key] = $value;
		}
		$this->validators[$validator['name']][] = $val;

		return $this;
	}


	/**
	*
	* Run the validations
	*
	*/
	public function run()
	{
		// loop over the validators
		foreach( $this->validators as $key=>$val )
		{
			// each validator may contain multiple rules
			foreach( $val as $key=>$options )
			{
				// check if the field is required
				$this->checkRequired( $options );

				// run the validation
				switch( $options['type'] )
				{
					case 'string':
					$this->validateString( $options );
					break;

					case 'length':
					$this->validateStringLength( $options );
					break;

					case 'numeric':
					$this->validateNumeric( $options );
					break;

					case 'regex':
					$this->validateRegex( $options );
					break;

					case 'float':
					$this->validateFloat( $options );
					break;

					case 'date':
					$this->validatedate( $options );
					break;

					case 'url':
					$this->validateUrl( $options );
					break;

					case 'email':
					$this->validateEmail( $options );
					break;

					case 'injection':
					$this->validateEmailInjection( $options );
					break;

					case 'ipv4':
					$this->validateIpv4( $options );
					break;

					case 'ipv6':
					$this->validateIpv6( $options );
					break;

					case 'callback':
					$this->validateCallback( $options );
					break;

					default:
					throw new Exception( "Invalid Type" );
				}
			}
		}
	}

	/**
	* Check if a field is required
	*
	* @access	private
	* @param	array	bool
	*
	*/
	private function checkRequired( $values )
	{
		$message =  $this->parseCamelCase( $values['name'] ) . ' is a required field';
		$message = isset( $values['message'] ) ? $values['message'] : $message;
		if( isset( $values['required'] ) && $values['required'] === true )
		{
			if( $this->source[$values['name']] == '' )
			{
				$this->errors[] = $message;
			}
		}
	}
	

	/**
	*
	* Validate a string
	*
	* @access	private
	* @param	array	$values
	* @return	bool
	*
	*/
	private function validateString( $values )
	{
		$message = $this->parseCamelCase( $values['name'] ) . ' is Invalid';
		$message = isset( $values['message'] ) ? $values['message'] : $message;
		$name = $values['name'];
		if( !is_string( $this->source[$name] ) )
		{
			$this->errors[] = $message;
		}
	}

	/**
	*
	* Check the length of a string
	*
	* @access	private
	* @param	string	string	the string to check
	* @param	int	$length	the length
	* @param	string	$type	min or max or both
	* @return	bool
	* 
	*/
	private function validateStringLength( $values )
	{
		$message = $this->parseCamelCase( $values['name'] ). ' must be between ' . $values['min'] . ' and ' . $values['max'] . ' characters long';
		$message = isset( $values['message'] ) ? $values['message'] : $message;

		if( strlen( $this->source[$values['name']] ) > $values['max'] || strlen( $this->source[$values['name']] ) < $values['min'] )
		{
			$this->errors[] = $message;
		}
	}

	/**
	*
	* Validate by Regular Expression
	*
	* @access	private
	* @param	array	$values
	* 
	*/
	public function validateRegex( $values )
	{
		$default_message = $this->parseCamelCase( $values['name'] ) . ' does not match the required pattern';
		$message = isset( $values['message'] ) ? $values['message'] : $default_message;

		if( !preg_match( "'".$values['pattern']."'", $this->source[$values['name']] ) )
		{
			$this->errors[] = $message;
		}
	}

	/**
	*
	* Validate a number is numeric
	*
	* @access	private
	* @param	array	$options
	*/
	private function validateNumeric( $options )
	{
		$default_message = $this->parseCamelCase( $options['name'] ) . ' must be a number';
		$message = isset( $options['message'] ) ? $options['message'] : $default_message;

		if( filter_var( $this->source[$options['name']], FILTER_VALIDATE_INT ) != true )
		{
			$this->errors[] = $message;
		}
	}

	/**
	*
	* Validate an email address
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function validateEmail( $options )
	{
		$default_message = $this->parseCamelCase( $options['name'] ) . ' is not a valid email address';
		$message = isset( $options['message'] ) ? $options['message'] : $default_message;

		if(filter_var( $this->source[$options['name']], FILTER_VALIDATE_EMAIL ) === FALSE )
		{
			$this->errors[] = $message;
		}
	}

	/**
	* Check an email for email injection characters
	*
	* @access	private
	* @param	$options
	*
	*/
	private function validateEmailInjection( $options )
	{
		$default_message = $this->parseCamelCase( $options['name'] ) . ' contains injection characters';
		$message = isset( $options['message'] ) ? $options['message'] : $default_message;
		if ( preg_match( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , $this->source[$options['name']] ) )
		{
			$this->errors[] = $message;
		}
	}

	/**
	*
	* Validate a value is a floating point number
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function validateFloat( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid floating point number';
		$message = isset( $options['message'] ) ? $options['message'] : $message;
		if(filter_var( $this->source[$options['name']], FILTER_VALIDATE_FLOAT ) === false )
		{
			$this->errors[] = $message;
		}
	}

	/**
	*
	* Parse CamelCase or camel_case to Camel Case
	*
	* @access	private
	* @param	string	$string
	* @return	string
	*
	*/
	private function parseCamelCase( $string )
	{
		$cc = preg_replace('/(?<=[a-z])(?=[A-Z])/',' ',$string);
		$cc = ucwords( str_replace( '_', ' ', $cc ) );
		return $cc;
	}

	/**
	*
	* Validate a URL
	*
	* @access       private
	* @param	array	$options
	*
	*/
	private function validateUrl( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid URL';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if(filter_var( $this->source[$options['name']], FILTER_VALIDATE_URL) === FALSE )
		{
			$this->errors[] = $message;
		}
	}

	/**
	* Validate a date
	*
	* @access	private
	* @param	array	options
	*
	*/
	private function validateDate( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid date';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		switch( $options['format'] )
		{
			case 'YYYY/MM/DD':
			case 'YYYY-MM-DD':
			list( $y, $m, $d ) = preg_split( '/[-\.\/ ]/', $this->source[$options['name']] );
			break;

			case 'YYYY/DD/MM':
			case 'YYYY-DD-MM':
			list( $y, $d, $m ) = preg_split( '/[-\.\/ ]/', $this->source[$options['name']] );
			break;

			case 'DD-MM-YYYY':
			case 'DD/MM/YYYY':
			list( $d, $m, $y ) = preg_split( '/[-\.\/ ]/', $this->source[$options['name']] );
			break;

			case 'MM-DD-YYYY':
			case 'MM/DD/YYYY':
			list( $m, $d, $y ) = preg_split( '/[-\.\/ ]/', $this->source[$options['name']] );
			break;

			case 'YYYYMMDD':
			$y = substr( $this->source[$options['name']], 0, 4 );
			$m = substr( $this->source[$options['name']], 4, 2 );
			$d = substr( $this->source[$options['name']], 6, 2 );
			break;

			case 'YYYYDDMM':
			$y = substr( $this->source[$options['name']], 0, 4 );
			$d = substr( $this->source[$options['name']], 4, 2 );
			$m = substr( $this->source[$options['name']], 6, 2 );
			break;

			default:
			throw new Exception( "Invalid Date Format" );
		}
		if( checkdate( $m, $d, $y ) == false )
		{
			$this->errors[] = $message;
		}
	}

	/**
	*
	* Validate an ipv4 IP address
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function validateIpv4( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid ipv4 address';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if( filter_var( $this->source[$options['name']], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) === FALSE)
		{
			$this->errors[] = $message;
		}
	}

	/**
	*
	* Validate an ipv6 IP address
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function validateIpv6( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid ipv6 address';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if( filter_var( $this->source[$options['name']], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE )
		{
			$this->errors[] = $message;
		}
	}

	/**
	*
	* Custom or external validator
	*
	* @access	private
	* @param	array	$options
	*
	*/
	public function validateCallback( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ) . ' is invalid';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if( isset( $options['class'] ) )
		{
			$class = $options['class'];
			$func = $options['function'];
			$obj = new $class;
			// the callback function MUST return bool
			if( $obj->$func( $this->source[$options['name']] ) == true )
			{
				$this->errors[] = $message;
			}
		}
		else
		{
			$func = $options['function'];
			if( $func( $this->source[$options['name']] ) == true )
			{
				$this->errors[] = $message;
			}
		}
	}

	/**
	*
	* Define the error message
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function errorMessage( $options )
	{

	}
} // end of validation class

?>
