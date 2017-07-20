<?php

// app/View/Helper/WrapHelper.php
App::uses('AppHelper', 'View/Helper');

/*
 * A helper used specifically for this app
 */
class LocalHelper extends AppHelper 
{
	public function state($toggle = 0)
	{
	/*
	 * Prints a Yes or a not based on a boolean value
	 */
		if(!$toggle) return __('Closed');
		return __('Open');
	}
	
	public function emailOptions2($option = false)
	{
		$options = Configure::read('Options.review_state_email');
		if($option === false)
		{
			return $options;
		}
		if(!isset($options[$option]))
		{
			return false;
		}
		return $options[$option];
	}
	
	public function emailTimes($selected = false)
	{
		if($selected === null) return ' '; // not even midnight is selected
		$review_times = range(0, 23);
		$formated_times = array();
		foreach($review_times as $hour)
		{
			$nice = $hour. ' am';
			if($hour > 12)
			{
				$nice = ($hour - 12). ' pm';
			}
			if($hour == 12) $nice = 'Noon';
			if($hour == 0) $nice = 'Midnight';
			$formated_times[$hour] = $nice;
 		}
 		if($selected) return $formated_times[$selected];
 		return $formated_times;
	}
}