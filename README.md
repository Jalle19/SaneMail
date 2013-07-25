SaneMail
========

Zend Mail wrapper for those who want to preserve their sanity without having to resort to using ZF1. It provides an easy solution for sending e-mail without having to write 50 lines of code, and in contrast to doing it according to the ZF2 documentation, this way actually works.

## Features

* Does the proper magic to make sure Zend\Mail actually uses the encoding you specify
* Always sets a plain-text version before the HTML version so that your e-mails, you know, actually display correctly
* Properly sets the correct Content-Type for e-mails containing both a plain-text and an HTML version so that, as sane people expect, the HTML version is displayed unless the user requests the plain-text version

## Installation

Install via Composer

## Usage

```php
$mail = new SaneMail\Mail();
$mail->addFrom('from@example.com', 'Disgruntled programmer');
$mail->addTo('to@example.com');
$mail->addCc('cc@example.com');
$mail->addBcc('bcc@example.com');
$mail->addReplyTo('replyto@example.com');
$mail->setSubject('This is a working e-mail');

$html = '<html><head><title></title></head><body><h1>This is HTML</h1><p>This is a paragraph</p></body></html>';
$text = "This is plain-text\n\nThis is a paragraph";

$mail->setBodyText($text);
$mail->setBodyHtml($html);

try {
	$mail->send();
}
catch(SaneMail\Exception $e) {
	// Zend\Mail\Exception\RuntimeException available from $e->getPrevious()
}
```

## License
This library is licensed under the MIT license
