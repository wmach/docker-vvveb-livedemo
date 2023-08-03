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

namespace Vvveb\Controller\Content;

use function Vvveb\__;
use Vvveb\Controller\Base;
use function Vvveb\humanReadable;
use function Vvveb\model;
use function Vvveb\sanitizeHTML;
use function Vvveb\slugify;
use Vvveb\Sql\categorySQL;
use Vvveb\System\CacheManager;
use Vvveb\System\Core\View;
use Vvveb\System\Images;
use Vvveb\System\Sites;
use Vvveb\System\Validator;

class Edit extends Base {
	protected $type = 'post';

	protected $object = 'post';

	use TaxonomiesTrait, AutocompleteTrait;

	function getThemeFolder() {
		return DIR_THEMES . DS . Sites::getTheme() ?? 'default';
	}

	function index() {
		$view = $this->view;

		$admin_path          = \Vvveb\adminPath();
		$postOptions         = [];
		$post                = [];
		$post_id             = $this->request->get[$this->object . '_id'] ?? $this->request->post[$this->object . '_id'] ?? false;

		$controllerPath        = $admin_path . 'index.php?module=media/media';
		$view->scanUrl         = "$controllerPath&action=scan";
		$view->uploadUrl       = "$controllerPath&action=upload";
		$theme                 = Sites::getTheme() ?? 'default';
		$view->themeCss        = PUBLIC_PATH . "themes/$theme/css/admin-post-editor.css";
		//$view->themeCss        = PUBLIC_PATH . "themes/$theme/css/style.css";

		if ($post_id) {
			$postOptions[$this->object . '_id'] = (int)$post_id;
		} else {
			if (isset($this->request->get['slug'])) {
				$postOptions['slug'] = $this->request->get['slug'];
			}
		}

		if (isset($this->request->get['type'])) {
			$this->type = $this->request->get['type'];
		}

		if ($postOptions) {
			$posts = model($this->object);

			$postOptions['type'] = $this->type;
			$options             = $postOptions + $this->global;
			//get all languages
			unset($options['language_id']);
			$post                = $posts->get($options);

			if (! $post) {
				$message = sprintf(__('%s not found!'), humanReadable(__($this->type)));
				$this->notFound(false, ['message' => $message, 'title' => $message]);
			}

			//featured image
			if (isset($post['image'])) {
				$post['image_url'] = Images::image($post['image'], $this->object);
			}

			//gallery
			if (isset($post[$this->object . '_image'])) {
				$post['images'] = Images::images($post[$this->object . '_image'], $this->object);
			}

			//$productImages = $posts->getImages($postOptions);
		} else {
			$post['image_url'] = Images::image('',$this->object);
		}

		if (isset($post['date_modified'])) {
			$post['date_modified'] = str_replace(' ', 'T', $post['date_modified']);
		} else {
			$post['date_modified'] = date("Y-m-d\TH:i:s", isset($post['date_modified']) && $post['date_modified'] ? strtotime($post['date_modified']) : time());
		}

		if (isset($post[$this->object . '_content'][1]['slug'])) {
			//$post['url'] = \Vvveb\url("content/{$this->type}/index", ['slug'=> $post[$this->object . '_content'][1]['slug']]);
			$post['url'] = \Vvveb\url("content/{$post['type']}/index", $post);
		}

		$this->type = $post['type'] ?? $this->type;
		$type_name  = humanReadable(__($this->type));

		$defaultTemplate = \Vvveb\getCurrentTemplate();
		$template        = isset($post['template']) && $post['template'] ? $post['template'] : $defaultTemplate;
		$themeFolder     = $this->getThemeFolder();

		if (isset($post['url'])) {
			$design_url         = $admin_path . \Vvveb\url(['module' => 'editor/editor', 'url' => $post['url'], 'template' => $template], false, false);
			$post['design_url'] = $design_url;
		}

		if (! file_exists($themeFolder . DS . $template)) {
			if ($template == $defaultTemplate) {
				$view->template_missing = sprintf(__('Template missing, choose existing template or %screate global template%s for %s.'), '<a href="' . $design_url . '" target="_blank">', '</a>', $type_name);
			} else {
				$view->template_missing = sprintf(__('Template missing, %screate template%s for this  %s.'), '<a href="' . $design_url . '" target="_blank">', '</a>', $type_name);
			}
		}

		if ($this->type != 'page') {
			$view->taxonomies = $this->taxonomies($post[$this->object . '_id'] ?? false);
		}

		$object                    = $this->object;
		$view->$object             = $post;
		$view->status              = ['publish' => 'Publish', 'draft' => 'Draft', 'pending' => 'Pending', 'private' => 'Private', 'password' => 'Password'];
		$view->templates           = \Vvveb\getTemplateList(false, ['email']);
		$validator                 = new Validator([$this->object]);
		$view->validatorJson       = $validator->getJSON();
		$view->type                = __($this->type);
		$view->type_name           = $type_name;
		$view->posts_list_url      =  \Vvveb\url(['module' => 'content/posts', 'type' => $this->type]);
	}

