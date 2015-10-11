<?php

    namespace Dez\Url;

    use Dez\Router\Router;
    use Dez\Url\Builder\BuilderException;

    /**
     * Class Builder
     * @package Dez\Url
     */
    class Builder {

        /**
         * @var
         */
        protected $router;

        /**
         * @var
         */
        protected $parameters;

        /**
         * @var bool
         */
        protected $founded  = false;

        /**
         * @var
         */
        protected $link;

        /**
         * @param $macros
         * @param array $parameters
         * @param Router $router
         * @throws BuilderException
         */
        public function __construct( $macros, array $parameters = [], Router $router ) {

            $this->setRouter( $router );

            if( $macros !== '' ) {
                $macros     = explode( ':', $macros );

                if( count( $macros ) == 3 ) {
                    list( $parameters['module'], $parameters['controller'], $parameters['action'] )
                        = $macros;
                } else if( count( $macros ) == 2 ) {
                    list( $parameters['controller'], $parameters['action'] )
                        = $macros;
                } else {
                    $parameters['controller']
                        = $macros[0];
                }

                $this->setParameters( $parameters );
            } else {
                throw new BuilderException( 'The macro can not be empty.' );
            }

        }

        /**
         * @return boolean
         */
        public function search() {
            if( $this->normalSearch()->hardSearch()->isFounded() ) {
                return $this->getLink();
            }
            return false;
        }

        /**
         * @return $this
         */
        protected function normalSearch() {

            foreach( $this->getRouter()->getRoutes() as $route ) {

                if(
                    count( $route->getMatches() ) === 0
                    && count( $route->getMacrosNames() ) > 0
                    && count( $route->getMacrosNames() ) === count( $this->getParameters() )
                ) {

                    if( count( array_diff( $route->getMacrosNames(), array_keys( $this->getParameters() ) ) ) === 0 ) {

                        $parameters     = array_map( function( $item ) {
                            return ":$item";
                        }, array_flip( $this->getParameters() ) );

                        $search         = array_values( $parameters );
                        $replacements   = array_keys( $parameters );

                        $this->setLink( str_replace( $search, $replacements, $route->getPseudoPattern() ) );
                        $this->setFounded( true );

                    }

                }

            }

            return $this;
        }

        /**
         * @return $this
         */
        protected function hardSearch() {

            if( ! $this->isFounded() ) {

                foreach( $this->getRouter()->getRoutes() as $route ) {

                    $matches        = array_diff_key( $this->getParameters(), $route->getMacrosPositions() );
                    $parameters     = array_diff_key( $this->getParameters(), $route->getMatches() );

                    $isEquals       = ( count( array_diff( $matches, $route->getMatches() ) ) === 0 );

                    if( $isEquals && count( $route->getMatches() ) > 0 ) {

                        $replacements   = array_values( $parameters );
                        $search         = array_map( function( $name ) {
                            return ":$name";
                        }, array_keys( $parameters ) );

                        $this->setLink( str_replace( $search, $replacements, $route->getPseudoPattern() ) );
                        $this->setFounded( true );
                    }

                }

            }

            return $this;
        }

        /**
         * @param Router $router
         * @return $this
         */
        public function setRouter( Router $router ) {
            $this->router = $router;
            return $this;
        }

        /**
         * @return Router
         */
        public function getRouter() {
            return $this->router;
        }

        /**
         * @return mixed
         */
        public function getParameters() {
            return $this->parameters;
        }

        /**
         * @param mixed $parameters
         * @return $this
         */
        public function setParameters( $parameters ) {
            $this->parameters = array_map( 'urlencode', $parameters );
            return $this;
        }

        /**
         * @return boolean
         */
        public function isFounded() {
            return $this->founded;
        }

        /**
         * @param boolean $founded
         * @return $this
         */
        public function setFounded( $founded ) {
            $this->founded = $founded;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getLink() {
            return $this->link;
        }

        /**
         * @param mixed $link
         * @return $this
         */
        public function setLink( $link ) {
            $this->link = $link;
            return $this;
        }

    }