<?php

$handle = fopen( 'public_suffix_list.txt', 'r' );

echo '<pre>';

$domains = [];
if ($handle) {
	while (($line = fgets($handle)) !== false)
	{
		if( substr( trim($line), 0, 2 ) == '//' )
			continue;
		
		if( empty( trim($line) ) )
			continue;
		
		$split = preg_split( "/\./", $line );
		
		$line = nesting( $split );
		
		$domains = array_merge_recursive( $line, $domains );
		
	}
	
	fclose($handle);
	
} else {
	echo 'Cannot open file';
}

$domains = cleanList($domains);
print_r($domains);

$handle = fopen(__DIR__.DIRECTORY_SEPARATOR.'tld.dat', 'w');
fwrite( $handle, serialize( $domains ) );
fclose($handle);

echo '</pre>';

function nesting( $data )
{
	if( count($data) == 1 ) {
		return [ trim($data[0]) => [] ];
	} else {
		$top = trim(array_pop($data));
		
		if( count($data) == 1 && $data[0] == '*' ) {
			return [ trim($top) => [] ];
		}
		return [ $top => nesting($data) ];
	}
	
}

function cleanList( $data )
{
	foreach( $data as $key => $item ) {
		if( empty($item) ) {
			$data[] = $key;
			unset($data[$key]);
		} else {
			$data[$key] = cleanList($item);
		}
	}
	return $data;
}