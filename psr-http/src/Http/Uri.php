<?php namespace Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    private static $unreservedCharacters = 'a-zA-Z0-9_\-\.~';
    private static $subDelimiterCharacters = '!\$&\'\(\)\*\+,;=';
    private static $defaultPorts = [
        'ftp'    => 21,
        'telnet' => 23,
        'tn3270' => 23,
        'gopher' => 70,
        'http'   => 80,
        'pop'    => 110,
        'nntp'   => 119,
        'news'   => 119,
        'imap'   => 143,
        'ldap'   => 389,
        'https'  => 443,
    ];

    private $scheme = '';
    private $userInfo = '';
    private $host = '';
    private $port = null;
    private $path = '';
    private $query = '';
    private $fragment = '';

    public function __construct(?string $uri = '') {
        if ($uri === null) {
            return;
        }

        $this->setParts(parse_url($uri));
    }

    public function setParts(array $parts): UriInterface {
        $this->scheme = $this->parseScheme($parts['scheme'] ?? '');
        $this->host = $this->parseHost($parts['host'] ?? '');
        $this->port = $this->parsePort($parts['port'] ?? null);
        $this->userInfo = $this->parseUserInfo($parts['user'] ?? '');
        $this->path = $this->parsePath($parts['path'] ?? '');
        $this->query = $this->parseQuery($parts['query'] ?? '');
        $this->fragment = $this->parseFragment($parts['fragment'] ?? '');

        if (isset($parts['pass'])) {
            $this->userInfo .= ":{$parts['pass']}";
        }

        return $this;
    }

    public function getScheme(): string {
        return $this->scheme;
    }

    public function getAuthority(): string {
        $authority = $this->host;

        if ($this->userInfo) {
            $authority = "{$this->userInfo}@{$authority}";
        }

        if ($this->port) {
            $authority = "{$authority}:{$this->port}";
        }

        return $authority;
    }

    public function getUserInfo(): string {
        return $this->userInfo;
    }

    public function getHost(): string {
        return $this->host;
    }

    public function getPort(): ?int {
        return $this->port;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getQuery(): string {
        return $this->query;
    }

    public function getFragment(): string {
        return $this->fragment;
    }

    public function withScheme($scheme): UriInterface {
        $scheme = $this->parseScheme($scheme);

        if ($scheme === $this->scheme) {
            return $this;
        }

        $uri = clone $this;
        $uri->scheme = $scheme;

        return $uri;
    }

    public function withUserInfo($user, $password = null): UriInterface {
        $userInfo = $this->parseUserInfo($user);

        if ($userInfo !== '' && $password) {
            $password = strtolower($password);
            $userInfo = "{$userInfo}:{$password}";
        }

        if ($this->userInfo === $userInfo) {
            return $this;
        }

        $uri = clone $this;
        $uri->userInfo = $userInfo;

        return $uri;
    }

    public function withHost($host): UriInterface {
        $host = $this->parseHost($host);

        if ($host === $this->host) {
            return $this;
        }

        $uri = clone $this;
        $uri->host = $host;

        return $uri;
    }

    public function withPort($port): UriInterface {
        $port = $this->parsePort($port);

        if ($port === $this->port) {
            return $this;
        }

        $uri = clone $this;
        $uri->port = $port;

        return $uri;
    }

    public function withPath($path): UriInterface {
        $path = $this->parsePath($path);

        if ($path === $this->path) {
            return $this;
        }

        $uri = clone $this;
        $uri->path = $path;

        return $uri;
    }

    public function withQuery($query): UriInterface {
        $query = $this->parseQuery($query);

        if ($query === $this->query) {
            return $this;
        }

        $uri = clone $this;
        $uri->query = $query;

        return $uri;
    }

    public function withFragment($fragment): UriInterface {
        $fragment = $this->parseFragment($fragment);

        if ($fragment === $this->fragment) {
            return $this;
        }

        $uri = clone $this;
        $uri->fragment = $fragment;

        return $uri;
    }

    public function __toString(): string {
        $uri = '';

        if ($this->scheme) {
            $uri = "{$this->scheme}:";
        }

        $authority = $this->getAuthority();
        if ($authority) {
            $uri = "{$uri}//{$authority}";
        }

        if ($this->path) {
            $isRoot = strpos($this->path, '/') === 0;

            if ($authority) {
                $uri = $isRoot ? "{$uri}{$this->path}" : "{$uri}/{$this->path}";
            } else {
                /** @note `ltrim` is used to reduces multiple leading slashes to one. */
                $uri = $isRoot ? "{$uri}/" . ltrim($this->path, '/') : "{$uri}{$this->path}";
            }
        }

        if ($this->query) {
            $uri = "{$uri}?{$this->query}";
        }

        if ($this->fragment) {
            $uri = "{$uri}#{$this->fragment}";
        }

        return $uri;
    }

    private function parseScheme(string $scheme): string {
        return strtolower($scheme);
    }

    private function parsePort(?int $port): ?int {
        if (null !== $port && ($port < 1 || $port > 65535)) {
            throw new \InvalidArgumentException("Invalid port: {$port}. Must be between 1 and 65535.");
        }

        $defaultPortForScheme = self::$defaultPorts[$this->scheme] ?? null;

        return $port && $defaultPortForScheme !== $port ? (int) $port : null;
    }

    private function parseUserInfo(string $userInfo): string {
        return $userInfo;
    }

    private function parseHost(string $host): string {
        return strtolower($host);
    }

    private function parsePath(string $path): string {
        /** @see https://github.com/guzzle/psr7/blob/master/src/Uri.php#L645 */
        return preg_replace_callback('/(?:[^' . self::$unreservedCharacters . self::$subDelimiterCharacters . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/', function (array $match) {
            return rawurlencode($match[0]);
        }, $path);
    }

    private function parseQuery(string $query): string {
        /** @see https://github.com/guzzle/psr7/blob/master/src/Uri.php#L667 */
        return preg_replace_callback('/(?:[^' . self::$unreservedCharacters . self::$subDelimiterCharacters . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/', function (array $match) {
            return rawurlencode($match[0]);
        }, $query);
    }

    private function parseFragment(string $fragment): string {
        /** @see https://github.com/guzzle/psr7/blob/master/src/Uri.php#L667 */
        return preg_replace_callback('/(?:[^' . self::$unreservedCharacters . self::$subDelimiterCharacters . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/', function (array $match) {
            return rawurlencode($match[0]);
        }, $fragment);
    }
}
