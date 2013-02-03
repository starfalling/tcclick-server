<?php

class AdminRequiredFilter extends Filter{
	
	public function preFilter(){
		if(User::current() && User::current()->isAdmin()) return true;
		return false;
	}
}

