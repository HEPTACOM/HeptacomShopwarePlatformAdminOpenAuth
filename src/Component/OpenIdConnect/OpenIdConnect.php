<?php

declare(strict_types = 1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

class OpenIdConnect
{

    private array $config;

    private ?string $accessToken = null;

    private ?string $refreshToken = null;

    private array $userInfo = [];

    /**
     * @var mixed holds well-known openid server properties
     */
    private $wellKnown = false;

    /**
     * @var mixed holds well-known opendid configuration parameters, like policy for MS Azure AD B2C User Flow
     * @see https://docs.microsoft.com/en-us/azure/active-directory-b2c/user-flow-overview
     */
    private array $wellKnownConfigParameters = [];

    /**
     * @var int timeout (seconds)
     */
    protected $timeOut = 60;

    /**
     * @var string
     */
    private $redirectURL;

    /**
     * @var int defines which URL-encoding http_build_query() uses
     */
    protected $encType = PHP_QUERY_RFC1738;

    /**
     * @var array holds PKCE supported algorithms
     */
    private $pkceAlgs = ['S256' => 'sha256', 'plain' => false];

    /**
     * @param array<'provider_url'|'authorization_endpoint'|'token_endpoint'|'userinfo_endpoint'|'client_id'|'client_secret'|'scopes', mixed> $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * @throws OpenIdConnectException
     */
    public function getAuthorizationEndpoint(): string {
        return $this->getConfig('authorization_endpoint');
    }

    /**
     * @return bool
     * @throws OpenIdConnectException
     * @link https://github.com/jumbojett/OpenID-Connect-PHP
     */
    public function authenticate(array $requestParams) {

        // Do a preemptive check to see if the provider has thrown an error from a previous redirect
        if (isset($requestParams['error'])) {
            $desc = isset($requestParams['error_description']) ? ' Description: ' . $requestParams['error_description'] : '';
            throw new OpenIdConnectException('Error: ' . $requestParams['error'] .$desc);
        }

        // If we have an authorization code then proceed to request a token
        if (isset($requestParams['code'])) {

            $code = $requestParams['code'];
            $token_json = $this->requestTokens($code);

            // Throw an error if the server returns one
            if (isset($token_json->error)) {
                if (isset($token_json->error_description)) {
                    throw new OpenIdConnectException($token_json->error_description);
                }
                throw new OpenIdConnectException('Got response: ' . $token_json->error);
            }

            // Do an OpenID Connect session check
            if ($requestParams['state'] !== $this->getState()) {
                throw new OpenIdConnectException('Unable to determine state');
            }

            // Cleanup state
            $this->unsetState();

            if (!property_exists($token_json, 'id_token')) {
                throw new OpenIdConnectException('User did not authorize openid scope.');
            }

            $claims = $this->decodeJWT($token_json->id_token, 1);

            // Verify the signature
            if ($this->canVerifySignatures()) {
                if (!$this->getConfig('jwks_uri')) {
                    throw new OpenIdConnectException ('Unable to verify signature due to no jwks_uri being defined');
                }
                if (!$this->verifyJWTsignature($token_json->id_token)) {
                    throw new OpenIdConnectException ('Unable to verify signature');
                }
            } else {
                user_error('Warning: JWT signature verification unavailable.');
            }

            // Save the id token
            $this->idToken = $token_json->id_token;

            // Save the access token
            $this->accessToken = $token_json->access_token;

            // If this is a valid claim
            if ($this->verifyJWTclaims($claims, $token_json->access_token)) {

                // Clean up the session a little
                $this->unsetNonce();

                // Save the full response
                $this->tokenResponse = $token_json;

                // Save the verified claims
                $this->verifiedClaims = $claims;

                // Save the refresh token, if we got one
                if (isset($token_json->refresh_token)) {
                    $this->refreshToken = $token_json->refresh_token;
                }

                // Success!
                return true;

            }

            throw new OpenIdConnectException ('Unable to verify JWT claims');
        }

        return false;
    }

    /**
     * Get's anything that we need configuration wise including endpoints, and other values
     *
     * @return mixed
     *
     * @throws OpenIdConnectException
     */
    protected function getConfig(string $param, ?string $default = null) {

        // If the configuration value is not available, attempt to fetch it from a well known config endpoint
        // This is also known as auto "discovery"
        if (!isset($this->config[$param])) {
            $this->config[$param] = $this->getWellKnownConfigValue($param, $default);
        }

        return $this->config[$param];
    }

    /**
     * Get's anything that we need configuration wise including endpoints, and other values
     *
     * @throws OpenIdConnectException
     */
    private function getWellKnownConfigValue(string $param, $default = null) {

        // If the configuration value is not available, attempt to fetch it from a well known config endpoint
        // This is also known as auto "discovery"
        if(!$this->wellKnown) {
            $well_known_config_url = rtrim($this->getProviderURL(), '/') . '/.well-known/openid-configuration';
            if (count($this->wellKnownConfigParameters) > 0){
                $well_known_config_url .= '?' .  http_build_query($this->wellKnownConfigParameters) ;
            }
            $this->wellKnown = json_decode($this->fetchURL($well_known_config_url));
        }

        $value = false;
        if(isset($this->wellKnown->{$param})){
            $value = $this->wellKnown->{$param};
        }

        if ($value) {
            return $value;
        }

        if (isset($default)) {
            // Uses default value if provided
            return $default;
        }

        throw new OpenIdConnectException(
            "The provider {$param} could not be fetched. Make sure your provider has a well known configuration available.");
    }


    public function setRedirectURL (string $url) {
        $this->redirectURL = $url;
    }

    /**
     * Used for arbitrary value generation for nonces and state
     *
     * @return string
     * @throws OpenIdConnectException
     */
    protected function generateRandString() {
        // Error and Exception need to be catched in this order, see https://github.com/paragonie/random_compat/blob/master/README.md
        // random_compat polyfill library should be removed if support for PHP versions < 7 is dropped
        try {
            return \bin2hex(\random_bytes(16));
        } catch (Error $e) {
            throw new OpenIdConnectException('Random token generation failed.');
        } catch (Exception $e) {
            throw new OpenIdConnectException('Random token generation failed.');
        };
    }

    /**
     * Start Here
     * @return void
     * @throws OpenIdConnectException
     */
    public function getAuthorizationUrl($redirectUri): string {
        $auth_endpoint = $this->getConfig('authorization_endpoint');
        $response_type = 'code';

        // Generate and store a nonce in the session
        // The nonce is an arbitrary value
        $nonce = $this->setNonce($this->generateRandString());

        // State essentially acts as a session key for OIDC
        $state = $this->setState($this->generateRandString());

        $auth_params = array_merge($this->authParams, [
            'response_type' => $response_type,
            'redirect_uri' => $redirectUri,
            'client_id' => $this->config['client_id'],
            'nonce' => $nonce,
            'state' => $state,
            'scope' => 'openid'
        ]);

        // If the client has been registered with additional scopes
        if (count($this->getConfig('scopes')) > 0) {
            $auth_params = array_merge($auth_params, ['scope' => implode(' ', array_merge($this->getConfig('scopes'), ['openid']))]);
        }

        // If the client has been registered with additional response types
        if (count($this->responseTypes) > 0) {
            $auth_params = array_merge($auth_params, ['response_type' => implode(' ', $this->responseTypes)]);
        }

        // If the client supports Proof Key for Code Exchange (PKCE)
        $ccm = $this->getCodeChallengeMethod();
        if (!empty($ccm) && in_array($this->getCodeChallengeMethod(), $this->getConfig('code_challenge_methods_supported'))) {
            $codeVerifier = bin2hex(random_bytes(64));
            $this->setCodeVerifier($codeVerifier);
            if (!empty($this->pkceAlgs[$this->getCodeChallengeMethod()])) {
                $codeChallenge = rtrim(strtr(base64_encode(hash($this->pkceAlgs[$this->getCodeChallengeMethod()], $codeVerifier, true)), '+/', '-_'), '=');
            } else {
                $codeChallenge = $codeVerifier;
            }
            $auth_params = array_merge($auth_params, [
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => $this->getCodeChallengeMethod()
            ]);
        }

        $auth_endpoint .= (strpos($auth_endpoint, '?') === false ? '?' : '&') . http_build_query($auth_params, '', '&', $this->encType);

        return $auth_endpoint;
    }

    /**
     * Requests a client credentials token
     *
     * @throws OpenIdConnectException
     */
    public function requestClientCredentialsToken() {
        $token_endpoint = $this->getConfig('token_endpoint');

        $headers = [];

        $grant_type = 'client_credentials';

        $post_data = [
            'grant_type'    => $grant_type,
            'client_id'     => $this->getConfig('client_id'),
            'client_secret' => $this->getConfig('client_secret'),
            'scope'         => implode(' ', $this->getConfig('scopes'))
        ];

        // Convert token params to string format
        $post_params = http_build_query($post_data, '', '&', $this->encType);

        return json_decode($this->fetchURL($token_endpoint, $post_params, $headers));
    }

    /**
     * Requests ID and Access tokens
     *
     * @param string $code
     * @param string[] $headers Extra HTTP headers to pass to the token endpoint
     * @return mixed
     * @throws OpenIdConnectException
     */
    protected function requestTokens($code, $redirectUri, $headers = array()) {
        $token_endpoint = $this->getConfig('token_endpoint');
        $token_endpoint_auth_methods_supported = $this->getConfig('token_endpoint_auth_methods_supported', ['client_secret_basic']);

        $grant_type = 'authorization_code';

        $token_params = [
            'grant_type' => $grant_type,
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'client_id' => $this->getConfig('client_id'),
            'client_secret' => $this->getConfig('client_secret')
        ];

        $authorizationHeader = null;
        # Consider Basic authentication if provider config is set this way
        if (in_array('client_secret_basic', $token_endpoint_auth_methods_supported, true)) {
            $authorizationHeader = 'Authorization: Basic ' . base64_encode(urlencode($this->getConfig('client_id')) . ':' . urlencode($this->getConfig('client_secret')));
            unset($token_params['client_secret']);
            unset($token_params['client_id']);
        }

        $ccm = $this->getCodeChallengeMethod();
        $cv = $this->getCodeVerifier();
        if (!empty($ccm) && !empty($cv)) {
            $cs = $this->getClientSecret();
            if (empty($cs)) {
                $authorizationHeader = null;
                unset($token_params['client_secret']);
            }
            $token_params = array_merge($token_params, [
                'client_id' => $this->getConfig('client_id'),
                'code_verifier' => $this->getCodeVerifier()
            ]);
        }

        // Convert token params to string format
        $token_params = http_build_query($token_params, '', '&', $this->encType);

        if (null !== $authorizationHeader) {
            $headers[] = $authorizationHeader;
        }

        $this->tokenResponse = json_decode($this->fetchURL($token_endpoint, $token_params, $headers));

        return $this->tokenResponse;
    }

    /**
     * Request RFC8693 Token Exchange
     * https://datatracker.ietf.org/doc/html/rfc8693
     *
     * @param string $subjectToken
     * @param string $subjectTokenType
     * @param string $audience
     * @return mixed
     * @throws OpenIdConnectException
     */
    public function requestTokenExchange($subjectToken, $subjectTokenType, $audience = '') {
        $token_endpoint = $this->getConfig('token_endpoint');
        $token_endpoint_auth_methods_supported = $this->getConfig('token_endpoint_auth_methods_supported', ['client_secret_basic']);
        $headers = [];
        $grant_type = 'urn:ietf:params:oauth:grant-type:token-exchange';

        $post_data = array(
            'grant_type'    => $grant_type,
            'subject_token_type' => $subjectTokenType,
            'subject_token' => $subjectToken,
            'client_id' => $this->getConfig('client_id'),
            'client_secret' => $this->getConfig('client_secret'),
            'scope'         => implode(' ', $this->getConfig('scopes'))
        );

        if (!empty($audience)) {
            $post_data['audience'] = $audience;
        }

        # Consider Basic authentication if provider config is set this way
        if (in_array('client_secret_basic', $token_endpoint_auth_methods_supported, true)) {
            $headers = ['Authorization: Basic ' . base64_encode(urlencode($this->getConfig('client_id')) . ':' . urlencode($this->getConfig('client_secret')))];
            unset($post_data['client_secret']);
            unset($post_data['client_id']);
        }

        // Convert token params to string format
        $post_params = http_build_query($post_data, null, '&', $this->enc_type);

        return json_decode($this->fetchURL($token_endpoint, $post_params, $headers));
    }


    /**
     * Requests Access token with refresh token
     *
     * @param string $refresh_token
     * @return mixed
     * @throws OpenIdConnectException
     */
    public function refreshToken($refresh_token) {
        $token_endpoint = $this->getConfig('token_endpoint');
        $token_endpoint_auth_methods_supported = $this->getConfig('token_endpoint_auth_methods_supported', ['client_secret_basic']);

        $headers = [];

        $grant_type = 'refresh_token';

        $token_params = [
            'grant_type' => $grant_type,
            'refresh_token' => $refresh_token,
            'client_id' => $this->getConfig('client_id'),
            'client_secret' => $this->getConfig('client_secret'),
            'scope'         => implode(' ', $this->getConfig('scopes')),
        ];

        # Consider Basic authentication if provider config is set this way
        if (in_array('client_secret_basic', $token_endpoint_auth_methods_supported, true)) {
            $headers = ['Authorization: Basic ' . base64_encode(urlencode($this->getConfig('client_id')) . ':' . urlencode($this->getConfig('client_secret')))];
            unset($token_params['client_secret']);
            unset($token_params['client_id']);
        }

        // Convert token params to string format
        $token_params = http_build_query($token_params, '', '&', $this->encType);

        $json = json_decode($this->fetchURL($token_endpoint, $token_params, $headers));

        if (isset($json->access_token)) {
            $this->accessToken = $json->access_token;
        }

        if (isset($json->refresh_token)) {
            $this->refreshToken = $json->refresh_token;
        }

        return $json;
    }

    /**
     * @param array $keys
     * @param array $header
     * @return object
     * @throws OpenIdConnectException
     */
    private function getKeyForHeader($keys, $header) {
        foreach ($keys as $key) {
            if ($key->kty === 'RSA') {
                if (!isset($header->kid) || $key->kid === $header->kid) {
                    return $key;
                }
            } else {
                if (isset($key->alg) && $key->alg === $header->alg && $key->kid === $header->kid) {
                    return $key;
                }
            }
        }
        if ($this->additionalJwks) {
            foreach ($this->additionalJwks as $key) {
                if ($key->kty === 'RSA') {
                    if (!isset($header->kid) || $key->kid === $header->kid) {
                        return $key;
                    }
                } else {
                    if (isset($key->alg) && $key->alg === $header->alg && $key->kid === $header->kid) {
                        return $key;
                    }
                }
            }
        }
        if (isset($header->kid)) {
            throw new OpenIdConnectException('Unable to find a key for (algorithm, kid):' . $header->alg . ', ' . $header->kid . ')');
        }

        throw new OpenIdConnectException('Unable to find a key for RSA');
    }


    /**
     * @param string $hashtype
     * @param object $key
     * @param $payload
     * @param $signature
     * @param $signatureType
     * @return bool
     * @throws OpenIdConnectException
     */
    private function verifyRSAJWTsignature($hashtype, $key, $payload, $signature, $signatureType) {
        if (!class_exists('\phpseclib3\Crypt\RSA') && !class_exists('\phpseclib\Crypt\RSA') && !class_exists('Crypt_RSA')) {
            throw new OpenIdConnectException('Crypt_RSA support unavailable.');
        }
        if (!(property_exists($key, 'n') && property_exists($key, 'e'))) {
            throw new OpenIdConnectException('Malformed key object');
        }

        /* We already have base64url-encoded data, so re-encode it as
           regular base64 and use the XML key format for simplicity.
        */
        $public_key_xml = "<RSAKeyValue>\r\n".
            '  <Modulus>' . b64url2b64($key->n) . "</Modulus>\r\n" .
            '  <Exponent>' . b64url2b64($key->e) . "</Exponent>\r\n" .
            '</RSAKeyValue>';
        if (class_exists('\phpseclib3\Crypt\RSA', false)) {
            $key = \phpseclib3\Crypt\PublicKeyLoader::load($public_key_xml)
                ->withHash($hashtype);
            if ($signatureType === 'PSS') {
                $key = $key->withMGFHash($hashtype)
                    ->withPadding(\phpseclib3\Crypt\RSA::SIGNATURE_PSS);
            } else {
                $key = $key->withPadding(\phpseclib3\Crypt\RSA::SIGNATURE_PKCS1);
            }
            return $key->verify($payload, $signature);
        } elseif (class_exists('Crypt_RSA', false)) {
            $rsa = new Crypt_RSA();
            $rsa->setHash($hashtype);
            if ($signatureType === 'PSS') {
                $rsa->setMGFHash($hashtype);
            }
            $rsa->loadKey($public_key_xml, Crypt_RSA::PUBLIC_FORMAT_XML);
            $rsa->setSignatureMode($signatureType === 'PSS' ? Crypt_RSA::SIGNATURE_PSS : Crypt_RSA::SIGNATURE_PKCS1);
            return $rsa->verify($payload, $signature);
        } else {
            $rsa = new \phpseclib\Crypt\RSA();
            $rsa->setHash($hashtype);
            if ($signatureType === 'PSS') {
                $rsa->setMGFHash($hashtype);
            }
            $rsa->loadKey($public_key_xml, \phpseclib\Crypt\RSA::PUBLIC_FORMAT_XML);
            $rsa->setSignatureMode($signatureType === 'PSS' ? \phpseclib\Crypt\RSA::SIGNATURE_PSS : \phpseclib\Crypt\RSA::SIGNATURE_PKCS1);
            return $rsa->verify($payload, $signature);
        }
    }

    /**
     * @throws OpenIdConnectException
     */
    private function verifyHMACJWTsignature(string $hashtype, string $key, $payload, $signature): bool
    {
        if (!function_exists('hash_hmac')) {
            throw new OpenIdConnectException('hash_hmac support unavailable.');
        }

        $expected=hash_hmac($hashtype, $payload, $key, true);

        if (function_exists('hash_equals')) {
            return hash_equals($signature, $expected);
        }

        return self::hashEquals($signature, $expected);
    }

    /**
     * @param string $iss
     * @return bool
     * @throws OpenIdConnectException
     */
    protected function validateIssuer($iss) {
        if ($this->issuerValidator !== null) {
            return $this->issuerValidator->__invoke($iss);
        }

        return ($iss === $this->getIssuer() || $iss === $this->getWellKnownIssuer() || $iss === $this->getWellKnownIssuer(true));
    }

    /**
     * @param object $claims
     * @param string|null $accessToken
     * @return bool
     */
    protected function verifyJWTclaims($claims, $accessToken = null) {
        if(isset($claims->at_hash) && isset($accessToken)) {
            if(isset($this->getIdTokenHeader()->alg) && $this->getIdTokenHeader()->alg !== 'none') {
                $bit = substr($this->getIdTokenHeader()->alg, 2, 3);
            } else {
                // TODO: Error case. throw exception???
                $bit = '256';
            }
            $len = ((int)$bit)/16;
            $expected_at_hash = $this->urlEncode(substr(hash('sha'.$bit, $accessToken, true), 0, $len));
        }
        return (($this->validateIssuer($claims->iss))
            && (($claims->aud === $this->getConfig('client_id')) || in_array($this->getConfig('client_id'), $claims->aud, true))
            && (!isset($claims->nonce) || $claims->nonce === $this->getNonce())
            && ( !isset($claims->exp) || ((gettype($claims->exp) === 'integer') && ($claims->exp >= time() - $this->leeway)))
            && ( !isset($claims->nbf) || ((gettype($claims->nbf) === 'integer') && ($claims->nbf <= time() + $this->leeway)))
            && ( !isset($claims->at_hash) || !isset($accessToken) || $claims->at_hash === $expected_at_hash )
        );
    }

    /**
     * @param string $str
     * @return string
     */
    protected function urlEncode($str) {
        $enc = base64_encode($str);
        $enc = rtrim($enc, '=');
        $enc = strtr($enc, '+/', '-_');
        return $enc;
    }

    /**
     * @param string $jwt encoded JWT
     * @param int $section the section we would like to decode
     * @return object
     */
    protected function decodeJWT($jwt, $section = 0) {

        $parts = explode('.', $jwt);
        return json_decode(base64url_decode($parts[$section]));
    }

    /**
     *
     * @param string|null $attribute optional
     *
     * Attribute        Type        Description
     * user_id          string      REQUIRED Identifier for the End-User at the Issuer.
     * name             string      End-User's full name in displayable form including all name parts, ordered according to End-User's locale and preferences.
     * given_name       string      Given name or first name of the End-User.
     * family_name      string      Surname or last name of the End-User.
     * middle_name      string      Middle name of the End-User.
     * nickname         string      Casual name of the End-User that may or may not be the same as the given_name. For instance, a nickname value of Mike might be returned alongside a given_name value of Michael.
     * profile          string      URL of End-User's profile page.
     * picture          string      URL of the End-User's profile picture.
     * website          string      URL of End-User's web page or blog.
     * email            string      The End-User's preferred e-mail address.
     * verified         boolean     True if the End-User's e-mail address has been verified; otherwise false.
     * gender           string      The End-User's gender: Values defined by this specification are female and male. Other values MAY be used when neither of the defined values are applicable.
     * birthday         string      The End-User's birthday, represented as a date string in MM/DD/YYYY format. The year MAY be 0000, indicating that it is omitted.
     * zoneinfo         string      String from zoneinfo [zoneinfo] time zone database. For example, Europe/Paris or America/Los_Angeles.
     * locale           string      The End-User's locale, represented as a BCP47 [RFC5646] language tag. This is typically an ISO 639-1 Alpha-2 [ISO639‑1] language code in lowercase and an ISO 3166-1 Alpha-2 [ISO3166‑1] country code in uppercase, separated by a dash. For example, en-US or fr-CA. As a compatibility note, some implementations have used an underscore as the separator rather than a dash, for example, en_US; Implementations MAY choose to accept this locale syntax as well.
     * phone_number     string      The End-User's preferred telephone number. E.164 [E.164] is RECOMMENDED as the format of this Claim. For example, +1 (425) 555-1212 or +56 (2) 687 2400.
     * address          JSON object The End-User's preferred address. The value of the address member is a JSON [RFC4627] structure containing some or all of the members defined in Section 2.4.2.1.
     * updated_time     string      Time the End-User's information was last updated, represented as a RFC 3339 [RFC3339] datetime. For example, 2011-01-03T23:58:42+0000.
     *
     * @return mixed
     *
     * @throws OpenIdConnectException
     */
    public function requestUserInfo($attribute = null) {

        $user_info_endpoint = $this->getConfig('userinfo_endpoint');
        $schema = 'openid';

        $user_info_endpoint .= '?schema=' . $schema;

        //The accessToken has to be sent in the Authorization header.
        // Accept json to indicate response type
        $headers = ["Authorization: Bearer {$this->accessToken}",
            'Accept: application/json'];

        $user_json = json_decode($this->fetchURL($user_info_endpoint,null,$headers));
        if ($this->getResponseCode() <> 200) {
            throw new OpenIdConnectException(
                'The communication to retrieve user data has failed with status code '.$this->getResponseCode());
        }
        $this->userInfo = $user_json;

        if($attribute === null) {
            return $this->userInfo;
        }

        if (property_exists($this->userInfo, $attribute)) {
            return $this->userInfo->$attribute;
        }

        return null;
    }

    /**
     * todo: refactor to use guzzle instead
     * @param string $url
     * @param string | null $post_body string If this is set the post type will be POST
     * @param array $headers Extra headers to be send with the request. Format as 'NameHeader: ValueHeader'
     * @return mixed
     * @throws OpenIdConnectException
     */
    protected function fetchURL($url, $post_body = null, $headers = []) {

        // OK cool - then let's create a new cURL resource handle
        $ch = curl_init();

        // Determine whether this is a GET or POST
        if ($post_body !== null) {
            // curl_setopt($ch, CURLOPT_POST, 1);
            // Alows to keep the POST method even after redirect
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);

            // Default content type is form encoded
            $content_type = 'application/x-www-form-urlencoded';

            // Determine if this is a JSON payload and add the appropriate content type
            if (is_object(json_decode($post_body))) {
                $content_type = 'application/json';
            }

            // Add POST-specific headers
            $headers[] = "Content-Type: {$content_type}";

        }

        // If we set some headers include them
        if(count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $url);

        if (isset($this->httpProxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->httpProxy);
        }

        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Allows to follow redirect
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);

        // Download the given URL, and return output
        $output = curl_exec($ch);

        // HTTP Response code from server may be required from subclass
        $info = curl_getinfo($ch);
        $this->responseCode = $info['http_code'];

        if ($output === false) {
            throw new OpenIdConnectException('Curl error: (' . curl_errno($ch) . ') ' . curl_error($ch));
        }

        // Close the cURL resource, and free system resources
        curl_close($ch);

        return $output;
    }

    /**
     * @return mixed
     * @throws OpenIdConnectException
     */
    public function getProviderURL() {
        if (!($this->config['provider_url'] ?? null)) {
            throw new OpenIdConnectException('The provider URL has not been set');
        }

        return $this->config['provider_url'];
    }

    public function mergeConfig(array $config) {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Introspect a given token - either access token or refresh token.
     * @see https://tools.ietf.org/html/rfc7662
     *
     * @throws OpenIdConnectException
     */
    public function introspectToken(string $token, string $token_type_hint = '', ?string $clientId = null, ?string $clientSecret = null) {
        $introspection_endpoint = $this->getConfig('introspection_endpoint');

        $post_data = ['token' => $token];

        if ($token_type_hint) {
            $post_data['token_type_hint'] = $token_type_hint;
        }
        $clientId = $clientId !== null ? $clientId : $this->getConfig('client_id');
        $clientSecret = $clientSecret !== null ? $clientSecret : $this->getConfig('client_id');

        // Convert token params to string format
        $post_params = http_build_query($post_data, '', '&');
        $headers = ['Authorization: Basic ' . base64_encode(urlencode($clientId) . ':' . urlencode($clientSecret)),
            'Accept: application/json'];

        return json_decode($this->fetchURL($introspection_endpoint, $post_params, $headers));
    }

    /**
     * Set the access token.
     *
     * May be required for subclasses of this Client.
     *
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getAccessToken() {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken() {
        return $this->refreshToken;
    }

    /**
     * Set timeout (seconds)
     *
     * @param int $timeout
     */
    public function setTimeout($timeout) {
        $this->timeOut = $timeout;
    }

    /**
     * @return int
     */
    public function getTimeout() {
        return $this->timeOut;
    }

    /**
     * Safely calculate length of binary string
     * @param string $str
     * @return int
     */
    private static function safeLength($str) {
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, '8bit');
        }
        return strlen($str);
    }

    /**
     * Where hash_equals is not available, this provides a timing-attack safe string comparison
     * @param string $str1
     * @param string $str2
     * @return bool
     */
    private static function hashEquals($str1, $str2) {
        $len1=static::safeLength($str1);
        $len2=static::safeLength($str2);

        //compare strings without any early abort...
        $len = min($len1, $len2);
        $status = 0;
        for ($i = 0; $i < $len; $i++) {
            $status |= (ord($str1[$i]) ^ ord($str2[$i]));
        }
        //if strings were different lengths, we fail
        $status |= ($len1 ^ $len2);
        return ($status === 0);
    }

    public function setUrlEncoding($curEncoding) {
        switch ($curEncoding)
        {
            case PHP_QUERY_RFC1738:
                $this->encType = PHP_QUERY_RFC1738;
                break;

            case PHP_QUERY_RFC3986:
                $this->encType = PHP_QUERY_RFC3986;
                break;

            default:
                break;
        }

    }
}
