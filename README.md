# MaxxBase AppEmail
A simple object-oriented email composition library for PHP.

This library handles email messages and their composition in an object oriented manor. 
That is, the Message and it's parts like: **Email Addresses** and **Attachments** are constructed as objects.

**What This Library Is**

This library is about message composition. It job is to make putting the message together easier.
 
**What This Library Is Not**

This library is purposely without any functionality related to email queueing, delivery, relaying or tracking.  

** THIS LIBRARY IS NOT PRODUCTION READY. IT IS STILL A WORK IN PROGRESS **

##Usage and Examples

```php
$message   = new Message();
$recipient = new EmailAddress($realName, $emailAddress = test@example.com);
$sender    = new EmailAddress($realName, $emailAddress = mysite@example.com);
 
$message
    ->messageImport('Message/File/Path');
    ->setSubject('My Custom Subject');
    ->addSender($sender)
    ->addRecipient($recipient);
    
$message->send();
```

####CC and BCC Options
```php
$cc = new EmailAddress($realName = 'CC Someone', $emailAddress = cc@example.com);
$message->addCc( $cc );
```
You can do a BCC the same way, simply use $message->addBcc($bcc); 


####Attach a File to Your Email Message
Option 1: is to generate an attachment object using the Attachment class
```php
$message->addAttachment( Attachment::generateFromPath('File/Path') );
```

Option 2: Use the **attachFileData()** function within the Message class
 ```php
 $message->attachFileData($fileName, $fileData);
 ```
 
####Reply-To Address aka Return Path
```php
 $message->addReplyTo(EmailAddress::generate($realName, $emailAddress));
```

####Add A Header Entry
```php
 $message->addHeader("Header to Add"));
```

##Credits
This library was created by James R. Lincoln <james@maxxsocial.com> for the Vurbose IO Project.