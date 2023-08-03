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

$TABLE  = $_GET['edit'];
$fields = fields($TABLE);
$where  = (isset($_GET['select']) ? ($_POST['check'] && count($_POST['check']) == 1 ? where_check($_POST['check'][0], $fields) : '') : where($_GET, $fields));
$update = (isset($_GET['select']) ? $_POST['edit'] : $where);

foreach ($fields as $name => $field) {
	if (! isset($field['privileges'][$update ? 'update' : 'insert']) || $adminer->fieldName($field) == '' || $field['generated']) {
		unset($fields[$name]);
	}
}

if ($_POST && ! $error && ! isset($_GET['select'])) {
	$location = $_POST['referer'];

	if ($_POST['insert']) { // continue edit or insert
		$location = ($update ? null : $_SERVER['REQUEST_URI']);
	} elseif (! preg_match('~^.+&select=.+$~', $location)) {
		$location = ME . 'select=' . urlencode($TABLE);
	}

	$indexes      = indexes($TABLE);
	$unique_array = unique_array($_GET['where'], $indexes);
	$query_where  = "\nWHERE $where";

	if (isset($_POST['delete'])) {
		queries_redirect(
			$location,
			lang('Item has been deleted.'),
			$driver->delete($TABLE, $query_where, ! $unique_array)
		);
	} else {
		$set = [];

		foreach ($fields as $name => $field) {
			$val = process_input($field);

			if ($val !== false && $val !== null) {
				$set[idf_escape($name)] = $val;
			}
		}

		if ($update) {
			if (! $set) {
				redirect($location);
			}
			queries_redirect(
				$location,
				lang('Item has been updated.'),
				$driver->update($TABLE, $set, $query_where, ! $unique_array)
			);

			if (is_ajax()) {
				page_headers();
				page_messages($error);

				exit;
			}
		} else {
			$result  = $driver->insert($TABLE, $set);
			$last_id = ($result ? last_id() : 0);
			queries_redirect($location, lang('Item%s has been inserted.', ($last_id ? " $last_id" : '')), $result); //! link
		}
	}
}

$row = null;

if ($_POST['save']) {
	$row = (array) $_POST['fields'];
} elseif ($where) {
	$select = [];

	foreach ($fields as $name => $field) {
		if (isset($field['privileges']['select'])) {
			$as = convert_field($field);

			if ($_POST['clone'] && $field['auto_increment']) {
				$as = "''";
			}

			if ($jush == 'sql' && preg_match('~enum|set~', $field['type'])) {
				$as = '1*' . idf_escape($name);
			}
			$select[] = ($as ? "$as AS " : '') . idf_escape($name);
		}
	}
	$row = [];

	if (! support('table')) {
		$select = ['*'];
	}

	if ($select) {
		$result = $driver->select($TABLE, $select, [$where], $select, [], (isset($_GET['select']) ? 2 : 1));

		if (! $result) {
			$error = error();
		} else {
			$row = $result->fetch_assoc();

			if (! $row) { // MySQLi returns null
				$row = false;
			}
		}

		if (isset($_GET['select']) && (! $row || $result->fetch_assoc())) { // $result->num_rows != 1 isn't available in all drivers
			$row = null;
		}
	}
}

if (! support('table') && ! $fields) {
	if (! $where) { // insert
		$result = $driver->select($TABLE, ['*'], $where, ['*']);
		$row    = ($result ? $result->fetch_assoc() : false);

		if (! $row) {
			$row = [$driver->primary => ''];
		}
	}

	if ($row) {
		foreach ($row as $key => $val) {
			if (! $where) {
				$row[$key] = null;
			}
			$fields[$key] = ['field' => $key, 'null' => ($key != $driver->primary), 'auto_increment' => ($key == $driver->primary)];
		}
	}
}

edit_form($TABLE, $fields, $row, $update);