	private function addCategory($taxonomy_id, $name) {
		$categories = new CategorySQL();
		$cat        = $categories->addCategory([
			'taxonomy_item' => $this->global + [
				'taxonomy_id' => $taxonomy_id,
			],
			'taxonomy_item_content' => $this->global + ['slug'=> slugify($name), 'name' => $name, 'content' => ''],
		] + $this->global);

		return $category_id = $cat['taxonomy_item'];
	}

	function save() {
		$validator                     = new Validator([$this->object]);
		$view                          = view :: getInstance();
		$post_id                       = $this->request->get[$this->object . '_id'] ?? $this->request->post[$this->object . '_id'] ?? false;
		$this->{$this->object . '_id'} = $post_id;
		$publicPath                    = \Vvveb\publicMediaUrlPath();

		if (isset($this->request->get['type'])) {
			$this->type          = $this->request->get['type'];
		}

		if (($errors = $validator->validate($this->request->post)) === true) {
			$posts = model($this->object);

			$post = [];

			$post = $this->request->post;

			foreach ($post[$this->object . '_content'] as &$content) {
				$content['name']    = strip_tags($content['name']);
				$content['content'] = sanitizeHTML($content['content']);

				if (isset($content['excerpt'])) {
					$content['excerpt'] = sanitizeHTML($content['excerpt']);
				}
			}

			if (isset($post['date_modified'])) {
				$post['date_modified'] = str_replace(' ', 'T', $post['date_modified']);
			} else {
				$post['date_modified'] = date("Y-m-d\TH:i:s", time());
			}

			//process tags
			if (isset($post['tag'])) {
				foreach ($post['tag'] as $listId => $tags) {
					foreach ($tags as $tagId => $tag) {
						//existing tag add to post taxonomy_item list
						if (is_numeric($tagId)) {
							//$post['taxonomy_item'][] = $tagId;
						} else {
							//add new taxonomy_item
							$tagId = $this->addCategory($listId, $tag);
						}
						$post['taxonomy_item'][] = $tagId;
					}
				}
			}

			$post = $post + $this->global;

			if (isset($post[$this->object . '_image'])) {
				$productImage = $this->object . 'Image';

				foreach ($post[$this->object . '_image'] as &$image) {
					$image = str_replace($publicPath . 'media/','', $image);
				}

				$posts->$productImage([$this->object . '_id' => $post_id, $this->object . '_image' => $post[$this->object . '_image']]);
			}

			if ($post_id) {
				$post[$this->object . '_id']                     = (int)$post_id;
				$result                                          = $posts->edit([$this->object => $post, $this->object . '_id' => $post_id] + $this->global);

				if ($result >= 0) {
					$this->view->success[] = ucfirst($this->type) . ' ' . __('saved') . '!';
					//CacheManager::delete($this->object);
					CacheManager::delete();
				} else {
					$this->view->errors = [$posts->error];
				}
			} else {
				$post['type']                      = $this->type;
				$post['date_added']                = $post['date_modified'];
				$return                            = $posts->add([$this->object => $post, $this->object . '_id' => $post_id] + $this->global);
				$id                                = $return[$this->object] ?? false;

				if (! $id) {
					$view->errors = [$posts->error];
				} else {
					CacheManager::delete($this->object);
					$message         = ucfirst($this->type) . ' ' . __('saved') . '!';
					$view->success[] = $message;
					$this->redirect(['module'=>$this->module, $this->object . '_id' => $id, 'type' => $this->type, 'success' => $message]);
				}
			}
		} else {
			$view->errors = $errors;
		}
	}
}
