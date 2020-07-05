<?php
  require __DIR__ . '/ApiRequest.php';

  const EOL   = "\n";
  const DEBUG = true;

  const ERR_PROTOCOL_NOT_VALID = 1;
  const ERR_PORT_NOT_VALID     = 2;
  const ERR_METHOD_NOT_VALID   = 3;
  const ERR_ARG_NOT_VALID      = 4;
  const ERR_PARAMS_NOT_VALID   = 5;

  const MSG_PROTOCOL_NOT_VALID = "Protocol not valid";
  const MSG_PORT_NOT_VALID     = "Port not valid";
  const MSG_METHOD_NOT_VALID   = "Method not valid";
  const MSG_ARG_NOT_VALID      = "Parameter not valid";
  const MSG_PARAMS_NOT_VALID   = "Parameters not valid encoded";

  $protocol   = ApiRequest::HTTPS;
  $host       = "localhost";
  $port;
  $method     = ApiRequest::GET;
  $endpoint   = "/";
  $key;
  $params;

  $api = new ApiRequest ();

  // Set default values -->

  $api->setProtocol ( $protocol );
  $api->setHost     ( $host );
  $api->setPort     ( $port );
  $api->setMethod   ( $method );
  $api->setEndpoint ( $endpoint );

  for ( $i = 1; $i <= count($argv); $i++ )
  {
    $argument = explode ( "=", $argv[$i] );
    $key      = $argument [0];
    $value    = $argument [1];

    // if ( DEBUG) if ( $key !== "" ) echo $key . ": " . $value . EOL;

    switch ( $key )
    {
      case '--protocol':
        if ( ! in_array ( strtolower ( $value ), $api::PROTOCOLS ) )
        {
          echo MSG_PROTOCOL_NOT_VALID . EOL;
          exit ( ERR_PROTOCOL_NOT_VALID );
        }
        else
        {
          $api->setProtocol = strtolower ( $value );
          echo strtolower ( $value ) . EOL;
        }
        break;

      case '--host':
        if ( isset ( $value) )
        $api->setHost ( $value );
        break;

      case '--port':
        if ( (string) intval ($value) != $value  )
        {
          echo MSG_PORT_NOT_VALID . EOL;
          exit ( ERR_PORT_NOT_VALID );
        }
        else
        {
          $api->setPort ( intval ( $value ) );
        }
        break;

      case '--endpoint':
        if ( isset ( $value) )
        $api->setEndpoint ( $value );
        break;

      case '--method':
        if ( ! in_array ( strtoupper ( $value ), $api::METHODS ) )
        {
          echo MSG_METHOD_NOT_VALID . EOL;
          exit ( ERR_METHOD_NOT_VALID );
        }
        else
        {
          $api->setMethod ( strtoupper ( $value ) );
        }
        break;

      case '--key':
        if ( isset ( $value) )
        $api->setKey ( $value );
        break;

      case '--params':
        if ( ( isset ( $value) ) && ( json_decode ( $value ) != null ) )
        {
          $api->setParameters ( $value );
        }
        else
        {
          echo MSG_PARAMS_NOT_VALID . " " . $key . EOL;
          exit ( ERR_PARAMS_NOT_VALID );
        }
        break;

      case '--body':
        if ( isset ( $value) )
        $api->setBody ( $value );
        break;

      default:
        if ( $key != "" )
        {
          echo MSG_ARG_NOT_VALID . " " . $key . EOL;
          exit ( ERR_ARG_NOT_VALID );
        }
        break;
    }
  }
  echo $api->toString() . EOL;

  if ( $api->execute () )
  {
    var_dump ( json_decode ( $api->getResponse (), true ) );
    echo EOL;
  }
  else
  {
    echo "Error: " . $api->getResponse () . EOL;
  }
