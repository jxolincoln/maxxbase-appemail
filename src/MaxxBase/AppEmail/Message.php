<?php
/************************************************************************
 * Message Model
 */
namespace MaxxBase\AppEmail;
use MaxxBase\AppEmail\Exceptions;

class Message
{
    public $MessageKey = null;
    public $CampaignKey = null;
    public $EmailListKey = null;
    public $From = null;
    public $Headers = null;
    public $Bcc = null;
    public $Subject = null;
    public $Cc = null;
    public $Message = null;
    public $MessagePlainText = null;
    public $SendBefore = null;
    public $SendAfter = null;
    public $MetaData = null;

    private $_separator = null;

    private $_header_entries = [];
    private $_attachments = [];
    private $_reply_to = [];
    private $_recipients = [];
    private $_bcc = [];
    private $_cc = [];
    private $_senders = [];

    /************************************************************************
     * Message constructor.
     */
    public function __construct()
    {
        // AUTO GENERATE SEPARATOR
        $this->MessageKey = $this->_generateMessageKey();
        $this->_separator = $this->_generateSeparator();
    }

    /************************************************************************
     * @return array
     */
    public function attributeNames()
    {
        return [
            'MessageKey',
            'CampaignKey',
            'EmailListKey',
            'From',
            'Headers',
            'Bcc',
            'Subject',
            'Cc',
            'Message',
            'MessagePlainText',
            'SendBefore',
            'SendAfter',
            'MetaData'
        ];
    }

    /************************************************************************
     * SET THE RICH TEXT MESSAGE BODY
     * @param $message
     *
     * @return $this
     */
    public function messageSet($message)
    {
        $this->Message = $message;

        return $this;
    }

    /************************************************************************
     * IMPORT THE RICH TEXT MESSAGE BODY
     * @param $messagePath
     *
     * @return $this
     */
    public function messageImport($messagePath)
    {
        //TODO LIMIT WHERE FILES CAN COME FROM -- POTENTIAL SECURITY RISK
        if (is_file($messagePath)) {
            $this->Message = file_get_contents($messagePath);
        }

        return $this;
    }

    /************************************************************************
     * APPEND SOME TEXT TO THE RICH MESSAGE BODY
     * @param $message
     *
     * @return $this
     */
    public function messageAppend($message)
    {
        $this->Message .= $message;

        return $this;
    }

    /************************************************************************
     * SET PLAIN TEXT BODY FOR THE MESSAGE
     * @param $message
     *
     * @return $this
     */
    public function messagePlainText($message)
    {
        $this->MessagePlainText = $message;

        return $this;
    }

    /************************************************************************
     * IMPORT THE PLAIN TEXT TO USE AS THE MESSAGE BODY
     * @param $messagePath
     *
     * @return $this
     */
    public function messagePlainTextImport($messagePath)
    {
        //TODO LIMIT WHERE FILES CAN COME FROM -- POTENTIAL SECURITY RISK
        if (is_file($messagePath)) {
            $this->MessagePlainText = file_get_contents($messagePath);
        }

        return $this;
    }

    /************************************************************************
     * ADD A HEADER ENTRY TO THE MESSAGE
     * @param null $headerData
     *
     * @return $this
     */
    public function addHeader($headerData = null)
    {
        array_push($this->_header_entries, $headerData);

        return $this;
    }

    /************************************************************************
     * ADD A SENDER EMAIL OBJECT TO THE MESSAGE
     * @param EmailAddress $sender
     *
     * @return $this
     */
    public function addSender(EmailAddress $sender)
    {
        array_push($this->_senders, $sender);

        return $this;
    }

    /************************************************************************
     * ADD A RECIPIENT EMAIL OBJECT TO THE MESSAGE
     * @param EmailAddress $recipient
     *
     * @return $this
     */
    public function addRecipient(EmailAddress $recipient)
    {
        array_push($this->_recipients, $recipient);

        return $this;
    }

    /************************************************************************
     * ADD A CC RECIPIENT
     * @param EmailAddress $recipient
     *
     * @return $this
     */
    public function addCc(EmailAddress $recipient)
    {
        array_push($this->_cc, $recipient);

        return $this;
    }

    /************************************************************************
     * ADD A BCC RECIPIENT
     * @param EmailAddress $recipient
     *
     * @return $this
     */
    public function addBcc(EmailAddress $recipient)
    {
        array_push($this->_bcc, $recipient);

        return $this;
    }

    /************************************************************************
     * ADD A REPLY TO EMAIL OBJECT TO THE MESSAGE
     * @param EmailAddress $recipient
     *
     * @return $this
     */
    public function addReplyTo(EmailAddress $recipient)
    {
        array_push($this->_reply_to, $recipient);

        return $this;
    }

    /************************************************************************
     * SET THE EMAIL MESSAGE SUBJECT
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->Subject = $subject;

        return $this;
    }

    /************************************************************************
     * ADD AN ATTACHMENT TO THE MESSAGE
     * @param Attachment $attachment
     *
     * @return $this
     * @throws AppEmailException
     */
    public function addAttachment(Attachment $attachment)
    {
        try {
            array_push($this->_attachments, $attachment);
        } catch (\Exception $e) {
            throw new AppEmailException("Could not add Attachment", 500, $e);
        }

        return $this;
    }

    /************************************************************************
     * ATTACH A FILE USING THE LOCAL PATH
     *
     * @param $filePath
     *
     * @return $this
     * @throws AppEmailException
     */
    public function attachFile($filePath)
    {
        try {
            array_push($this->_attachments, Attachment::generateFromPath($filePath));
        } catch (\Exception $e) {
            throw new AppEmailException("Could not attach file using path {$filePath} " , 500, $e);
        }

        return $this;
    }

