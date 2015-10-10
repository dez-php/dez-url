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

    class Url extends Injectable {

        /**
         * @var string
         */
        protected $staticPath   = '/';

        /**
         * @var string
         */
        protected $basePath     = '/';

        /**
         * @return Request
         */
        public function getRequest() {
            return $this->request;
        }

        /**
         * @return Router
         */
        public function getRouter() {
            return $this->router;
        }

        /**
         * @return EventDispatcher
         */
        public function getEventDispatcher() {
            return $this->eventDispatcher;
        }

        /**
         * @return string
         */
        public function getStaticPath() {
            return $this->staticPath;
        }

        /**
         * @param string $staticPath
         * @return $this
         */
        public function setStaticPath($staticPath) {
            $this->staticPath = $staticPath;
            return $this;
        }

        /**
         * @return string
         */
        public function getBasePath() {
            return $this->basePath;
        }

        /**
         * @param string $basePath
         * @return $this
         */
        public function setBasePath( $basePath ) {
            $this->basePath = $basePath;
            return $this;
        }

        public function path( $uri = '', array $query = [] ) {
            $uri    = ltrim( $uri, '/' );
            $uri    = "{$this->getBasePath()}{$uri}";
            return ( new Uri( $uri ) )->setQueryArray( $query )->local();
        }

    }