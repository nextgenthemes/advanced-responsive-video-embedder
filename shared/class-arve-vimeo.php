<?php

abstract class ARVE_Vimeo_Base{

	const ENDPOINT 			= 'https://api.vimeo.com/';
	const UNAUTH_REQUEST 	= 'oauth/authorize/client';
	const AUTH_REDIRECT 	= 'oauth/authorize/';
	const ACCESS_TOKEN		= 'oauth/access_token';

	/**
	 * Stores Vimeo token
	 * @var string
	 */
	protected $token;
	/**
	 * oAuth client ID
	 * @var string
	 */
	private $client_id;
	/**
	 * oAuth client secret
	 * @var string
	 */
	private $client_secret;
	/**
	 * oAuth redirect URL
	 * @var string
	 */
	private $redirect_url;

	/**
	 * Constructor, sets up client id, client secret and token
	 * @param string $client_id - oAuth client ID
	 * @param string $client_secret - oAuth client secret
	 * @param string $token - authorization token
	 */
	public function __construct( $client_id, $client_secret, $token = null, $redirect_url ){
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->token = $token;

		$this->redirect_url = $redirect_url;
	}

	/**
	 * Retrieves unauthorized request token
	 */
	public function get_unauth_token(){
		// if there is a token, return it
		if( !empty( $this->token ) ){
			return $this->token;
		}

		// construct the endpoint
		$endpoint = self::ENDPOINT . self::UNAUTH_REQUEST;

		// make request
		$request = wp_remote_post( $endpoint, array(
			'body' => array(
				'grant_type' => 'client_credentials'
			),
			'method' => 'POST',
			'headers' => array(
				'authorization' => 'basic ' . base64_encode( $this->client_id . ':' . $this->client_secret )
			)
		));

		// if request failed for some reason, return the error
		if( is_wp_error( $request ) ){
			return $request;
		}

		// get request data
		$data = json_decode( wp_remote_retrieve_body( $request ), true );

		// if Vimeo API returned the access token, set the token and return its value
		if( isset( $data['access_token'] ) && !empty( $data['access_token'] ) ){
			$this->token = $data['access_token'];
			return $data['access_token'];
		}

		// if Vimeo returned error, return the error
		if( isset( $data['error'] ) ){
			$error_code = 'cvm_vimeo_api_err';
			$error_desc = '';
			if ( isset( $data['error'] ) && isset( $data['error_description'] ) ) {
				$error_code = $data['error'];
				$error_desc = $data['error_description'];
			} else if ( isset( $data['error'] ) ) {
				$error_desc = $data['error'];
			}

			return $this->_generate_error( $error_code, $error_desc );
		}
	}

	/**
	 * Returns the authorization redirect URL
	 * @return string - authorization URL
	 */
	public function get_auth_redirect( $state = null ){
		if( empty( $this->client_id ) || empty( $this->client_secret ) ){
			return $this->_generate_error( 'cvm_missing_oauth_credentials', __( 'Vimeo authorization credentials could not be found. Please enter them in plugin Settings page.' , 'codeflavors-vimeo-video-post-lite') );
		}

		$redirect = self::ENDPOINT . self::AUTH_REDIRECT;
		if( !$state ) {
			$state = time();
		}

		$args = array(
			'response_type' => 'code',
			'client_id' 	=> $this->client_id,
			'scope' 		=> 'public private',
			'state' 		=> $state,
			'redirect_uri' 	=> $this->redirect_url
		);
		return $redirect . '?' . http_build_query( $args );
	}

