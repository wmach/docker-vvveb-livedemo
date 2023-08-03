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
Name: Pick up shipping method
Slug: pick-up-shipping
Category: shipping
Url: https://www.vvveb.com
Description: Adds Pick up shipping method on checkout page
Author: givanz
Version: 0.1
Thumb: shipping.svg
Author url: http://www.vvveb.com
Settings: /admin/?module=plugins/pick-up-shipping/settings
*/

use Vvveb\Plugins\PickUpShipping\Shipping;
use Vvveb\System\Shipping as ShippingApi;

if (! defined('V_VERSION')) {
	die('Invalid request!');
}

class PickUpShipping {
	function admin() {
	}

	function app() {
	}

	function __construct() {
		if (Vvveb\isEditor()) {
			return;
		}

		$shipping = ShippingApi::getInstance();
		$shipping->registerMethod('pick-up', Shipping::class);

		if (APP == 'admin') {
			$this->admin();
		} else {
			if (APP == 'app') {
				$this->app();
			}
		}
	}
}

$PickUpShipping = new PickUpShipping();
