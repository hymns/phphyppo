<?php
/**
 * phpHyppo
 *
 * An open source MVC application framework for PHP 5.1+
 *
 * @package			phpHyppo
 * @author			Muhammad Hamizi Jaminan, hymns [at] time [dot] net [dot] my
 * @copyright		Copyright (c) 2008 - 2010, Green Apple Software.
 * @license			LGPL, see included license file
 * @link			http://www.phphyppo.org
 * @since			Version 11.02
 */

/* no direct access */
if (!defined('BASEDIR'))
	exit;

/**
 * Validation
 *
 * Library for validate form field with rules
 *
 * @package         phpHyppo
 * @subpackage      Application Library
 * @author          Muhammad Hamizi Jaminan
 */

/**
 * usage:
 *
 * $this->load->library('validation', 'validation');
 *
 * $data = $this->input->post('data', true);
 * $this->validation->addSource($data);
 *
 * $rules_array = array(
 *       'name' => array(
 *                   'required'=>true, 
 *                   'type'=>'string',  
 *                   'min'=>30, 'max'=>50, 
 *                   'trim'=>true
 *               ),
 *       'age' => array(
 *                  'type'=>'numeric', 'required'=>true, 
 *                  'max'=>120, 
 *                  'trim'=>true
 *               )
 * );
 * 
 * $this->validation->addRules($rules_array);
 * $this->validation->test();
 *
 * if (sizeof($this->validation->errors) > 0)
 * {
 *      print_r($this->validation->errors); 
 * }
 *
 * type available:
 * string, numeric, email, float, ipv4, ipv6, url & bool
 *
 * strip function:
 * stripNonAlphaNumericSpaces, stripNonAlphaNumeric, stripNonNumeric, stripNonAlpha, stripExcessWhitespace
 *
 * string utils:
 * formatForUrl, formatFromUrl, getUniqueChars, randomString
 *
 */
class Validation
{
    /*
    * @errors array
    */
    public $errors = array();

    /*
    * @the validation rules array
    */
    private $rules = array();

    /*
     * @the sanitized values array
     */
    public $sanitized = array();
     
    /*
     * @the source 
     */
    private $source = array();


    /**
     *
     * @the constructor, duh!
     *
     */
    public function __construct()
    {
    }

    /**
     *
     * @add the source
     *
     * @access public
     *
     * @param array $source
     *
     */
    public function addSource($source, $trim=false)
    {
        $this->source = $source;
    }

    /**
     *
     * @add a rule to the validation rules array
     *
     * @access public
     * @param string $varname The variable name
     * @param string $type The type of variable
     * @param bool $required If the field is required
     * @param int $min The minimum length or range
     * @param int $max the maximum length or range
     *
     */
    public function addRule($varname, $type, $required=false, $min=0, $max=0, $trim=false)
    {
        $this->rules[$varname] = array('type' => $type, 'required' => $required, 'min' => $min, 'max' => $max, 'trim' => $trim);
        
        return $this;
    }

    /**
     *
     * @add multiple rules to teh validation rules array
     *
     * @access public
     * @param array $rules_array The array of rules to add
     *
     */
    public function addRules(array $rules_array)
    {
        $this->rules = array_merge($this->rules, $rules_array);
    }

