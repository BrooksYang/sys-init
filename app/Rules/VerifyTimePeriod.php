<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VerifyTimePeriod implements Rule
{
    private $start;

    /**
     * Create a new rule instance.
     *
     * @param $start
     * @return void
     */
    public function __construct($start)
    {
        $this->start = $start;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (strtotime($value) < strtotime($this->start)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '结束时间应大于开始时间.';
    }
}
