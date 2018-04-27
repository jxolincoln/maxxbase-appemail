<?php
/*************************************************************************************
 * Email Address Model
 *
 */


namespace AppEmail;

class EmailAddress
{
    public $RealName     = null;
    public $EmailAddress = null;

    /************************************************************************
     * EmailAddress constructor.
     * @param null $realName
     * @param $emailAddress
     * @param bool $validateFormat
     */
    public function __construct($realName = null, $emailAddress, $validateFormat = false)
    {
        $this->RealName     = trim($realName);
        $this->EmailAddress = trim($emailAddress);
    }

    /************************************************************************
     * RETURN THE COMPOSED EMAIL STRING
     * @return string
     */
    public function composed()
    {
        if (!empty($this->RealName)) {
            return "{$this->RealName} <{$this->EmailAddress}>";
        }
        else {
            return "{$this->EmailAddress}";
        }
    }

    /************************************************************************
     * GENERATE AN EMAIL OBJECT AND RETURN IT
     * @param null $realName
     * @param $emailAddress
     * @param bool $validateFormat
     * @return EmailAddress
     */
    public static function generate($realName = null, $emailAddress, $validateFormat = false)
    {
        return new EmailAddress($realName, $emailAddress, $validateFormat);
    }
}