    /**
     *
     * @test the validation rules
     *
     * @access public
     *
     */
    public function test()
    {
        // reset error
        $this->errors = array();

        // set vars
        foreach( new ArrayIterator($this->rules) as $var => $opt )
        {
            if ($opt['required'] == true)
            {
                $this->is_set($var);
            }

            // trim white space
            if ( array_key_exists('trim', $opt) && $opt['trim'] == true )
            {
                $this->source[$var] = trim( $this->source[$var] );
            }

            switch($opt['type'])
            {
                case 'email':
                    $this->validateEmail($var, $opt['required']);
                    if ( ! array_key_exists($var, $this->errors) )
                    {
                        $this->sanitizeEmail($var);
                    }
                break;

                case 'url':
                    $this->validateUrl($var);
                    if ( ! array_key_exists($var, $this->errors) )
                    {
                        $this->sanitizeUrl($var);
                    }
                break;

                case 'number':
                    $this->validateNumber($var, $opt['min'], $opt['max'], $opt['required']);
                    if ( ! array_key_exists($var, $this->errors) )
                    {
                        $this->stripNonNumeric($var);
                    }
                break;

                case 'numeric':
                    $this->validateNumeric($var, $opt['min'], $opt['max'], $opt['required']);
                    if ( ! array_key_exists($var, $this->errors) )
                    {
                        $this->sanitizeNumeric($var);
                    }
                break;

                case 'string':
                    $this->validateString($var, $opt['min'], $opt['max'], $opt['required']);
                    if ( ! array_key_exists($var, $this->errors) )
                    {
                        $this->sanitizeString($var);
                    }
                break;

                case 'float':
                    $this->validateFloat($var, $opt['required']);
                    if ( ! array_key_exists($var, $this->errors) )
                    {
                        $this->sanitizeFloat($var);
                    }
                break;

                case 'ipv4':
                    $this->validateIpv4($var, $opt['required']);
                    if ( ! array_key_exists($var, $this->errors) )
                    {
                        $this->sanitizeIpv4($var);
                    }
                break;

                case 'ipv6':
                    $this->validateIpv6($var, $opt['required']);
                    if ( ! array_key_exists($var, $this->errors) )
                    {
                        $this->sanitizeIpv6($var);
                    }
                break;

                case 'bool':
                    $this->validateBool($var, $opt['required']);
                    if ( ! array_key_exists($var, $this->errors) )
                    {
                        $this->sanitized[$var] = (bool) $this->source[$var];
                    }
                break;
            }
        }

        // reset rules, source        
        $this->rules = array();
        $this->source = array();
    }

    /**
     *
     * @Check if POST variable is set
     *
     * @access private
     * @param string $var The POST variable to check
     *
     */
    private function is_set($var)
    {
        if ( ! isset($this->source[$var]) )
        {
            $this->errors[$var] = $var . ' is not set';
        }
    }