	/**
	 * Get authenticated request access token.
	 * @param string $code - code returned by Vimeo after user granted access
	 */
	public function get_auth_token( $code ){
		if( empty( $code ) ){
			return $this->_generate_error( 'cvm_no_access_code', __( 'Error, no access code provided.' , 'codeflavors-vimeo-video-post-lite') );
		}

		$endpoint = self::ENDPOINT . self::ACCESS_TOKEN;
		$request = wp_remote_post( $endpoint, array(
			'body' => array(
				'grant_type' 	=> 'authorization_code',
				'code' 		 	=> $code,
				'redirect_uri' 	=> $this->redirect_url
			),
			'method' => 'POST',
			'headers' => array(
				'authorization' => 'basic ' . base64_encode( $this->client_id . ':' . $this->client_secret )
			)
		));

		if( is_wp_error( $request ) ){
			return $request;
		}

		$data = json_decode( wp_remote_retrieve_body( $request ), true );

		// if Vimeo API returned the access token, set the token and return its value
		if( isset( $data['access_token'] ) && !empty( $data['access_token'] ) ){
			$this->token = $data['access_token'];
			return $data['access_token'];
		}

		// if Vimeo returned error, return the error
		if( isset( $data['error'] ) ){
			return $this->_generate_error( $data['error'], $data['error_description'] );
		}
	}

	/**
	 * Returns the value of the redirect URL set up by child class
	 * @return string
	 */
	public function get_redirect_url(){
		return $this->redirect_url;
	}

	protected function _generate_error( $code, $message, $data = false ){
		$error = new WP_Error( $code, $message, array( 'vimeo_api_error' => true, 'data' => $data ) );
		return $error;
	}
}

class ARVE_Vimeo extends ARVE_Vimeo_Base{

	const VERSION_STRING 	= 'application/vnd.vimeo.*+json; version=3.2';
	/**
	 * Store plugin settings for later use
	 * @var array
	 */
	private $settings;
	/**
	 * Store params
	 * @var array
	 */
	private $params;

	/**
	 * Constructor, fires up the parent by providing it with
	 * client ID, secret and token, if any
	 */
	public function __construct( $args = array() ) {
		// set plugin settings
		$this->settings = arve_get_options();
		// set the token
		$token = null;
		if( ! empty( $this->settings['oauth_secret'] ) ) {
			$token = $this->settings['oauth_secret'];
		} elseif ( ! empty( $option_token = get_option( 'arve_vimeo_oauth_token' ) ) ) {
			$token = $option_token;
		}
		// set up redirect URL
		$redirect_url = admin_url( 'options-general.php?page=advanced-responsive-video-embedder' );
		// start the parent
		parent::__construct(
			$this->settings['vimeo_client_identifier'],
			$this->settings['vimeo_client_secret'],
			$token,
			$redirect_url
		);

		if( !$args ){
			return;
		}

		$default = array(
			'feed' 		=> 'video', 	// feed type; can be album, channel, user or video
			'feed_id'	=> false, 		// vimeo ID for feed
			'feed_type' => 'videos', 	// vimeo method to query for ( videos, likes, all, appears )
			'sort'		=> 'new',		// video sorting
			'page'		=> 1,			// current page number
			'per_page'	=> 999,			// items per page
			// these shouldn't need to be changed
			'response'  => 'json'
		);

		$this->params = wp_parse_args( $args, $default );
	}

	/**
	 * Makes a request based on the params passed on constructor
	 */
	public function request_feed(){

		$map = array(
			'new' 		=> array( 'order_by' => 'date', 'order' => 'desc' ),
			'old' 		=> array( 'order_by' => 'date', 'order' => 'asc' ),
			'played' 	=> array( 'order_by' => 'plays', 'order' => 'desc' ),
			'likes' 	=> array( 'order_by' => 'likes', 'order' => 'desc' ),
			'comments' 	=> array( 'order_by' => 'comments', 'order' => 'desc' ),
			'relevant' 	=> array( 'order_by' => 'relevant', 'order' => 'desc' )
		);

		$args = array( 'order_by' => 'date', 'order' => 'desc' );
		if( array_key_exists( $this->params['sort'] , $map ) ){
			$args = $map[ $this->params['sort'] ];
		}

		$endpoint = $this->_get_endpoint( $this->params['feed'] , $this->params['feed_id'], $this->params['page'], $args );

		// send a debug message for any client listening to plugin messages
		#_cvm_debug_message( sprintf( __( 'Making remote request to: %s.', 'codeflavors-vimeo-video-post-lite' ), $endpoint ) );

		$request = wp_remote_get( $endpoint, array(
			'headers' => array(
				'authorization' => 'bearer ' . $this->token,
				'accept' => self::VERSION_STRING
			)
		));

		$rate_limit = wp_remote_retrieve_header( $request, 'x-ratelimit-limit' );
		if( $rate_limit ){
			// send a debug message for any client listening to plugin messages
			/*
			_cvm_debug_message(
				sprintf(
					__( 'Current rate limit: %s (%s remaining). Limit reset time set at %s.', 'codeflavors-vimeo-video-post-lite' ),
					$rate_limit,
					wp_remote_retrieve_header( $request , 'x-ratelimit-remaining' ),
					wp_remote_retrieve_header( $request , 'x-ratelimit-reset' )
				)
			);
			*/
		}

		// get request data
		$data = json_decode( wp_remote_retrieve_body( $request ), true );
		// if Vimeo returned error, return the error
		if( isset( $data['error'] ) ){
			return $this->_generate_error( 'vimeo_error', $data['error'] );
		}

		return $request;
	}

