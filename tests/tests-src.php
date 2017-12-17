<?php

class Tests_Iframe_Src extends WP_UnitTestCase {

	public function test_iframe_src_exists() {

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

				$args  = array( 'url' => $test['url'] );

				$this->assertNotContains( 'Error', arve_shortcode_arve( $args ) );
				$this->assertRegExp( '#<iframe[^>]+src="http#i', arve_shortcode_arve( $args ) );
				$this->assertContains( sprintf( 'data-provider="%s"', $provider ), arve_shortcode_arve( $args ) );

			endforeach;

		endforeach;
	}
}