    /**
     *
     * @validate an ipv4 IP address
     *
     * @access private
     * @param string $var The variable name
     * @param bool $required
     *
     */
    private function validateIpv4($var, $required = false)
    {
        if ( $required == false && strlen($this->source[$var]) == 0 )
        {
            return true;
        }
        
        if ( filter_var($this->source[$var], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE )
        {
            $this->errors[$var] = $var . ' is not a valid IPv4';
        }
    }

    /**
     *
     * @validate an ipv6 IP address
     *
     * @access private
     * @param string $var The variable name
     * @param bool $required
     *
     */
    private function validateIpv6($var, $required=false)
    {
        if ( $required == false && strlen($this->source[$var]) == 0 )
        {
            return true;
        }

        if ( filter_var($this->source[$var], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE )
        {
            $this->errors[$var] = $var . ' is not a valid IPv6';
        }
    }

    /**
     *
     * @validate a floating point number
     *
     * @access private
     * @param $var The variable name
     * @param bool $required
     */
    private function validateFloat($var, $required=false)
    {
        if ( $required == false && strlen($this->source[$var]) == 0 )
        {
            return true;
        }
        if ( filter_var($this->source[$var], FILTER_VALIDATE_FLOAT) === false )
        {
            $this->errors[$var] = $var . ' is an invalid float';
        }
    }

    /**
     *
     * @validate a string
     *
     * @access private
     * @param string $var The variable name
     * @param int $min the minimum string length
     * @param int $max The maximum string length
     * @param bool $required
     *
     */
    private function validateString($var, $min=0, $max=0, $required=false)
    {
        if ( $required == false && strlen($this->source[$var]) == 0 )
        {
            return true;
        }

        if ( isset($this->source[$var]) )
        {
            if ( strlen($this->source[$var]) < $min )
            {
                $this->errors[$var] = $var . ' is too short';
            }
            elseif ( strlen($this->source[$var]) > $max )
            {
                $this->errors[$var] = $var . ' is too long';
            }
            elseif ( ! is_string($this->source[$var]) )
            {
                $this->errors[$var] = $var . ' is invalid';
            }
        }
    }

    /**
     *
     * @validate an number
     *
     * @access private
     * @param string $var the variable name
     * @param int $min The minimum number range
     * @param int $max The maximum number range
     * @param bool $required
     *
     */
    private function validateNumber($var, $min=0, $max=0, $required=false)
    {
        if ( $required == false && strlen($this->source[$var]) == 0 )
        {
            return true;
        }

        if ( $this->source[$var] == 0)
        {
            return true;
        }

        if ( filter_var($this->source[$var], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[0-9]+$/'))) == FALSE )
        {
            $this->errors[$var] = $var . ' is an invalid number';
        }
    }

    /**
     *
     * @validate an numeric
     *
     * @access private
     * @param string $var the variable name
     * @param int $min The minimum number range
     * @param int $max The maximum number range
     * @param bool $required
     *
     */
    private function validateNumeric($var, $min=0, $max=0, $required=false)
    {
        if ( $required == false && strlen($this->source[$var]) == 0 )
        {
            return true;
        }

        if ( filter_var($this->source[$var], FILTER_VALIDATE_INT, array('options' => array('min_range' => $min, 'max_range' => $max))) === FALSE )
        {
            $this->errors[$var] = $var . ' is an invalid number';
        }
    }

    /**
     *
     * @validate a url
     *
     * @access private
     * @param string $var The variable name
     * @param bool $required
     *
     */
    private function validateUrl($var, $required=false)
    {
        if ( $required == false && strlen($this->source[$var]) == 0 )
        {
            return true;
        }
        if ( filter_var($this->source[$var], FILTER_VALIDATE_URL) === FALSE )
        {
            $this->errors[$var] = $var . ' is not a valid URL';
        }
    }


    /**
     *
     * @validate an email address
     *
     * @access private
     * @param string $var The variable name 
     * @param bool $required
     *
     */
    private function validateEmail($var, $required=false)
    {
        if ( $required == false && strlen($this->source[$var]) == 0 )
        {
            return true;
        }

        if ( filter_var($this->source[$var], FILTER_VALIDATE_EMAIL) === FALSE )
        {
            $this->errors[$var] = $var . ' is not a valid email address';
        }
    }


    /**
     * @validate a boolean 
     *
     * @access private
     * @param string $var the variable name
     * @param bool $required
     *
     */
    private function validateBool($var, $required=false)
    {
        if ( $required == false && strlen($this->source[$var]) == 0 )
        {
            return true;
        }

        filter_var($this->source[$var], FILTER_VALIDATE_BOOLEAN);
        {
            $this->errors[$var] = $var . ' is Invalid';
        }
    }
    

    /**
     *
     * @santize and email
     *
     * @access private
     * @param string $var The variable name
     * @return string
     *
     */
    private function sanitizeEmail($var)
    {
        $email = preg_replace( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , '', $this->source[$var] );
        $this->sanitized[$var] = (string) filter_var($email, FILTER_SANITIZE_EMAIL);
    }


    /**
     *
     * @sanitize a ipv4
     *
     * @access private
     * @param string $var The variable name
     *
     */
    private function sanitizeIpv4($var)
    {
        $this->sanitized[$var] = (string) filter_var($this->source[$var],  FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     *
     * @sanitize a url
     *
     * @access private
     * @param string $var The variable name
     *
     */
    private function sanitizeUrl($var)
    {
        $this->sanitized[$var] = (string) filter_var($this->source[$var],  FILTER_SANITIZE_URL);
    }

    /**
     *
     * @sanitize a number
     *
     * @access private
     * @param string $var The variable name
     *
     */
    private function sanitizeNumber($var)
    {
        $this->sanitized[$var] = (int) $this->stripNonNumeric($this->source[$var]);
    }

    /**
     *
     * @sanitize a numeric value
     *
     * @access private
     * @param string $var The variable name
     *
     */
    private function sanitizeNumeric($var)
    {
        $this->sanitized[$var] = (int) filter_var($this->source[$var], FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     *
     * @sanitize a float value
     *
     * @access private
     * @param string $var The variable name
     *
     */
    private function sanitizeFloat($var)
    {
        $this->sanitized[$var] = (int) filter_var($this->source[$var], FILTER_SANITIZE_NUMBER_FLOAT);
    }

    /**
     *
     * @sanitize a string
     *
     * @access private
     * @param string $var The variable name
     *
     */
    private function sanitizeString($var)
    {
        $this->sanitized[$var] = (string) filter_var($this->source[$var], FILTER_SANITIZE_STRING);
    }

    /**
     * Remove all characters except letters, numbers, and spaces.
     *
     * @param string $string
     * @return string
     */
    public function stripNonAlphaNumericSpaces( $string ) 
    {
        return preg_replace( "/[^a-z0-9 ]/i", "", $string );
    }

    /**
     * Remove all characters except letters and numbers.
     *
     * @param string $string
     * @return string
     */
    public function stripNonAlphaNumeric( $string ) 
    {
        return preg_replace( "/[^a-z0-9]/i", "", $string );
    }    

    /**
     * Remove all characters except numbers.
     *
     * @param string $string
     * @return string
     */
    public function stripNonNumeric( $string ) 
    {
        return preg_replace( "/[^0-9]/", "", $string );
    }

    /**
     * Remove all characters except letters.
     *
     * @param string $string
     * @return string
     */
    public function stripNonAlpha( $string ) 
    {
        return preg_replace( "/[^a-z]/i", "", $string );
    }

    /**
     * Transform two or more spaces into just one space.
     *
     * @param string $string
     * @return string
     */
    public function stripExcessWhitespace( $string ) 
    {
        return preg_replace( '/  +/', ' ', $string );
    }

    /**
     * Format a string so it can be used for a URL slug
     *
     * @param string $string
     * @return string
     */
    public function formatForUrl( $string ) 
    {                 
        $string = stripNonAlphNumericSpaces( trim( strtolower( $string ) ) );
        return str_replace( " ", "-", stripExcessWhitespace( $string ) );            
    }

    /**
     * Format a slug into human readable string
     *
     * @param string $string
     * @return string
     */
    public function formatFromUrl( $string ) 
    {
        return str_replace( "-", " ", trim( strtolower( $string ) ) );
    }

    /**
     * Get an array of unique characters used in a string. This should also work with multibyte characters.
     *
     * @param string $string
     * @return mixed
     */
    public function getUniqueChars( $string, $returnAsArray=true ) 
    {
        $unique = array_unique( preg_split( '/(?<!^)(?!$)/u', $string ) );
        if ( empty( $returnAsArray ) ) 
        {
            $unique = implode( "", $unique );
        }
        return $unique;
    }

    /**
     * Get an array of slit word from cammel case.
     *
     * @param string $string
     * @return mixed
     */
    public function splitCamelCase($string)
    {
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', $string, $matches);

        return $matches;
    }

    /**
     * Generate a random string of specified length from a set of specified characters
     *
     * @param integer $size Default size is 30 characters.
     * @param string $chars The characters to use for randomization.
     */
    public function randomString( $size=30, $chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789" ) 
    {
        $string = "";
        $length = strlen( $chars );
     
        for( $i=0; $i < $size; $i++ ) 
        {
            $string .= $chars{ rand( 0, $length ) };
        }
     
        return $string;     
    }

} 

/* End of validation.php */
/* Location: /core/libraries/validation.php */