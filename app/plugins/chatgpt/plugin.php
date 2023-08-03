<?php

/**
 * Vvveb
 *
 * Copyright (C) 2022  Ziadin Givan
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

/*
Name: ChatGPT
Slug: chatgpt
Category: tools
Url: https://www.vvveb.com
Description: Add ChatGPT prompt toolbar button to content editor on post and product edit page and chatgpt prompt component in site editor.
Author: givanz
Version: 0.1
Thumb: chatgpt.svg
Author url: http://www.vvveb.com
Settings: /admin/?module=plugins/chatgpt/settings
*/

use \Vvveb\System\Event as Event;

if (! defined('V_VERSION')) {
	die('Invalid request!');
}

class ChatgptPlugin {
	function addTinyMce() {
		Event::on('Vvveb\System\Core\View', 'compile', __CLASS__, function ($template, $htmlFile, $tplFile, $vTpl, $view) {
			//insert js on post and product page
			if ($template == 'content/post.html' || $template == 'product/product.html') {
				$defaults = [
					'key'             => '',
					'model'           => 'text-davinci-003',
					'temperature'     => 0,
					'max_tokens'      => 70,
				];

				$options = Vvveb\get_setting('chatgpt', ['key', 'model', 'temperature', 'max_tokens']);
				$options = $options + $defaults;
				$json = json_encode($options);
				$script = "'<script>chatgptOptions = $json;</script><script src=\"../../../plugins/chatgpt/chatgpt-tinymce.js\"></script>'";

				//insert script
				//$vTpl->loadTemplateFile(__DIR__ . '/app/template/common.pst');
				$vTpl->addCommand('body|append', $script);
			}
		});
	}

	function admin() {
		$this->addTinyMce();
	}

	function app() {
	}

	function __construct() {
		if (APP == 'admin') {
			$this->admin();
		} else {
			if (APP == 'app') {
				$this->app();
			}
		}
	}
}

$chatgptPlugin = new ChatgptPlugin();
