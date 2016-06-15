<?php

declare(strict_types = 1);

namespace Dez\Url;

/**
 * Class Uri
 * @package Dez\Url
 */
/**
 * Class Uri
 * @package Dez\Url
 */
class Uri
{

    /**
     * @var string
     * @url https://ru.wikipedia.org/wiki/URI
     */
    protected $pattern = '~^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~ui';

    /**
     * @var string
     */
    protected $schema;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $queryString;

    /**
     * @var array
     */
    protected $queryArray = [];

    /**
     * @var string
     */
    protected $fragment;

    /**
     * Uri constructor.
     * @param string $uri
     */
    public function __construct(string $uri)
    {

        $components = parse_url($uri, -1);

        if (isset($components['scheme'])) {
            $this->setSchema($components['scheme']);
        }

        if (isset($components['host'])) {
            $this->setHost($components['host']);
        }

        if (isset($components['user'], $components['pass'])) {
            $this->setUser($components['user'])->setPassword($components['pass']);
        }

        if (isset($components['post'])) {
            $this->setPort($components['post']);
        }

        if (isset($components['path'])) {
            $this->setPath($components['path']);
        }

        if (isset($components['query'])) {
            $this->setQueryString($components['query']);
            parse_str($components['query'], $this->queryArray);
        }

        if (isset($components['fragment'])) {
            $this->setFragment($components['fragment']);
        }

    }

    /**
     * @param \string[] ...$components
     * @return string
     */
    protected function build(string ...$components): string
    {

        $uriParts = [
            'schema' => null,
            'credentials' => null,
            'host' => null,
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null,
        ];

        if (count($components) > 0) {
            foreach ($components as $component) {

                switch ($component) {

                    case 'schema':
                        if ($this->getSchema()) {
                            $uriParts['schema'] = "{$this->getSchema()}://";
                        }
                        break;

                    case 'host':
                        if ($this->getHost()) {
                            $uriParts['host'] = $this->getHost();
                        }
                        break;

                    case 'user':
                        if ($this->getUser() && $this->getPassword()) {
                            $uriParts['credentials'] = "{$this->getUser()}:{$this->getPassword()}@";
                        } else {
                            if ($this->getUser()) {
                                $uriParts['credentials'] = "{$this->getUser()}@";
                            }
                        }
                        break;

                    case 'port':
                        if ($this->getPort()) {
                            $uriParts['port'] = ":{$this->getPort()}";
                        }
                        break;

                    case 'path':
                        if ($this->getPath()) {
                            $uriParts['path'] = $this->getPath();
                        } else {
                            $uriParts['path'] = '/';
                        }
                        break;

                    case 'query':
                        if (count($this->getQueryArray()) > 0) {
                            $uriParts['query'] = '?' . $this->getQueryString();
                        }
                        break;

                    case 'fragment':
                        if ($this->getFragment()) {
                            $uriParts['fragment'] = '#' . $this->getFragment();
                        }
                        break;

                    default:
                        break;
                }

            }
        }

        return implode('', $uriParts);
    }

    /**
     * @return string
     */
    public function full(): string
    {
        return $this->build('schema', 'user', 'host', 'port', 'path', 'query', 'fragment');
    }

    /**
     * @return string
     */
    public function host(): string
    {
        return $this->build('schema', 'user', 'host', 'port');
    }

    /**
     * @return string
     */
    public function local(): string
    {
        return $this->build('path', 'query', 'fragment');
    }

    /**
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @param string $schema
     * @return Uri
     */
    public function setSchema(string $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return Uri
     */
    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return Uri
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return Uri
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return string
     */
    public function setPort(int $port): string
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Uri
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        $this->setQueryString(http_build_query($this->getQueryArray()));

        return $this->queryString;
    }

    /**
     * @param $queryString
     * @return $this
     */
    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;

        return $this;
    }

    /**
     * @return array
     */
    public function getQueryArray(): array
    {
        return $this->queryArray;
    }

    /**
     * @param array $queryArray
     * @return Uri
     */
    public function setQueryArray(array $queryArray): self
    {
        $this->queryArray = $queryArray;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasQuery(string $name): bool
    {
        return isset($this->queryArray[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getQuery(string $name): string
    {
        return $this->hasQuery($name) ? $this->queryArray[$name] : '';
    }

    /**
     * @param string $name
     * @param string $value
     * @return Uri
     */
    public function setQuery(string $name, string $value): self
    {
        $this->queryArray[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return Uri
     */
    public function removeQuery(string $name): self
    {
        if ($this->hasQuery($name)) {
            unset($name);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFragment() //: ?string @TODO waiting php version 7.1.x for nullable types
    {
        return $this->fragment;
    }

    /**
     * @param string $fragment
     * @return Uri
     */
    public function setFragment(string $fragment): self
    {
        $this->fragment = $fragment;

        return $this;
    }

}