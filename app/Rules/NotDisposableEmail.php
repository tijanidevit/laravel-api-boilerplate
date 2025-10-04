<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotDisposableEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $blockedDomains = [
            '10minutemail.com',
            '10minutemail.net',
            '10minutemail.org',
            '20minutemail.com',
            '33mail.com',
            'airmail.cc',
            'anonbox.net',
            'guerrillamail.com',
            'guerrillamail.net',
            'guerrillamail.org',
            'mailinator.com',
            'mailinator.net',
            'mailinator.org',
            'maildrop.cc',
            'maildrop.xyz',
            'mailnesia.com',
            'mintemail.com',
            'moakt.com',
            'mytemp.email',
            'noclickemail.com',
            'noref.in',
            'nospam.today',
            'nada.email',
            'getnada.com',
            'spambog.com',
            'spam4.me',
            'spamgourmet.com',
            'temp-mail.org',
            'temp-mail.com',
            'tempmail.dev',
            'tempmail.io',
            'tempmail.net',
            'tempinbox.com',
            'temp-mail.io',
            'throwawaymail.com',
            'trashmail.com',
            'trashmail.net',
            'yopmail.com',
            'yopmail.net',
            'yopmail.fr',
            'fakeinbox.com',
            'fakemail.net',
            'instant-mail.de',
            'sharklasers.com'
        ];

        $domain = strtolower(substr(strrchr($value, "@"), 1));

        if (in_array($domain, $blockedDomains)) {
            $fail("The :attribute provider is not allowed. Please use a valid email address.");
        }
    }
}
