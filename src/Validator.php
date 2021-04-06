<?php
namespace KKandmore\Validation;

class Validator
{
    protected $defaultRule = [];

    protected $customRule = [];

    protected $rule = [];

    protected $defaultMessages = [];

    protected $customMessages = [];

    protected $messages = [];

    protected $alias = [];

    protected $errors = [];

    protected $data = [];

    protected $processedData = [];

    public function __construct(array $rule = [], array $messages = [])
    {
        $this->defaultRule = [
            'required' => true,
            'type' => 'string',
            'regex' => '',
        ];
        $this->defaultMessages = [
            'required' => 'The :attribute is required',
            'type' => 'The :attribute must be of the type :type,:currentType given',
            'regex' => 'The :attribute format is invalid.',
        ];
        if ($rule) {
            $this->customRule = $rule;
        }
        if ($messages) {
            $this->customMessages = $messages;
        }
    }

    public function validate(array $data, array $rules, array $messages = [], array $alias = [])
    {
        $this->data = $data;
        $this->alias = $alias;
        foreach ($rules as $attribute => $rule) {
            $this->rule = array_merge($this->defaultRule, $this->customRule, $rule);
            $this->messages = array_merge($this->defaultMessages, $this->customMessages, $messages[$attribute] ?? []);
            $this->validateAttribute($attribute);
        }

        if (!count($this->errors)) {
            $this->processData($data, $rules);
        }

        return count($this->errors) === 0;
    }

    protected function validateAttribute($attribute)
    {
        $value = $this->getValue($attribute);

        foreach ($this->rule as $ruleName => $ruleValue) {
            $method = 'validate'.ucfirst($ruleName);
            if (!$this->$method($ruleName, $ruleValue, $value)) {
                $this->addError($attribute, $value, $ruleName, $ruleValue);
            }
        }
    }

    protected function getValue($attribute)
    {
        return array_key_exists($attribute, $this->data) ? $this->data[$attribute] : null;
    }

    protected function addError($attribute, $value, $ruleName, $ruleValue)
    {
        $message = $this->messages[$ruleName];

        $message = str_replace(':attribute', $this->alias[$attribute] ?? $attribute, $message);
        $message = str_replace(':type', $ruleValue, $message);
        $message = str_replace(':currentType', gettype($value), $message);
        $message = str_replace(':regex', $ruleValue, $message);

        $this->errors[$attribute][] = $message;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function getProcessedData()
    {
        return $this->processedData;
    }

    protected function validateRequired($ruleName, $ruleValue, $value)
    {
        return !(is_null($value) && $ruleValue);
    }

    protected function validateType($ruleName, $ruleValue, $value)
    {
        return gettype($value) === $ruleValue;
    }

    protected function validateRegex($ruleName, $ruleValue, $value)
    {
        return preg_match($ruleValue, $value);
    }

    protected function processData(array $data, array $rules)
    {
        foreach ($rules as $attribute => $rule) {
            $this->processedData[$attribute] = $data[$attribute];
        }
    }
}