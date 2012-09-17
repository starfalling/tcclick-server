<?php

class AdminRequiredFilter extends Filter{
	
	public function preFilter(){
		if(User::current() && User::current()->username=="admin") return true;
		return false;
	}
}

