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

        /**
         * @param $macros
         * @param array $params
         * @param array $query
         * @return null|string
         */
        public function create( $macros, array $params = [], array $query = [] ) {
            $builder    = new Builder( $macros, $params, $this->getRouter() );
            if( $builder->search() ) {
                return $this->path( $builder->getLink(), $query );
            } else {
                return null;
            }
        }

        /**
         * @param string $path
         * @param array $query
         * @return string
         */
        public function path( $path = '', array $query = [] ) {
            $path       = ltrim( $path, '/' );
            $basepath   = rtrim( $this->getBasePath(), '/' );
            return ( new Uri( "$basepath/$path" ) )->setQueryArray( $query )->local();
        }

        /**
         * @param string $path
         * @param array $query
         * @return string
         */
        public function staticPath( $path = '', array $query = [] ) {
            $path       = ltrim( $path, '/' );
            $staticpath = rtrim( $this->getStaticPath(), '/' );
            return ( new Uri( "$staticpath/$path" ) )->local();
        }

        /**
         * @param string $path
         * @param array $query
         * @param bool|false $fragment
         * @return string
         */
        public function full( $path  = '', array $query = [], $fragment = false ) {
            $path       = ltrim( $path, '/' );
            $basepath   = rtrim( $this->getBasePath(), '/' );

            $uri        = ( new Uri( "$basepath/$path" ) )
                ->setSchema( $this->request->getSchema() )
                ->setHost( $this->request->getServerHttp( 'host' ) )
                ->setQueryArray( $query );

            if( $fragment !== false ) {
                $uri->setFragment( ltrim( $fragment, '#' ) );
            }

            return $uri->full();
        }

    }