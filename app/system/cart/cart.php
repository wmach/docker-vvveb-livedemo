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

namespace Vvveb\System\Cart;

use function \Vvveb\url;
use Vvveb\System\Images;
use Vvveb\System\Session;

class Cart {
	protected $cart = [];

	private $session;

	protected $options;

	protected $products = [];

	protected $taxes = [];

	protected $totals = [];

	protected $total = 0;

	protected $total_tax = 0;

	protected $total_items = 0;

	public static function getInstance($options = []) {
		static $inst = null;

		if ($inst === null) {
			$inst = new Cart($options);
		}

		return $inst;
	}

	private function __construct($options = []) {
		$this->session = Session :: getInstance();

		$this->options = $options;
		$this->read();

		if (! isset($this->total_items)) {
			$this->total_items = 0;
		}

		if (! isset($this->total)) {
			$this->total = 0;
		}

		if (! isset($this->products)) {
			$this->products = [];
		}
	}

	private function updateCart() {
		$this->total       = 0;
		$this->total_items = 0;

		$results = ['products' => [], 'count' => 0];

		if (! empty($this->products)) {
			$productIds = array_keys($this->products);

			$options =  [
				'product_id'            => $productIds,
				'start'                 => 0,
				'include_image_gallery' => true,
			] + $this->options;

			$products = new \Vvveb\Sql\ProductSQL();
			$results  = $products->getAll(
				$options
			);
		}

		$tax      = Tax::getInstance();
		$currency = Currency::getInstance();

		$products       = $this->products;
		$this->products = [];

		foreach ($results['products'] as $id => &$product) {
			$productId = $product['product_id'];

			if (isset($product['images'])) {
				$product['images'] = json_decode($product['images'], true);

				foreach ($product['images'] as &$image) {
					$image['image'] = Images::image($image['image'], 'product');
				}
			}

			if (isset($product['image'])) {
				$product['image'] = Images::image($product['image'], 'product');
			}

			$product['url']             = htmlentities(url('product/product/index', $product));
			$product['remove-url']      = htmlentities(url('cart/cart/remove', $product));

			$product['quantity']        = $products[$productId]['quantity'] ?? 1;
			$product['total']           = (int)$product['price'] * $product['quantity'];
			$product['total_formatted'] = '$' . $product['total'];

			$taxValue             = $tax->addTaxes($product['price'], $product['tax_class_id'], true);
			$product['price_tax'] = ($taxValue + $product['price']);
			$product['tax']       = $product['price_tax'] * $product['quantity'];
			$product['total_tax'] = $product['tax'];

			$product['price_tax_formatted'] = $currency->format($product['price_tax'], '$');
			$product['price_formatted']     = $currency->format($product['price'], '$');
			$product['total_formatted']     = $currency->format($product['total'], '$');
			$product['total_tax_formatted'] = $currency->format($product['total_tax'], '$');

			$this->total += $product['total'];
			$this->total_tax += $product['total_tax'];
			$this->total_items += $product['quantity'];

			if (isset($this->products[$productId])) {
				$product = array_merge($this->products[$productId], $product);
			}

			$this->products[$productId] = $product;
		}

		$this->addTotal('sub_total', 'Sub-total', $this->total);
		//write is done by addTotal
		//$this->write();

		return $results;
	}

	function add($productId, $quantity = 1, $option = [], $recurringId = 0, $subscriptionPlanId = 0) {
		if (! $productId) {
			return false;
		}

		if (isset($this->products[$productId])) {
			$this->products[$productId]['quantity'] += $quantity;
		} else {
			$this->products[$productId] =
			[
				'quantity'     => $quantity,
				'option'       => $option,
				'recurring_id' => $recurringId,
			];
		}

		return $this->updateCart();
	}

	function update($productId, $quantity = 1) {
		if (isset($this->products[$productId])) {
			$this->products[$productId]['quantity'] = max(1, $quantity);

			return $this->updateCart();
		}
	}

