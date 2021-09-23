<?php
/**
 * Copyright (c) White Phoenix Media Ltd.  All Rights Reserved.
 * Product Name: test
 * @author John Easton <john@whitephoenixmedia.co.uk>
 * @copyright 2005-2019 White Phoenix Media Ltd / John Easton
 * @license Proprietary and confidential
 */

namespace WhitePhoenixMedia\Utilities\DomainParser;

class domainParser
{
	protected $scheme;
	protected $rootSuffix;
	protected $primaryDomain;
	protected $subdomain;
	protected $path;
	protected $port;
	protected $tld;
	
	/**
	 * Original data sources from publicsuffix.org, converted to PHP Serialised Array for all ICANN domains
	 */
	private static $tlds;
	
	public function __construct( $domainString )
	{
		$data = parse_url( $domainString );
		
		if( isset( $data['scheme'] ) ) {
			$this->scheme = strtolower( $data['scheme'] );
		}
		
		if( isset( $data['port'] ) ) {
			$this->port = $data['port'];
		}
		
		// If scheme is missing, host is saved as path
		if( ! isset( $data['host'] ) && isset( $data['path'] ) ) {
			$url = preg_split( "~/~", $data['path'], 2 );
			
			$domainPart = $url[0];
			
			if( isset( $url[1] ) ) {
				$this->path = $url[1];
			}
			
			$this->identifyTld( $domainPart );
		} elseif( isset( $data['host'] ) ) {
			$this->identifyTld( $data['host'] );
		}
		
	}
	
	private function identifyTld( $host )
	{
		if( ! isset( self::$tlds ) ) {
			$this->loadTldFromFile();
		}
		
		// If no dot, assume local hostname
		if( strpos( $host, '.' ) === false ) {
			$this->primaryDomain = $host;
			
			return;
		}
		
		$hostParts = preg_split( "/\./", $host );
		$lastPart  = array_pop( $hostParts );
		
		if( isset( self::$tlds[ $lastPart ] ) ) {
			// Last Part is in list and has children
			$matched      = [ $lastPart ];
			$remainingTld = self::$tlds[ $lastPart ];
			$lastPart     = array_pop( $hostParts );
			
			while( isset( $remainingTld[ $lastPart ] ) ) {
				// Matches child
				$remainingTld = $remainingTld[ $lastPart ];
				$matched[]    = $lastPart;
				$lastPart     = array_pop( $hostParts );
			}
			
			if( in_array( $lastPart, $remainingTld ) ) {
				// Final element is in list without children
				$matched[] = $lastPart;
			} else {
				// Final element not Tld - return to hostParts
				$hostParts[] = $lastPart;
			}
			
			$matched = array_reverse( $matched );
			
			$this->rootSuffix    = implode( '.', $matched );
			$this->primaryDomain = array_pop( $hostParts );
			$this->subdomain     = implode( '.', $hostParts );
			
		} elseif( in_array( $lastPart, self::$tlds ) ) {
			// Last Part is in list without children
			$this->rootSuffix = $lastPart;
			$this->primaryDomain = array_pop( $hostParts );
			$this->subdomain     = implode( '.', $hostParts );
		} else {
			// No Tld.  First part assumed to be primary domain, rest subdomains
			$this->primaryDomain = $lastPart;
			$this->subdomain     = implode( '.', $hostParts );
		}
		
		if( strpos( $this->rootSuffix, '.' ) ) {
			$tldParts = preg_split( "/\./", $host );
			$this->tld = array_pop( $tldParts );
		} else {
			$this->tld = $this->rootSuffix;
		}
		
	}
	
	private function loadTldFromFile()
	{
		// Load Tld data
		$handler   = fopen( __DIR__.DIRECTORY_SEPARATOR.'tld.dat', 'r' );
		$data      = fgets( $handler );
		self::$tlds = unserialize( $data );
	}
	
}
