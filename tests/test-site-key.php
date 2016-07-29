<?php

class MemcachedUnitTestsSiteKey extends MemcachedUnitTests {

	protected $blog_id;

	public function setUp() {

		parent::setUp();

		if ( is_multisite() ) {
			$this->blog_id = 1;
		} else {
			$this->blog_id = 'wptests_';
		}

	}

	public function test_get_site_key_always_same() {

		$keys = [];

		for ( $i = 0; $i <= 10; $i++ ) {

			$keys[] = $this->object_cache->get_site_key( $this->blog_id );

		}

		$this->assertEquals( count( array_unique( $keys ) ), 1 );

	}

	public function test_get_site_key_is_part_of_key() {

		$site_key = $this->object_cache->get_site_key( $this->blog_id );
		$this->assertContains( $site_key, $this->object_cache->key( 'test-key', 'default' ) );

		$this->object_cache->set_site_key( $this->blog_id );
		$site_key = $this->object_cache->get_site_key( $this->blog_id );
		$this->assertContains( $site_key, $this->object_cache->key( 'test-key', 'default' ) );

	}

	public function test_cache_flush_after_changing_site_key() {

		$value = 'test-value';
		$this->object_cache->set( 'test-key', $value );
		$this->assertEquals( $value, $this->object_cache->get( 'test-key' ) );

		$this->object_cache->set_site_key( $this->blog_id );

		$this->assertNotEquals( $value, $this->object_cache->get( 'test-key' ) );

	}

	public function test_cache_does_not_flush_after_changing_different_site_key() {

		$value = 'test-value';
		$this->object_cache->set( 'test-key', $value );
		$this->assertEquals( $value, $this->object_cache->get( 'test-key' ) );

		if ( ! is_multisite() ) {
			$this->object_cache->set_site_key( 1 );
			$this->assertEquals( $value, $this->object_cache->get( 'test-key' ) );
		}

		$this->object_cache->set_site_key( 100 );
		$this->assertEquals( $value, $this->object_cache->get( 'test-key' ) );

		$this->object_cache->set_site_key( 'global' );
		$this->assertEquals( $value, $this->object_cache->get( 'test-key' ) );

	}

	public function test_switch_to_blog() {

		if ( ! is_multisite() ) {
			return;
		}

		global $blog_id;
		$old_blog_id = $blog_id;

		$this->object_cache->set( 'test-key-1', 'test-value-1' );
		$this->assertEquals( 'test-value-1', $this->object_cache->get( 'test-key-1' ) );

		$this->object_cache->switch_to_blog( 100 );
		$this->object_cache->set_site_key( 100 );
		$this->object_cache->set( 'test-key-100', 'test-value-100' );
		$this->assertEquals( 'test-value-100', $this->object_cache->get( 'test-key-100' ) );
		$this->assertNotEquals( 'test-value-1', $this->object_cache->get( 'test-key-1' ) );

		$this->object_cache->switch_to_blog( 'global' );
		$this->object_cache->set_site_key( 'global' );
		$this->object_cache->set( 'test-key-global', 'test-value-global' );
		$this->assertEquals( 'test-value-global', $this->object_cache->get( 'test-key-global' ) );
		$this->assertNotEquals( 'test-value-1', $this->object_cache->get( 'test-key-1' ) );
		$this->assertNotEquals( 'test-value-100', $this->object_cache->get( 'test-key-100' ) );

		$this->object_cache->switch_to_blog( $old_blog_id );
		$this->assertEquals( 'test-value-1', $this->object_cache->get( 'test-key-1' ) );
		$this->assertNotEquals( 'test-value-100', $this->object_cache->get( 'test-key-100' ) );
		$this->assertNotEquals( 'test-value-global', $this->object_cache->get( 'test-key-global' ) );

	}
}
