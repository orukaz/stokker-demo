<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;

final class ProductFavoriteOwner
{
    public const VISITOR_COOKIE = 'stokker_favorite_visitor_id';

    public const VISITOR_COOKIE_MINUTES = 60 * 24 * 365;

    /**
     * @return array{user_id?: int, visitor_id?: string}
     */
    public static function attributes(Request $request, bool $createVisitor = false): array
    {
        if ($user = $request->user()) {
            return ['user_id' => $user->id];
        }

        $visitorId = $request->cookie(self::VISITOR_COOKIE);

        if (! $visitorId && $createVisitor) {
            $visitorId = (string) Str::uuid();
        }

        return $visitorId ? ['visitor_id' => $visitorId] : [];
    }

    public static function visitorCookie(string $visitorId): Cookie
    {
        return cookie(
            self::VISITOR_COOKIE,
            $visitorId,
            self::VISITOR_COOKIE_MINUTES,
            httpOnly: true,
            sameSite: 'lax',
        );
    }
}
