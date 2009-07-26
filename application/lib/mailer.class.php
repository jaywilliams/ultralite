<?php

/***
 *
 * @multiple protocols smtp, smtps, smptls, smtpssl
 *
 * @TODO Embedded image support.
 *
 * @TODO Support for 8bit, base64, binary, and quoted-printable encoding
 *
 * @TODO Add file size limits to attachments
 *
 * @TODO Add mail_message max size
 *
 * @TODO Add mail_message min size
 *
 * @TODO Add sanitizing of header vars
 *
 * @TODO Add sleep option between sending
 *
 * @TODO Add encryption
 *
 * @TODO Word wrap for messages
 *
 */

// namespace web2bb;

class mailer{

	/*
	 * The stmp stream
	 */
	private $stream;

	/*
	 * @the smtp server
	 */
	private $smtp_server;

	/*
	 * @the smtp port
	 */
	private $smtp_port = 25;

	/*
	 * @the email message
	 */
	private $mail_message;

	/*
	 * @end of line
	 */
	private $eol = "\r\n";

	/*
	 * @the mailer type System or Remote
	 */
	private $mail_type;

	/*
	 * @mail from email address
	 */
	private $mail_from_email;

	/*
	 * @mail from name
	 */
	private $mail_from_name;

	/*
	 * @mail_to_email
	 */
	private $mail_to_email;

	/*
	 * @private subject
	 */
	private $mail_subject;

	/*
	 * @the headers array
	 */
	private $headers = array();


	/**
	 *
	 * @set the email message;
	 *
	 * @access public
	 *
	 * @param string $message
	 *
	 */	
	public function setMessage($message)
	{
		$this->mail_message = $message;
	}

	/**
	 *
	 * @add a header
	 *
	 * @access public
	 *
	 * @param string $name The name of the header
	 *
	 * @param string $value the Value of the header
	 *
	 */
	public function addHeader($name, $value)
	{
		$this->headers[] = array('name'=>$name, 'value'=>$value);
	}

	/**
	 *
	 * @get the headers
	 *
	 * @access private
	 *
	 * @return string
	 *
	 */
	private function getHeaders()
	{
		$headers = '';
		foreach( $this->headers as $h)
		{
			if( $h['$value'] != '' )
			{
				$headers .= $h['name'].': '.$h['value'].$this->eol;
			}
		}
		return $headers;
	}
			

	/**
	 *
	 * Send mail using mail() command
	 *
	 */
	private function send_system()
	{
		$to = $this->mail_to_name.' <'.$this->mail_to_email.'>';

		$headers = $this->getHeaders();
		mail($to, $this->mail_subject, $this->mail_message, $headers);
	}


	

	/**
	 *
	 * @set the email from email address
	 *
	 * @access public
	 *
	 * @param string $mail_from_email
	 *
	 */
	public function setEmailFromEmail($mail_from_email)
	{
		$this->mail_from_email = $mail_from_email;
	}

	/**
	 *
	 * @set the email from name
	 *
	 * @access public
	 *
	 * @param string $mail_from_name
	 *
	 */
	public function setEmailFromName($mail_from_name)
	{
		$this->mail_from_name = $mail_from_name;
	}

	/**
	 *
	 * @set the email to email address
	 *
	 * @access public
	 *
	 * @param string $mail_to_email
	 *
	 */
	public function setEmailToEmail($mail_to_email)
	{
		$this->mail_to_email = $mail_to_email;
	}

	/**
	 *
	 * @set the email to name
	 *
	 * @access public
	 *
	 * @param string $mail_to_email
	 *
	 */
	public function setEmailToName($mail_to_name)
	{
		$this->mail_to_name = $mail_to_name;
	}

	/**
	 *
	 * @set the email subject
	 *
	 * @access public
	 *
	 * @param string $mail_subject
	 *
	 */
	public function setEmailSubject($mail_subject)
	{
		$this->mail_subject = $mail_subject;
	}


	/**
	 *
	 * @set the mail type to use
	 *
	 * @access public
	 *
	 * @param string $mail_type
	 *
	 */
	public function setMailer($mail_type)
	{
		$this->mail_type = $mail_type;
	}


	/**
	 *
	 * @choose which system to use to mail the message
	 *
	 */
	public function send()
	{
		if($this->mail_type == 'system')
		{
			$this->send_system();
		}
		else
		{
			$this->sendRemote();
		}
	}


	/**
	 *
	 * @set smtp port
	 *
	 * @access public
	 *
	 * @param int port number
	 *
	 */
	public function setSmptPort($smtp_port)
	{
		$this->smtp_port = $smtp_port;
	}

	/**
	 *
	 * @set smtp server
	 *
	 * @access public
	 *
	 * @param string $smtp_server
	 *
	 */
	public function setSmtpServer($smtp_server)
	{
		$this->smtp_server = $smtp_server;
	}

	/**
	 *
	 * @send a message by remote mail server
	 *
	 */
	public function sendRemote()
	{
		/*** get config values for mail ***/
		$config = config::getInstance();

		/*** headers ***/
		$headers = '';

		$this->stream = fsockopen ($config->config_values['mail']['smtp_server'], $config->config_values['mail']['smtp_port'], $errno, $errstr, 1); 
		$res=fgets($this->stream,256); 
		if(substr($res,0,3) != "220")
		{
			echo $res.'stream fail<br />';
		}

		// Introduce ourselves 
		fputs($this->stream, 'HELO '.$config->config_values['mail']['smtp_server'].$this->eol); 
		$res=fgets($this->stream,256); 
		if(substr($res,0,3) != "250")
		{
			echo $res.'helo fail<br />';
		}

		// Envelope from 
		fputs($this->stream, 'MAIL FROM: '.$this->mail_from_email.$this->eol);
		$res=fgets($this->stream,256); 
		if(substr($res,0,3) != "250")
		{
			echo $res.'mail from fail<br />';
		}

		// Envelope to 
		fputs($this->stream, 'RCPT TO: '.$this->mail_to_email.$this->eol); 
		$res=fgets($this->stream,256); 
		if(substr($res,0,3) != "250")
		{
			echo $res.'to fail<br />';
		}

		// The message 
		fputs($this->stream, "DATA".$this->eol); 
		$res=fgets($this->stream,256); 
		if(substr($res,0,3) != "354")
		{
			echo $res.'data fail';
		}

		// Send To:, From:, Subject:, other headers, blank line, message, and finish 
		// with a period on its own line. 
		fputs($this->stream, 'To: '.$this->mail_to_name.' <'.$this->mail_to_email.'>'.$this->eol.'From: '.$this->mail_from_name.' <'.$this->mail_from_email.'>'.$this->eol.'Subject: '.$this->mail_subject.$this->eol.$headers.$this->eol.$this->eol.$this->mail_message.$this->eol.'.'.$this->eol); 
		$res=fgets($this->stream,256); 
		if(substr($res,0,3) != "250")
		{
			echo $res.'send fail<br />';
		}

		// Say bye bye 
		fputs($this->stream,'QUIT'.$this->eol); 
		$res=fgets($this->stream,256); 
		if(substr($res,0,3) != "221")
		{
			echo $res.'quit fail<br />';
		}
	}

} /*** end of class ***/

?>
