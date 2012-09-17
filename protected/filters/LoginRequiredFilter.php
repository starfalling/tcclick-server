<?php

class LoginRequiredFilter extends Filter{
	
	public function preFilter(){
		if(User::current()) return true;
		header("Location: ".TCClick::app()->root_url.'login');
		exit;
	}
}

