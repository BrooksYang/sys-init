<?php

namespace App\Rules;
use App\Traits\Helper;
use Illuminate\Contracts\Validation\Rule;

class VerifyIpFormat implements Rule
{
    use Helper;

    /**
     * @var
     */
    private $ip;

    /**
     * Create a new rule instance.
     *
     * @param $ip
     * @return void
     */
    public function __construct($ip)
    {
        $this->ip = $ip;
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
        if (is_null($value) || $value === '') {
            return true;
        }
        $ips = $this->idFormat($value);
        foreach ($ips as $ip) {
            if (!$this->isIp($ip)) {
                return false;
            }
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
        return 'IP地址格式不正确';
    }
}
