<?php

namespace Dez\Url;

use Dez\DependencyInjection\Injectable;
use Dez\EventDispatcher\Dispatcher as EventDispatcher;
use Dez\Http\Request;
use Dez\Router\Router;

/**
 * @property Request request
 * @property Router router
 * @property EventDispatcher eventDispatcher
 */
class Url extends Injectable
{

    /**
     * @var string
     */
    protected $staticPath = '/';

    /**
     * @var string
     */
    protected $basePath = '/';

    /**
     * @return Request
     */
    public function getRequest() : Request
    {
        return $this->request;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher() : EventDispatcher
    {
        return $this->eventDispatcher;
    }

    /**
     * @return Router
     */
    public function getRouter() : Router
    {
        return $this->router;
    }

    /**
     * @param string $macros
     * @param array $params
     * @param array $query
     * @return string
     */
    public function create(string $macros, array $params = [], array $query = []) : string
    {
        $builder = new Builder($macros, $params, $this->getRouter());

        if ($builder->make()) {
            return $this->path($builder->getLink(), $query);
        }

        return '';
    }

    /**
     * @param string $path
     * @param array $query
     * @return string
     */
    public function path(string $path, array $query = []) : string
    {
        $path = ltrim($path, '/');
        $basepath = rtrim($this->getBasePath(), '/');

        return (new Uri("$basepath/$path"))->setQueryArray($query)->local();
    }

    /**
     * @param string $path
     * @param array $query
     * @param string $fragment
     * @return string
     */
    public function full(string $path, array $query = [], string $fragment) : string
    {
        $path = ltrim($path, '/');
        $basepath = rtrim($this->getBasePath(), '/');

        $uri = (new Uri("$basepath/$path"))
            ->setSchema($this->request->getSchema())
            ->setHost($this->request->getServerHttp('host'))
            ->setQueryArray($query);

        if ($fragment !== null) {
            $uri->setFragment(ltrim($fragment, '#'));
        }

        return $uri->full();
    }

    /**
     * @return string
     */
    public function getBasePath() : string
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     * @return Url
     */
    public function setBasePath(string $basePath) : static
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * @param string $path
     * @param array $query
     * @return string
     */
    public function staticPath(string $path, array $query = [])
    {
        $path = ltrim($path, '/');
        $staticpath = rtrim($this->getStaticPath(), '/');

        return (new Uri("$staticpath/$path"))->local();
    }

    /**
     * @return string
     */
    public function getStaticPath() : string
    {
        return $this->staticPath;
    }

    /**
     * @param string $staticPath
     * @return Url
     */
    public function setStaticPath(string $staticPath) : static
    {
        $this->staticPath = $staticPath;

        return $this;
    }

}