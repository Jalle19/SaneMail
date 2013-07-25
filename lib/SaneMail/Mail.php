<?php

/**
 * Mail class file
 * @author Sam Stenvall <sam@supportersplace.com>
 * @copyright Copyright &copy; Sam Stenvall 2013-
 * @license http://opensource.org/licenses/MIT The MIT License
 */

namespace SaneMail;

use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime as MimeType;
use Zend\Mail\Transport\Sendmail as SendMailTransport;
use Zend\Mail\Exception\RuntimeException as ZendRuntimeException;

/**
 * Wrapper for Zend Framework 2's mail functionality
 *
 * @author Sam Stenvall <sam@supportersplace.com>
 */
class Mail
{

	/**
	 * @var Message the message
	 */
	private $_message;

	/**
	 * @var string the message encoding
	 */
	private $_encoding;

	/**
	 * @var MimePart the plain-text version of the message
	 */
	private $_mimeText;

	/**
	 * @var MimePart the HTML version of the message
	 */
	private $_mimeHtml;

	/**
	 * Class constructor
	 * @param string $encoding the encoding to use for the e-mail. Defaults to 
	 * UTF-8.
	 */
	public function __construct($encoding = 'UTF-8')
	{
		$this->_message = new Message();
		$this->_encoding = $encoding;
	}

	/**
	 * Adds a recipient
	 * @param string $address
	 * @param string $name
	 */
	public function addTo($address, $name = null)
	{
		$this->_message->addTo($address, $name);
	}

	/**
	 * Adds a sender
	 * @param string $address
	 * @param string $name
	 */
	public function addFrom($address, $name = null)
	{
		$this->_message->addFrom($address, $name);
	}

	/**
	 * Adds a recipient as CC
	 * @param string $address
	 * @param string $name
	 */
	public function addCc($address, $name = null)
	{
		$this->_message->addCc($address, $name);
	}

	/**
	 * Adds a recipient as BCC
	 * @param string $address
	 * @param string $name
	 */
	public function addBcc($address, $name = null)
	{
		$this->_message->addBcc($address, $name);
	}

	/**
	 * Adds a reply-to address
	 * @param string $address
	 * @param string $name
	 */
	public function addReplyTo($address, $name = null)
	{
		$this->_message->addReplyTo($address, $name);
	}

	/**
	 * Sets the e-mail subject
	 * @param string $subject
	 */
	public function setSubject($subject)
	{
		$this->_message->setSubject($subject);
	}

	/**
	 * Sets the plain-text body of the e-mail
	 * @param string $text
	 */
	public function setBodyText($text)
	{
		$this->_mimeText = new MimePart($text);
		$this->_mimeText->type = MimeType::TYPE_TEXT;
		$this->_mimeText->charset = $this->_encoding;
	}

	/**
	 * Sets the HTML part of the e-mail
	 * @param string $html
	 */
	public function setBodyHtml($html)
	{
		$this->_mimeHtml = new MimePart($html);
		$this->_mimeHtml->type = MimeType::TYPE_HTML;
		$this->_mimeHtml->charset = $this->_encoding;
	}

	/**
	 * Sends the e-mail
	 * @throws Exception if the e-mail could not be sent
	 */
	public function send()
	{
		$body = new MimeMessage();
		$parts = array();

		// Dont' add a MIME part to the e-mail if it has not been set
		foreach (array($this->_mimeText, $this->_mimeHtml) as $part)
			if ($part !== null)
				$parts[] = $part;

		// Ensure there's some kind of body
		if (empty($parts))
			throw new Exception('You must specify a body');

		$body->setParts($parts);
		$this->_message->setBody($body);

		// Set encoding now, it can't be done sooner
		$this->_message->setEncoding($this->_encoding);

		// Properly set the Content-Type header if both plain-text and HTML is 
		// used
		if (count($parts) === 2)
		{
			$this->_message->getHeaders()->get('Content-Type')
					->setType('multipart/alternative');
		}

		try
		{
			$transport = new SendMailTransport();
			$transport->send($this->_message);
		}
		catch (ZendRuntimeException $e)
		{
			// Re-throw under our own namespace
			throw new Exception($e->getMessage(), $e->getCode(), $e);
		}
	}

}
