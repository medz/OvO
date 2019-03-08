<?php

declare(strict_types=1);

namespace App;

use Carbon\Carbon;
use Overtrue\EasySms\PhoneNumber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerificationCodeNotification;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class VerificationCode
{
    /**
     * The phone number.
     */
    protected $phoneNumber;

    /**
     * Create a util.
     * @param string|Overtrue\EasySms\PhoneNumber $itc
     * @param null|string $phone
     */
    public function __construct($itc, ?string $phone = null)
    {
        if (! ($itc instanceof PhoneNumber)) {
            $itc = new PhoneNumber($phone, $itc);
        }

        $this->phoneNumber = $itc;
    }

    protected function develop(string $code)
    {
        $request = request();
        if ($request->has('develop') && $request->hash === config('app.key')) {
            throw new AccessDeniedHttpException($code);
        }
    }

    /**
     * Send a notification.
     */
    public function notification(): void
    {
        $code = (string) mt_rand(100000, 999999);
        $key = static::key($this->phoneNumber, false);
        $hitKey = static::key($this->phoneNumber, true);

        // Put to cache.
        Cache::put($key, $code, (Carbon::now()->addSeconds(
            config('sms.text-verifcation-code.expires')
        )));

        $this->develop($code);

        Cache::put($hitKey, $code, (Carbon::now()->addSeconds(
            config('sms.text-verifcation-code.hit_expires')
        )));

        Notification::route('sms', $this->phoneNumber)->notify(new VerificationCodeNotification($code));
    }

    /**
     * has Hit or code.
     * @param null|bool $isHit
     * @param null|string $code
     * @return bool
     */
    public function has(?bool $isHit = false, ?string $code = null): bool
    {
        if ($isHit) {
            return Cache::has(static::key($this->phoneNumber, true)) ? true : false;
        }

        $key = static::key($this->phoneNumber, false);
        if (Cache::get($key) === $code) {
            return true;
        }

        return false;
    }

    /**
     * Remove a cache key.
     */
    public function remove()
    {
        $key = static::key($this->phoneNumber, false);
        $hitKey = static::key($this->phoneNumber, true);

        Cache::forget($key);
        Cache::forget($hitKey);
    }

    /**
     * Get a instance.
     */
    public static function instance(...$payload)
    {
        return new static(...$payload);
    }

    /**
     * Send a notification.
     */
    public static function send(string $itc, string $phone): void
    {
        $instance = static::instance($itc, $phone);
        if ($instance->has(true)) {
            // throw new AccessDeniedHttpException('发送频率过快，请稍后再试哦！');
        }

        $instance->notification();
    }

    /**
     * The cache key.
     */
    public static function key(PhoneNumber $phoneNulber, bool $isHit = false): string
    {
        $key = sprintf(config('sms.text-verifcation-code.cache-key'), $phoneNulber->getUniversalNumber());

        if ($isHit) {
            return $key.':hit';
        }

        return $key;
    }
}
