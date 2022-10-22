<?php

declare(strict_types = 1);


namespace Heptacom\AdminOpenAuth\Component\Saml;

use Psr\Http\Message\RequestInterface;

final class Saml2Toolkit
{
    /**
     * @var array{request: array, get: array, post: array}|null
     */
    private static ?array $superGlobals = null;

    /**
     * SAML library relies on super globals, so we need to set them.
     *
     * @return void
     * @throws \Exception if super globals are already set
     */
    public static function prepareSuperGlobals(string $samlResponse, string $relayState): void
    {
        if (self::$superGlobals !== null) {
            throw new \Exception('Super globals are already set. You cannot declare them multiple times.');
        }

        self::$superGlobals = [
            'request' => $_REQUEST,
            'get' => $_GET,
            'post' => $_POST,
        ];

        $_GET = [];
        $_POST = [
            'SAMLResponse' => $samlResponse,
            'RelayState' => $relayState,
        ];
//        $_GET = [];
//        parse_str($request->getUri()->getQuery(), $_GET);
//
//        $_POST = [];
//        switch ($request->getHeader('content-type')[0] ?? '') {
//            case 'application/x-www-form-urlencoded':
//                parse_str((string) $request->getBody(), $_POST);
//                break;
//            case 'application/json':
//                $_POST = \json_decode((string) $request->getBody(), true);
//                break;
//        }

        $_REQUEST = \array_merge($_GET, $_POST);
    }

    /**
     * When we are done with super globals, we want to reset them to their original contents.
     *
     * @return void
     */
    public static function restoreSuperGlobals(): void
    {
        if (self::$superGlobals === null) {
            return;
        }

        $_REQUEST = self::$superGlobals['request'];
        $_GET = self::$superGlobals['get'];
        $_POST = self::$superGlobals['post'];

        self::$superGlobals = null;
    }
}
