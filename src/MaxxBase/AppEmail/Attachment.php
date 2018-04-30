<?php
/*************************************************************************************
 * Attachment model and methods.
 * Use this class to generate an attachment to bind to an outgoing email message.
 */

namespace MaxxBase\AppEmail;
namespace MaxxBase\AppEmail\Exceptions;


class Attachment
{
    public $fileName;
    public $fileData;
    public $fileMimeType;
    public $filePath;

    /************************************************************************
     * @param $fileName - Name the file will have when attached
     * @param $fileData - Raw file data
     *
     * @return $this
     */
    public function __construction($fileName, $fileData)
    {
        $this->setFileName($fileName);
        $this->setFileData($fileData);
    }

    /************************************************************************
     * FORCE SET THE SUGGESTED NAME OF THE FILE
     *
     * @param $fileName - Name the file will have when attached
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /************************************************************************
     * FORCE SET THE BINARY DATA TO BE ATTACHED
     *
     * @param $binData - Raw file data
     *
     * @return $this
     */
    public function setFileData($binData)
    {
        $this->fileData = $binData;

        return $this;
    }

    /************************************************************************
     * FORCE SET THE CONTENT TYPE/DISPOSITION
     *
     * @param $mimeType - Force mime type of attached file
     *
     * @return $this
     */
    public function setMimeType($mimeType)
    {
        $this->fileMimeType = $mimeType;
        return $this;
    }

    /************************************************************************
     * STATICALLY GENERATE A NEW ATTACHMENT USING A FILE PATH, AND RETURN IT IMMEDIATELY
     *
     * @param $filePath - Path of file to attach
     *
     * @return Attachment
     */
    public static function generateFromPath($filePath)
    {
        //TODO LIMIT WHERE FILES CAN COME FROM -- POTENTIAL SECURITY RISK
        $fileName = basename($filePath);
        $content  = file_get_contents($filePath);

        return Attachment::generate($fileName, $content);
    }

    /************************************************************************
     * STATICALLY GENERATE A NEW ATTACHMENT AND RETURN IT IMMEDIATELY.
     *
     * @param $fileName
     * @param $fileData
     *
     * @return Attachment
     */
    public static function generate($fileName, $fileData)
    {
        $att = new Attachment();
        $att->setFileData($fileData);
        $att->setFileName($fileName);

        return $att;
    }

    /************************************************************************
     * GENERATES THE FINAL DATA CHUNK THAT SHOULD BE INSERTED INTO
     * THE MESSAGE
     *
     * @param $separator string
     *
     * @return string
     */
    public function composed($separator)
    {
        return $this->_compose($separator);
    }

    /************************************************************************
     * GENERATES THE FINAL DATA CHUNK THAT SHOULD BE INSERTED INTO
     * THE MESSAGE
     *
     * @param $separator
     *
     * @return string
     */
    private function _compose($separator)
    {
        $attachment = "--{$separator}" . Env::$eol;
        $attachment .= "Content-Type: application/octet-stream; name=\"{$this->fileName}\"" . Env::$eol;
        $attachment .= "Content-Transfer-Encoding: base64" . Env::$eol;
        $attachment .= "Content-Disposition: attachment; filename=\"{$this->fileName}\"" . Env::$eol
                       . Env::$eol;
        $attachment .= chunk_split(base64_encode($this->fileData));

        return $attachment;
    }
}