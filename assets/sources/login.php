<?php
	if ( !is_logged() ) {
		$themeData['is_redirect_login'] = (isset($_GET['redirect_url']) && !empty($_GET['redirect_url'])) ? '<input name="redirect_url" value="'.$_GET['redirect_url'].'" type="hidden">' : '';

		$themeData['page_content'] = \Tumder\UI::view('welcome/login');
	}
	else {
		$themeData['page_content'] = \Tumder\UI::view('welcome/error');
	}