    /************************************************************************
     * ATTACH A DATA AS A FILE ATTACHMENT
     * @param $fileName
     * @param $fileData
     *
     * @return $this
     * @throws AppEmailException
     */
    public function attachFileData($fileName, $fileData)
    {
        try {
            array_push($this->_attachments, Attachment::generate($fileName, $fileData));
        } catch (\Exception $e) {
            throw new AppEmailException("Could not attach file data for file: {$fileName}" , 500, $e);
        }

        return $this;
    }


    /************************************************************************
     * @return bool
     * @throws AppEmailException
     */
    public function send()
    {
        $to      = $this->composedRecipient();
        $headers = $this->composedHeader();
        $message = $this->composedMessage();
        try {
            return mail($to, $this->Subject, $message, $headers);
        } catch (\Exception $e) {
            throw new AppEmailException("Could not compile and send message." , 500, $e);
        }
    }

    /************************************************************************
     * GET THE COMPOSED RECIPIENT LINE
     * @return array
     */
    public function composedRecipient()
    {
        return $this->_composeRecipient();
    }

    /************************************************************************
     * GET THE COMPOSED HEADER
     * @return string
     */
    public function composedHeader()
    {
        return $this->_composeHeader();
    }

    /************************************************************************
     * GET THE COMPOSED MESSAGE
     * @return string
     */
    public function composedMessage()
    {
        return $this->_composeMessage();
    }

    /************************************************************************
     * COMPOSE THE MESSAGE HEADER USING ALL OF THE SUBMITTED PARTS
     * @return string
     */
    private function _composeHeader()
    {
        $messageHeader = [];
        if (!empty($this->_senders)) {
            $messageHeader[] = 'From: ' . implode("; ",
                                                  array_map(function (EmailAddress $email) {
                                                      return $email->composed();
                                                  }, $this->_senders));
        }
        if (!empty($this->_reply_to)) {
            $messageHeader[] = 'Reply-To: ' . implode("; ",
                                                      array_map(function (EmailAddress $email) {
                                                          return $email->composed();
                                                      }, $this->_reply_to));
        } else {
            $messageHeader[] = 'Reply-To: ' . implode("; ",
                                                      array_map(function (EmailAddress $email) {
                                                          return $email->composed();
                                                      }, $this->_senders));
        }

        if (!empty($this->_cc)) {
            $messageHeader[] = 'Cc: ' . implode("; ",
                                                array_map(function (EmailAddress $email) {
                                                    return $email->composed();
                                                }, $this->_cc));
        }
        if (!empty($this->_bcc)) {
            $messageHeader[] = 'Bcc: ' . implode("; ",
                                                 array_map(function (EmailAddress $email) {
                                                     return $email->composed();
                                                 }, $this->_bcc));
        }
        $messageHeader[] = 'MIME-Version: 1.0';

        if (!empty($this->_header_entries)) {
            $messageHeader[] = implode(Env::$eol, $this->_header_entries);
        }

        $messageHeader[] = 'Content-Type: multipart/mixed; boundary="' . $this->_separator . '"'
                           . str_repeat(Env::$eol, 2);

        return implode(Env::$eol, $messageHeader);
    }

    /************************************************************************
     * COMPOSE THE RECIPIENT LINE BASED ON THE ADDED EMAIL ADDRESSES
     * @return array
     */
    public function _composeRecipient()
    {
        $messageRecipients = '';
        if (!empty($this->_recipients)) {
            $messageRecipients = 'To: ' . implode("; ",
                                                  array_map(function (EmailAddress $email) {
                                                      return $email->composed();
                                                  }, $this->_recipients));
        }

        return $messageRecipients;
    }

    /************************************************************************
     * COMPOSE THE MESSAGE
     * @return string
     */
    private function _composeMessage()
    {
        $messageStack = [];

        if (!empty($this->MessagePlainText)) {
            $messageStack[] = "--{$this->_separator}";
            $messageStack[] = 'Content-Type: text/plain; charset=iso-8859-1';
            $messageStack[] = 'Content-Transfer-Encoding: 7bit' . Env::$eol;
            $messageStack[] = $this->MessagePlainText . Env::$eol;
        }

        if (!empty($this->Message)) {
            $messageStack[] = "--{$this->_separator}";
            $messageStack[] = 'Content-Type: text/html; charset=iso-8859-1';
            $messageStack[] = 'Content-Transfer-Encoding: 8bit' . Env::$eol;
            $messageStack[] = $this->Message . Env::$eol;
        }

        if (!empty($this->_attachments)) {
            $messageStack[] = implode("",
                                      array_map(function (Attachment $attachment, $a) {
                                          return $attachment->composed($a);
                                      }, $this->_attachments, [0 => $this->_separator]));

            $messageStack[] .= "--" . $this->_separator . "--";
        }

        return implode(Env::$eol, $messageStack);
    }

    /************************************************************************
     * GENERATE A MESSAGE KEY THAT IS UNIQUE.
     * SEEMS REDUNDANT AS THE SEPARATOR IS GENERATED THE SAME WAY.
     * BUT THIS MAY CHANGE.
     *
     * @return string
     */
    private function _generateMessageKey()
    {
        return md5(rand(0, microtime()));
    }

    /************************************************************************
     * GENERATE THE MIME/PARTS SEPARATOR USED IN EMAIL COMPOSITION
     * @return string
     */
    private function _generateSeparator()
    {
        return md5(rand(0, microtime()));
    }
}