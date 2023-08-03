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

/**
 * @package Contact form plugin
 * @version 0.1
 */
/*
Name: Contact form
Slug: contact-form
Category: email
Url: http://www.vvveb.com
Description: Create contact forms that sends email or saves data in the database
Thumb: contact-form.svg
Author: givanz
Version: 0.1
Author url: http://www.vvveb.com
*/

use function Vvveb\__;
use function Vvveb\array_insert_array_after;
use Vvveb\Plugins\ContactForm\Install;
use Vvveb\System\Core\View;
use Vvveb\System\Event;

if (! defined('V_VERSION')) {
	die('Invalid request!');
}

#[\AllowDynamicProperties]
class ContactFormPlugin {
	function admin() {
		//add admin menu item
		$admin_path = \Vvveb\adminPath();
		Event::on('Vvveb\Controller\Base', 'init-menu', __CLASS__, function ($menu) use ($admin_path) {
			$menu['plugins']['items']['contact-form'] = [
				'name'     => __('Contact form'),
				'url'      => $admin_path . '?module=plugins/contact-form/settings',
				'icon-img' => PUBLIC_PATH . 'plugins/contact-form/contact-form.svg',
				'module'   => 'plugins/contact-form/settings',
				'action'   => 'index',
			];

			$menu = array_insert_array_after('users', $menu, ['messages' => [
				'name'     => __('Messages'),
				'url'      => $admin_path . '?module=plugins/contact-form/messages',
				//'icon-img' => PUBLIC_PATH . 'plugins/contact-form/contact-form.svg',
				'icon'   => 'ion-ios-email-outline',
				'module' => 'plugins/contact-form/messages',
				'action' => 'index',
			]]);

			return [$menu];
		}, 20);

		Event::on('Vvveb\Controller\Editor\Editor', 'loadThemeAssets', __CLASS__, function ($inputs, $components, $blocks, $sections) {
			$components['contact-form'] = '../../../plugins/contact-form/editor/components.js';

			return [$inputs, $components, $blocks, $sections];
		});

		Event::on('Vvveb\System\Extensions\Plugins', 'setup', __CLASS__, function ($pluginName, $siteId) {
			if ($pluginName == 'contact-form') {
				$this->install();
			}

			return [$pluginName, $siteId];
		});
	}

	function install() {
		$install = new Install();
		$install->run();
	}

	function app() {
		$this->view = View::getInstance();
		$template   = $this->view->getTemplateEngineInstance();
		$template->loadTemplateFile(__DIR__ . '/app/template/contact-form.tpl');
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

$contactFormPlugin = new ContactFormPlugin();
