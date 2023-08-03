import(crud.tpl, {"type":"post"})

/* template */
[data-v-post] select[data-v-templates]|before = <?php $optgroup = '';?>
@templates-select-option = [data-v-post] select[data-v-templates] [data-v-option]

@templates-select-option|deleteAllButFirstChild

[data-v-post] select[data-v-templates]|before = 
<?php
	 //set select name
	 $selected = '';	
	 //$name = '@@__data-v-post-(*)__@@';
	 $name = 'templates';
	 if (isset($_POST[$name])) {
		 $selected = $_POST[$name];
	 } else
	 if (isset($this->post[$name])) {
		$selected = $this->post[$name];
	 }
?>

@templates-select-option|before = <?php
	if (isset($this->$name)) {
	$options = 	$this->$name;
	foreach($options as $key => $option){?>
	
		@templates-select-option|value = $option
		@templates-select-option = <?php echo ucfirst($option);?>

@templates-select-option|after = <?php
}}?>

@templates-select-option|addNewAttribute = <?php if ($option == $selected) echo 'selected';?>


@templates-select-option|before = <?php
if ($optgroup != $option['folder']) {
	$optgroup = $option['folder'];
	echo '<optgroup label="' . ucfirst($optgroup) . '">';
}
?>

@templates-select-option|after = <?php
if ($optgroup != $option['folder']) {
	$optgroup = $option['folder'];
	echo "/<optgroup>";
}
?>

@templates-select-option|value = <?php echo $option['file'];?>
@templates-select-option|addNewAttribute = <?php if (isset($this->post['template']) && ($option['file'] == $this->post['template'])) echo 'selected';?>
@templates-select-option = <?php echo ucfirst($option['title']);?>





/* language tabs */
[data-v-languages]|before = <?php $_lang_instance = '@@__data-v-languages__@@';$_i = 0;?>
[data-v-languages] [data-v-language]|deleteAllButFirstChild
//[data-v-languages] [data-v-language]|addClass = <?php if ($_i == 0) echo 'active';?>

[data-v-languages] [data-v-language]|before = <?php

foreach ($this->languagesList as $language) {
?>
	[data-v-languages] [data-v-language-id]|id = <?php echo 'lang-' . $language['code'] . '-' . $_lang_instance;?>
	[data-v-languages]  [data-v-language-id]|addClass = <?php if ($_i == 0) echo 'show active';?>

	[data-v-languages] [data-v-language] [data-v-language-name] = $language['name']
	[data-v-languages] [data-v-language] [data-v-language-img]|title = $language['name']
	[data-v-languages] [data-v-language] [data-v-language-img]|src = <?php echo 'language/' . $language['code'] . '/' . $language['code'] . '.png';?>
	[data-v-languages] [data-v-language] [data-v-language-link]|href = <?php echo '#lang-' . $language['code'] . '-' . $_lang_instance?>
	[data-v-languages] [data-v-language] [data-v-language-link]|addClass = <?php if ($_i == 0) echo 'active';?>

[data-v-languages] [data-v-language]|after = <?php 
$_i++;
}
?>


[data-v-post] input[data-v-post-content-*]|name = <?php echo 'post_content[' . $language['language_id'] . '][@@__data-v-post-content-(*)__@@]';?>
[data-v-post] textarea[data-v-post-content-*]|name = <?php echo 'post_content[' . $language['language_id'] . '][@@__data-v-post-content-(*)__@@]';?>

[data-v-post] input[data-v-post-content-*]|value = <?php
	$desc = '@@__data-v-post-content-(*)__@@';
	if (isset($this->post['post_content'][$language['language_id']][$desc])) 
		echo $this->post['post_content'][$language['language_id']][$desc];
?>

[data-v-post] textarea[data-v-post-content-*] = <?php
	$desc = '@@__data-v-post-content-(*)__@@';
	if (isset($this->post['post_content'][$language['language_id']][$desc])) 
		echo $this->post['post_content'][$language['language_id']][$desc];
?>


[data-v-post] input[data-v-post-content-language_id]|value = <?php echo $language['language_id']; ?>


[data-v-post] [data-v-image]|data-v-image = $this->post['image_url']
[data-v-post] input[data-v-image]|value = $this->post['image']
[data-v-post] img[data-v-image]|src = <?php echo $this->post['image_url'] ? $this->post['image_url'] : 'img/placeholder.svg';?>

[data-v-post] [data-v-url]|href = $this->post['url']
[data-v-post] [data-v-url] = $this->post['url']

[data-v-post] [data-v-design_url]|href = $this->post['design_url']

import(content/post_taxonomy.tpl)


[data-v-template_missing] = <?php echo $this->template_missing;?>
[data-v-type_name_plural] = $this->type_name_plural
[data-v-type-name] = $this->type_name
[data-v-type] = $this->type
[data-v-posts-list-url]|href = $this->posts_list_url