<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloudSysCoreHttpclient {
	public static function get($host, $data, $timeout = 5) {
		return function_exists ( "curl_init" ) ? self::curlRequest ( $host, $data, $timeout ) : self::request ( $host, $data, 'GET', $timeout );
	}
	
	public static function post($host, $data, $timeout = 5) {
		return function_exists ( "curl_init" ) ? self::curlRequest ( $host, $data, $timeout ) : self::request ( $host, $data, 'POST', $timeout );
	}
	
	public static function request($host, $data, $method = 'GET', $timeout = 5) {
		$parse = parse_url ( $host );
		$method = strtoupper ( $method );
		if (empty ( $parse ))
			return null;
		if (! isset ( $parse ['port'] ) || ! $parse ['port'])
			$parse ['port'] = '80';
		if (! in_array ( $method, array ('POST', 'GET' ) ))
			return null;
		
		$parse ['host'] = str_replace ( array ('http://', 'https://' ), array ('', 'ssl://' ), $parse ['scheme'] . "://" ) . $parse ['host'];
		if (! $fp = @fsockopen ( $parse ['host'], $parse ['port'], $errnum, $errstr, $timeout ))
			return null;
		
		$contentLength = '';
		$postContent = '';
		$query = isset ( $parse ['query'] ) ? $parse ['query'] : '';
		$parse ['path'] = str_replace ( array ('\\', '//' ), '/', $parse ['path'] ) . "?" . $query;
		if ($method == 'GET') {
			substr ( $data, 0, 1 ) == '&' && $data = substr ( $data, 1 );
			$parse ['path'] .= ($query ? '&' : '') . $data;
		} elseif ($method == 'POST') {
			$contentLength = "Content-length: " . strlen ( $data ) . "\r\n";
			$postContent = $data;
		}
		$write = $method . " " . $parse ['path'] . " HTTP/1.0\r\n";
		$write .= "Host: " . $parse ['host'] . "\r\n";
		$write .= "Content-type: application/x-www-form-urlencoded\r\n";
		$write .= $contentLength;
		$write .= "Connection: close\r\n\r\n";
		$write .= $postContent;
		@fwrite ( $fp, $write );
		
		$responseText = '';
		while ( $data = fread ( $fp, 4096 ) ) {
			$responseText .= $data;
		}
		@fclose ( $fp );
		$responseText = trim ( stristr ( $responseText, "\r\n\r\n" ), "\r\n" );
		return $responseText;
	}
	
	public static function curlRequest($host, $data, $timeout = 5) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_URL, $host );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ('Expect:' ) );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		$result = curl_exec ( $ch );
		if ($error = curl_error ( $ch )) {
			return null;
		}
		curl_close ( $ch );
		return $result;
	}
}