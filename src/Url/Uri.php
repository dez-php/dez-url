<?php

namespace Dez\Url;

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
   * @param $uri
   */
  public function __construct($uri)
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
   * @param array $components
   * @return string
   */
  protected function build(array $components = [])
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
    
    if (count($components) > 0) foreach ($components as $component) {
      
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
          } else if ($this->getUser()) {
            $uriParts['credentials'] = "{$this->getUser()}@";
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
    
    return implode('', $uriParts);
    
  }
  
  /**
   * @return string
   */
  public function full()
  {
    return $this->build(['schema', 'user', 'host', 'port', 'path', 'query', 'fragment']);
  }
  
  /**
   * @return string
   */
  public function host()
  {
    return $this->build(['schema', 'user', 'host', 'port']);
  }
  
  /**
   * @return string
   */
  public function local()
  {
    return $this->build(['path', 'query', 'fragment']);
  }
  
  /**
   * @return mixed
   */
  public function getSchema()
  {
    return $this->schema;
  }
  
  /**
   * @param mixed $schema
   * @return $this
   */
  public function setSchema($schema)
  {
    $this->schema = $schema;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getUser()
  {
    return $this->user;
  }
  
  /**
   * @param mixed $user
   * @return $this
   */
  public function setUser($user)
  {
    $this->user = $user;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getPassword()
  {
    return $this->password;
  }
  
  /**
   * @param mixed $password
   * @return $this
   */
  public function setPassword($password)
  {
    $this->password = $password;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getHost()
  {
    return $this->host;
  }
  
  /**
   * @param mixed $host
   * @return $this
   */
  public function setHost($host)
  {
    $this->host = $host;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getPort()
  {
    return $this->port;
  }
  
  /**
   * @param mixed $port
   * @return $this
   */
  public function setPort($port)
  {
    $this->port = $port;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getPath()
  {
    return $this->path;
  }
  
  /**
   * @param mixed $path
   * @return $this
   */
  public function setPath($path)
  {
    $this->path = $path;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getQueryString()
  {
    $this->setQueryString(http_build_query($this->getQueryArray()));
    return $this->queryString;
  }
  
  /**
   * @param mixed $queryString
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
  public function getQueryArray()
  {
    return $this->queryArray;
  }
  
  /**
   * @param array $queryArray
   * @return $this
   */
  public function setQueryArray($queryArray)
  {
    $this->queryArray = $queryArray;
    return $this;
  }
  
  /**
   * @param $name
   * @return bool
   */
  public function hasQuery($name)
  {
    return isset($this->queryArray[$name]);
  }
  
  /**
   * @param $name
   * @return null
   */
  public function getQuery($name)
  {
    return $this->hasQuery($name) ? $this->queryArray[$name] : null;
  }
  
  /**
   * @param $name
   * @param $value
   * @return $this
   */
  public function setQuery($name, $value)
  {
    $this->queryArray[$name] = $value;
    return $this;
  }
  
  /**
   * @param $name
   * @return $this
   */
  public function removeQuery($name)
  {
    if ($this->hasQuery($name)) {
      unset($name);
    }
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getFragment()
  {
    return $this->fragment;
  }
  
  /**
   * @param mixed $fragment
   * @return $this
   */
  public function setFragment($fragment)
  {
    $this->fragment = $fragment;
    return $this;
  }
  
}