	function getAll() {
		//$this->updateCart();
		//error_log(print_r($this->products, 1));
		return $this->products ?? [];
	}

	function getNoProducts() {
		//error_log(print_r($this->products, 1));
		return count($this->products ?? []);
	}

	function remove($productId) {
		unset($this->products[$productId]);

		return $this->updateCart();
	}

	public function getSubscription() {
		$product_data = [];

		foreach ($this->products as $value) {
			if ($value['subscription']) {
				$product_data[] = $value;
			}
		}

		return $product_data;
	}

	public function getWeight() {
		$weight = 0;

		foreach ($this->products as $product) {
			if ($product['shipping']) {
				$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}

		return $weight;
	}

	public function getSubTotal() {
		$total = 0;

		foreach ($this->products as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	public function getGrandTotal() {
		$sum = 0;

		if ($this->totals) {
			foreach ($this->totals as $total) {
				$sum += $total['value'] ?? 0;
			}
		}

		return $sum;
	}

	public function getGrandTotalFormatted() {
		$sum = $this->getGrandTotal();

		return '$' . $sum;
	}

	public function addTax($name, $price, $taxClassId) {
		if ($price && $taxClassId) {
			$this->taxes[$name] = ['price' => $price, 'tax_class_id' => $taxClassId];
		}
	}

	public function removeTax($name) {
		unset($this->taxes[$name]);
	}

	public function getTaxes() {
		$tax      = Tax::getInstance();
		$taxes    = [];
		$products = $this->products + $this->taxes;

		foreach ($products as $product) {
			if ($product['tax_class_id']) {
				$tax_rates = $tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (! isset($taxes[$tax_rate['tax_rate_id']])) {
						$taxes[$tax_rate['tax_rate_id']]          = $tax_rate;
						$taxes[$tax_rate['tax_rate_id']]['value'] = 0;
					}

					$taxes[$tax_rate['tax_rate_id']]['value'] += ($tax_rate['amount'] * ($product['quantity'] ?? 1));
				}
			}
		}

		return $taxes;
	}

	function addTaxTotal() {
		$taxes = $this->getTaxes();

		foreach ($taxes as $tax) {
			$this->addTotal('tax.' . $tax['tax_rate_id'], $tax['name'], $tax['value']);
		}
	}

	function addTotal($key, $title, $value, $text = '') {
		$data = ['key' => $key, 'title' => $title, 'value' => $value, 'value_formatted' => '$' . $value, 'text' => $text];

		$this->totals[$key] = $data;
		$this->write();
	}

	function removeTotal($key) {
		unset($this->totals[$key]);
	}

	function getTotals() {
		//include taxes
		$this->addTaxTotal();

		return $this->totals;
	}

	/*
		public function getTotal() {
			$total = 0;

			foreach ($this->products as $product) {
				$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
			}

			return $total;
		}
	*/
	public function countProducts() {
		$product_total = 0;

		$products = $this->products;

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts() {
		return count($this->products);
	}

	public function hasSubscription() {
		return count($this->getSubscription());
	}

	public function hasStock() {
		foreach ($this->products as $product) {
			if (! $product['stock']) {
				return false;
			}
		}

		return true;
	}

	public function hasShipping() {
		foreach ($this->products as $product) {
			if ($product['shipping']) {
				return true;
			}
		}

		return false;
	}

	public function hasDownload() {
		foreach ($this->products as $product) {
			if ($product['download']) {
				return true;
			}
		}

		return false;
	}

	private function read() {
		$data = $this->session->get('cart');

		if (is_array($data)) {
			foreach ($data as $property => $value) {
				$this->$property = $value;
			}
		}
	}

	private function write() {
		//$data = get_object_vars($this);
		foreach (['products', 'taxes', 'totals',  'total_items'] as $property) {
			$data[$property] = $this->$property;
		}

		$this->session->set('cart', $data);
	}

	public function empty() {
		$this->session->set('cart', []);
	}
}
