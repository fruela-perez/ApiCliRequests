<?php

// Alejandro Santos fecit, A.D. 2020

class ApiRequest
{
  public const GET       = "GET";
  public const POST      = "POST";
  public const PUT       = "PUT";
  public const DELETE    = "DELETE";

  public const HTTP      = "http";
  public const HTTPS     = "https";

  public const PROTOCOLS = [ self::HTTP, self::HTTPS ];
  public const METHODS   = [ self::GET, self::POST, self::PUT, self::DELETE ];

  private $protocol;
  private $method;
  private $host;
  private $port;
  private $endpoint;
  private $key;
  private $parameters;
  private $body;

  private $url;
  private $response;

  // -- GETTERS AND SETTERS -- //

  public function setProtocol ( $newValue )
  {
    $this->protocol = $newValue;
  }

  public function getProtocol ()
  {
    return $this->protocol;
  }

  public function setMethod ( $newValue )
  {
    $this->method = $newValue;
  }

  public function getMethod ()
  {
    return $this->method;
  }

  public function setHost ( $newValue )
  {
    $this->host = $newValue;
  }

  public function getHost ()
  {
    return $this->host;
  }

  public function setPort ( $newValue )
  {
    $this->port = $newValue;
  }

  public function getPort ()
  {
    return $this->port;
  }

  public function setEndpoint ( $newValue )
  {
    $this->endpoint = $newValue;
  }

  public function getEndpoint ()
  {
    return $this->endpoint;
  }

  public function setKey ( $newValue )
  {
    $this->key = $newValue;
  }

  public function getKey ()
  {
    return $this->key;
  }

  public function setParameters ( $newValue )
  {
    $this->parameters = $newValue;
  }

  public function getParameters ()
  {
    return $this->parameters;
  }

  public function setBody ( $newValue )
  {
    $this->body = $newValue;
  }

  public function getBody ()
  {
    return $this->body;
  }

  // -- READ-ONLY -- //

  public function getUrl ()
  {
    if ( isset ( $this->protocol ) && isset ( $this->host ) && isset ( $this->endpoint) )
    {
      $this->url =
        $this->protocol . "://" . $this->host .
        ( isset ( $this->port ) ? ":" . $this->port : "" ) .
        $this->endpoint;
    }

    return $this->url;
  }

  public function getResponse ()
  {
    return $this->response;
  }

  // -- CONSTRUCTOR -- //

  public function __construct ()
  {
  }

  // -- PRIVATE METHODS -- //

  private function params2urlParams ()
  {
    $arrParams = [];

    foreach ( json_decode ( $this->parameters ) as $key => $value)
    {
      $arrParams [] = $key . "=" . $value;
    }

    if ( count ( $arrParams ) > 0 )
    {
      return "?" . implode ( "&", $arrParams );
    }
    else return "";
  }

  private function validate ()
  {
    if ( isset ( $this->protocol ) && isset ( $this->host ) && isset ( $this->endpoint) )
    {
      return true;
    }

    return false;
  }

  // -- PUBLIC METHODS -- //

  public function execute ()
  {
    if ( $this->validate () )
    {
      $redirect_url = $this->getUrl() . $this->params2urlParams ();

      $ch = curl_init ( $redirect_url );

      if ( $this->method == self::POST )
      {
          curl_setopt ( $ch, CURLOPT_POST, 1 );
          curl_setopt ( $ch, CURLOPT_POSTFIELDS, $body );
      }
      else if ( $this->method == self::DELETE )
      {
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
      }
       else if ( $this->method == self::PUT )
       {
          curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, self::PUT );
          curl_setopt ( $ch, CURLOPT_POSTFIELDS,    $this->body );
      }

      curl_setopt ( $ch, CURLOPT_HEADER,         true );
      curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
      curl_setopt ( $ch, CURLOPT_TIMEOUT,        30 );
      curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
      curl_setopt ( $ch, CURLOPT_HEADER,         0 );
      curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

      $ret = curl_exec ( $ch );

      $http_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );

      if ( $http_code != 200 )
      {
        $this->response = $http_code;
        return false;
      }
      if ( curl_errno ( $ch ) )
      {
        $this->response = 504;
        return false;
      }

      if ( !isset ( $ret ) )
      {
        $this->response = null;
        return false;
      }

      $this->response = $ret;
      return true;
    }
  }

  function toString ()
  {
    $getters = array_filter
    (
      get_class_methods( $this ),
      function ( $method )
      {
        return 'get' === substr ( $method, 0, 3 );
      }
    );

    $ret = "";

    foreach ( $getters as $getter )
    {
      $ret .= str_replace ( "get", "", $getter ) . ": " . $this->{$getter}() . EOL;
    }

    return $ret;
  }
}
