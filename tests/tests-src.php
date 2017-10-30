<?php

class Tests_Src extends WP_UnitTestCase {

	public function test_src_exists() {

		$properties = arve_get_host_properties();

		$this->assertTrue( is_array( $properties ) );
		$this->assertNotEmpty( $properties );

		foreach( $properties as $provider => $props ) :

			$this->assertNotEmpty( $props, $provider );
			$this->assertTrue( is_array( $props ), $provider );

			if ( empty( $props['regex'] ) ) {
				continue;
			}

			$this->assertArrayHasKey( 'tests', $props, $provider );
			$this->assertNotEmpty( $props['tests'], $provider );
			$this->assertTrue( is_array( $props['tests'] ), $provider );

			foreach( $props['tests'] as $test ) :

				$this->assertNotEmpty( $test, $provider );
				$this->assertTrue( is_array( $test ), $provider );
				$this->assertArrayHasKey( 'id',  $test, $provider );
				$this->assertArrayHasKey( 'url', $test, $provider );

				$output = apply_filters( 'the_content', $url['url'] );

				$this->assertNotContains( 'Error', $output );
				$this->assertRegExp( '#<iframe[^>]+src="http#i', $output );

			endforeach;

		endforeach;
	}
}