	/**
	 * Returns endpoint URL complete with params for a given existing action.
	 *
	 * @param string $action - can be: search, group, category, user, channel, video
	 * @param string $query - the query string, depending on the action, can be search query, group ID, category ID, user ID, channel ID, video ID
	 * @param string $page - the page number to retrieve
	 * @param array $args - additional arguments
	 * @return
	 */
	private function _get_endpoint( $action, $query, $page, $args = array() ){

		$defaults = array(
			'order_by' 	=> 'date',
			'order' 	=> 'desc'
		);
		extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

		// set "fields" GET parameter to increase requests limit per hour to 2500
		$json_fields = implode( ',', array(
			'uri',
			'name',
			'description',
			'pictures',
			'metadata',
			'stats',
			'tags',
			'privacy',
			'width',
			'height',
			'created_time',
			'release_date',
			'type',
			'user',
			'modified_time',
			'duration',
			'link'
		));

		$actions = array(
			'search' => array(
				'action' => 'videos',
				'params' => array(
					'page' 		=> $page,
					'per_page' 	=> 20,
					'query' 	=> $query,
					'sort'		=> $order_by,
					'direction' => $order
				)
			),
			'group' => array(
				'action' => sprintf( 'groups/%s/videos', $query ),
				'params' => array(
					'page' 		=> $page,
					'per_page' 	=> 20,
					'sort'		=> $order_by,
					'direction' => $order
				)
			),
			'category' => array(
				'action' => sprintf( 'categories/%s/videos', $query ),
				'params' => array(
					'page' 		=> $page,
					'per_page' 	=> 20,
					'sort'		=> $order_by,
					'direction' => $order
				)
			),
			'user' => array(
				'action' => sprintf( 'users/%s/videos', $query ),
				'params' => array(
					'page' 		=> $page,
					'per_page' 	=> 20,
					'sort'		=> $order_by,
					'direction' => $order
				)
			),
			'channel' => array(
				'action' => sprintf( 'channels/%s/videos', $query ),
				'params' => array(
					'page' 		=> $page,
					'per_page' 	=> 20,
					'sort'		=> $order_by,
					'direction' => $order
				)
			),
			'video' => array(
				'action' => sprintf( 'videos/%s', $query ),
				'params' => array()
			),
			'album' => array(
				'action' => sprintf( 'albums/%s/videos', $query ),
				'params' => array(
					'page' 		=> $page,
					'per_page' 	=> 20,
					'sort'		=> $order_by,
					'direction' => $order
				)
			)
		);

		if( array_key_exists( $action, $actions ) ){
			$vimeo_action 		= $actions[ $action ]['action'];
			$params 			= $actions[ $action ]['params'];
			// set the JSON fields to increase imports/hour rate limit
			$params['fields'] 	= $json_fields;
			$endpoint 			= parent::ENDPOINT . $vimeo_action . '/?' . http_build_query( $params );
			return $endpoint;
		}else{
			return new WP_Error( 'unknown_vimeo_api_action', sprintf( __( 'Action %s could not be found to query Vimeo.', 'codeflavors-vimeo-video-post-lite' ), $action ) );
		}

	}

}
