<?php
function has_arg( string $arg ): bool {
	$getopt = getopt( null, [ $arg ] );
	return isset($getopt[ $arg ]);
}

function required_arg( string $arg ): string {

	$getopt = getopt( null, [ "$arg:" ] );

	if ( empty($getopt[ $arg ]) ) {
		echo "need --$arg=x";
		exit(1);
	}

	return $getopt[ $arg ];
}

function arg_with_default( string $arg, $default ): string {

	$getopt = getopt( null, [ "$arg::" ] );

	if ( empty($getopt[ $arg ]) ) {
		return $default;
	}

	return $getopt[ $arg ];
}

function sys( string $command, array $args = [] ): ?string {

	foreach ( $args as $k => $v ) {
		$command .= " --$k=" . escapeshellarg($v);
	}

	echo "Executing: $command" . PHP_EOL;

	$out = system( $command, $exit_code );

	if ( 0 !== $exit_code || false === $out ) {
		echo 'Error, output: ';
		var_dump($out);
		echo "Exit Code: $exit_code." . PHP_EOL;
		exit($exit_code);
	}

	return $out;
}
