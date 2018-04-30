<?php
/*************************************************************************************
 * Email Address Model
 *
 */

namespace MaxxBase\AppEmail;
use MaxxBase\AppEmail\Exceptions;

class EmailAddress
{
    public $RealName = null;
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

        if ($validateFormat == true) {
            $this->validate($emailAddress);
        }
    }

    /************************************************************************
     * RETURN THE COMPOSED EMAIL STRING
     * @return string
     */
    public function composed()
    {
        if (!empty($this->RealName)) {
            return "{$this->RealName} <{$this->EmailAddress}>";
        } else {
            return "{$this->EmailAddress}";
        }
    }

    /************************************************************************
     * VALIDATE THE EMAIL ADDRESS
     * @param $emailAddress
     * @param \Closure|null $validationFunction
     * @return $this
     * @throws Exceptions\InvalidEmailAddressException
     */
    public function validate($emailAddress, \Closure $validationFunction = null)
    {
        $isValid = true;
        $atIndex = strrpos($emailAddress, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain    = substr($emailAddress, $atIndex + 1);
            $local     = substr($emailAddress, 0, $atIndex);
            $localLen  = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if
            (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                         str_replace("\\\\", "", $local))
            ) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/',
                                str_replace("\\\\", "", $local))
                ) {
                    $isValid = false;
                }
            }
        }
        if ($isValid == false) {
            throw new Exceptions\InvalidEmailAddressException("Invalid email address {$emailAddress}", 500);
        }

        return $this;

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