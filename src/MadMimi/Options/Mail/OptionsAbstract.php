<?php
/**
 * The Abstract Options class
 *
 * @author Aaron Saray
 */

namespace MadMimi\Options\Mail;

use MadMimi\Exception\InvalidOptionException;

/**
 * Class OptionsAbstract
 * @package MadMimi\Options
 */
abstract class OptionsAbstract
{
    /**
     * @var string this is a post method
     */
    const REQUEST_TYPE_POST = 'post';

    /**
     * @var string this is a get method
     */
    const REQUEST_TYPE_GET = 'get';

    /**
     * OptionsAbstract constructor.
     *
     * You can pass in the properties here directly - if you know what they are exactly.  It might be easier though
     * to use the setters on the actual options classes
     *
     * @param array $options
     * @throws InvalidOptionException
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
            else {
                throw new InvalidOptionException($key . ' is not defined.');
            }
        }
    }

    /**
     * @return array the populated array of elements from this set of options
     */
    public function getPopulated()
    {
        $properties = array_reduce(array_keys(get_class_vars(get_class($this))), function($holder, $currentKey) {
            if (!is_null($this->$currentKey)) $holder[$currentKey] = $this->$currentKey;
            return $holder;
        }, []);
        return $properties;
    }

    /**
     * Helper function to set the string value of the email for MadMimi API
     *
     * @param $key string the key of the email
     * @param $email string the email
     * @param $name string the potential name
     * @return $this
     */
    protected function setEmailAddress($key, $email, $name = '')
    {
        $this->$key = $name ? trim("{$name} <{$email}>") : $email;
        return $this;
    }

    /**
     * Helper function to translate boolean values into what the API uses
     *
     * @param $key string the key of the option
     * @param $bool boolean true or false for this option
     * @return $this
     */
    protected function setTranslatedBooleanValue($key, $bool)
    {
        if (!is_bool($bool)) throw new \DomainException('Parameter of ' . debug_backtrace()[1]['function'] . ' must be a boolean');

        $this->$key = $bool ? 'yes' : 'no';
        return $this;
    }

    /**
     * This creates a csv from an array and assigns it internally
     *
     * @param string $key the internal key
     * @param array $values the values to make into csv
     * @return $this
     */
    protected function setCsvValueFromArray($key, array $values)
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $values);
        rewind($handle);
        $this->$key = trim(stream_get_contents($handle));
        fclose($handle);
        return $this;
    }

    /**
     * @return string
     */
    abstract public function getEndPoint();

    /**
     * @return string
     */
    abstract public function getRequestType();
}