<?php

class BaseController extends Controller {


	public function __construct()
	{
		// CSRF Protection
		$this->beforeFilter('csrf', array('on' => 'post'));

		// XSS Protection
		$this->beforeFilter('xss', array('on' => 'post'));
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}