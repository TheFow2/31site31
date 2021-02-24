<?php
	if ( !is_logged() ) {
		$themeData['page_content'] = \Tumder\UI::view('welcome/register');
	} else { 
		$themeData['page_content'] = \Tumder\UI::view('welcome/error');
	}