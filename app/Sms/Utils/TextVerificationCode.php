<?php

declare(strict_types=1);

namespace App\Sms\Utils;

use Carbon\Carbon;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\PhoneNumber;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Sms\Messages\TextVerificationCode as TextVerificationCodeMessage;

class TextVerificationCode
{
    /**
     * The Phone number validation code cache key template.
     */
    const CACHE_TEMPLATE = 'Phone Number(%s) Validation Code';

    /**
     * Cache expires second.
     */
    const EXPIRES_SECOND = 300;

    /**
     * Hir cache expires second.
     */
    const HIT_EPIRES_SECOND = 60;

    /**
     * Get the cache key.
     * @param string $phone
     * @return string
     */
    public static function getKey(string $phone): string
    {
        return sprintf(static::CACHE_TEMPLATE, $phone);
    }

    /**
     * Get the cache expires date.
     * @return \Carbon\Carbon
     */
    public static function getExpiresAt(): Carbon
    {
        return (new Carbon)->addSeconds(static::EXPIRES_SECOND);
    }

    public static function getHitExpiresAt(): Carbon
    {
        return (new Carbon)->addSeconds(static::HIT_EPIRES_SECOND);
    }

    /**
     * Make a code and put to cache.
     * @param string $phone
     * @return $int
     */
    public static function make(string $phone): int
    {
        // Write in Debug logs.
        Log::debug('Make Phone Validation Code.', [
            'phoneNumber' => $phone,
            'code' => $code = mt_rand(100000, 999999),
        ]);

        // Put to cache.
        Cache::put(static::getKey($phone), $code, static::getExpiresAt());
        Cache::put(static::getKey($phone).':has', true, static::getHitExpiresAt());

        return $code;
    }

    /**
     * Validate phone number validation code.
     * @param string $phone
     * @param int $code,
     * @return bool
     */
    public static function validate(string $phone, int $code): bool
    {
        // Write in debug logs.
        Log::debug('Cached Verify Phone Validation Code.', [
            'key' => $cacheKey = static::getKey($phone),
            'phoneNumber' => $phone,
            'inputCode' => $code,
            'cachedCode' => $cachedCode = (int) Cache::get($cacheKey),
        ]);

        return $cachedCode && $code && $cachedCode === $code;
    }

    /**
     * Has a phone.
     * @param string $phone
     * @return bool
     */
    public static function has(string $phone): bool
    {
        return (bool) Cache::has(static::getKey($phone).':has');
    }

    public static function send(string $TTC, string $phone): void
    {
        $phoneNumber = new PhoneNumber($phone, $TTC);
        if (static::has((string) $phoneNumber)) {
            throw new AccessDeniedHttpException('你的发送频率过快');
        }
        try {
            App::make(EasySms::class)->send($phoneNumber, new TextVerificationCodeMessage(
                static::make((string) $phoneNumber)
            ));
        } catch (NoGatewayAvailableException $e) {
            throw $e->getLastException();
        }
    }
}
