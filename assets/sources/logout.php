<?php

	if ( is_logged() ) 
	{
		if(isset($_GET['token']) && !empty($_GET['token'])) 
		{
			if(\Tumder\CSRF::get($_GET['token']))
			{
				/* Delete cookies */
				setcookie('tumd_ac_u', 0, time()-60, '/');
				setcookie('tumd_ac_p', 0, time()-60, '/');

				/* Remove token session */
				\Tumder\CSRF::delete($_GET['token']);

				/* Redirect to home */
				header("Location: ".siteUrl()."/home");
			} else 
			{
				header("Location: ".siteUrl()."/error");
			}
		} else 
		{
			header("Location: ".siteUrl()."/error");
		}
	} else 
	{
		header("Location: ".siteUrl()."/error");
	}