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

namespace Vvveb\System;

class Cron {
	private static $crons;

	public static function getInstance() {
		static $inst = null;

		if ($inst === null) {
			$inst   = new Cron();
		}

		return $inst;
	}

	public function __construct() {
	}

	public static function getCrons() {
		return self :: $crons;
	}

	public static function runAll() {
	}

	public static function run($cron) {
		return [];
	}

	public static function registerCron($cron, $class, $description) {
		self :: $crons[$cron] = $class;

		return true;
	}
}
