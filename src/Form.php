<?php

namespace aryelgois\Gump;

/**
 * Form validator, wrapping Gump
 *
 * Usage: You MUST extend this class, defining the constant properties for a
 *        specif form in your application, and SHOULD define __construct(),
 *        which MAY call it's parent with the correct data e.g. $_GET or $_POST.
 *
 * @author Aryel Mota Góis
 * @license MIT
 * @link https://www.github.com/aryelgois/GUMP
 */
abstract class Form
{
    /**
     * Language used by getErrorsReadable()
     *
     * @const string
     */
    const GUMP_LANG = 'pt-br';

    /**
     * Filters applied before the validation
     *
     * @const string[]
     */
    const GUMP_FILTERS_PRE = [];

    /**
     * Filters applied after the validation
     *
     * @const string[]
     */
    const GUMP_FILTERS_POST = [];

    /**
     * Validation rules that data must meet
     *
     * @const string[]
     */
    const GUMP_RULES = [];

    /**
     * Data to be validated
     *
     * @var mixed[]
     */
    protected $data;

    /**
     * Gump validator
     *
     * @var Gump
     */
    protected $validator;

    /**
     * Data after validation
     *
     * @var mixed[]
     */
    protected $validated;

    /**
     * Creates a new Form object
     *
     * @param mixed[] $data     Data to be validated
     * @param boolean $sanitize If data should be sanitized
     */
    public function __construct($data, $sanitize = true)
    {
        $this->validator = new Gump(static::GUMP_LANG);

        if ($sanitize) {
            $data = $this->validator->sanitize($data);
        }

        $this->data = $data;
    }

    /**
     * Validates and returns success or failure
     *
     * @return boolean If there were any errors
     */
    public function isValid()
    {
        $this->validate();
        return empty($this->validator->errors());
    }

    /**
     * Validates and returns result
     *
     * NOTES:
     * - Check if isValid() before using it
     *
     * @return mixed[] Same as data, might be sanitized/filtered
     */
    public function getValidated()
    {
        $this->validate();
        return $this->validated;
    }

    /**
     * Validates and returns errors
     *
     * @return array[] List of errors, each containing the field, value
     *                 (filtered), rule and param
     */
    public function getErrors()
    {
        $this->validate();
        return $this->validator->errors();
    }

    /**
     * Passes the arguments to validator's get_readable_errors()
     *
     * @see Gump::get_readable_errors()
     *
     * @param boolean $convert_to_string
     * @param string  $field_class
     * @param string  $error_class
     *
     * @return array
     * @return string
     */
    public function getErrorsReadable(
        $convert_to_string = false,
        $field_class = 'gump-field',
        $error_class = 'gump-error-message'
    ) {
        return $this->validator->get_readable_errors(func_get_args());
    }

    /**
     * Validates the data once and cache the result
     */
    protected function validate()
    {
        if ($this->validated === null) {
            $this->validated = $this->validator->filter(
                $this->data,
                static::GUMP_FILTERS_PRE
            );

            $this->validator->validate(
                $this->validated,
                static::GUMP_RULES
            );

            if (!empty(static::GUMP_FILTERS_POST)) {
                $this->validated = $this->validator->filter(
                    $this->validated,
                    static::GUMP_FILTERS_POST
                );
            }
        }
    }
}
