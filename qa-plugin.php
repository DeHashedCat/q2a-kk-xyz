<?php
/*
	Question2Answer (c) Gideon Greenspan

	https://www.question2answer.org/

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: https://www.question2answer.org/license.php
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

qa_register_plugin_module('captcha', 'KK_XYZ_Captcha.php', 'KK_XYZ_Captcha', 'KK_XYZ Captcha');

qa_register_plugin_module('page', 'KK_XYZ_Page.php', 'KK_XYZ_Page', 'KK_XYZ Page');
