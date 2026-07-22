<?php

namespace App\Actions;

use App\Models\Message;
use Illuminate\Support\Facades\RateLimiter;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class StoreContactMessage
{
    public const int MAX_ATTEMPTS = 5;

    public const int DECAY_SECONDS = 60;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes, ?string $ipAddress): Message
    {
        $ipAddress = filled($ipAddress)
            ? substr(trim((string) $ipAddress), 0, 45)
            : null;

        $key = self::rateLimitKey($ipAddress);

        $message = RateLimiter::attempt(
            $key,
            self::MAX_ATTEMPTS,
            function () use ($attributes, $ipAddress): Message {
                $message = new Message([
                    'name' => $this->stringAttribute($attributes, 'name'),
                    'email' => $this->stringAttribute($attributes, 'email'),
                    'subject' => $this->stringAttribute($attributes, 'subject'),
                    'body' => $this->stringAttribute($attributes, 'body'),
                ]);

                $message->ip_address = $ipAddress;
                $message->save();

                return $message;
            },
            self::DECAY_SECONDS,
        );

        if (! ($message instanceof Message)) {
            $retryAfter = max(1, RateLimiter::availableIn($key));

            throw new TooManyRequestsHttpException(
                $retryAfter,
                'Has enviado varios mensajes en poco tiempo. Espera un minuto antes de intentarlo de nuevo.',
            );
        }

        return $message;
    }

    public static function rateLimitKey(?string $ipAddress): string
    {
        $identity = filled($ipAddress) ? trim((string) $ipAddress) : 'unknown';

        return 'portfolio-contact:'.hash('sha256', $identity);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function stringAttribute(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;

        if (! is_string($value)) {
            throw new InvalidArgumentException("The contact attribute [{$key}] must be a string.");
        }

        return trim($value);
    }
}
