import(crud.tpl, {"type":"site"})

[data-v-theme-list] option|deleteAllButFirst

[data-v-theme-list] option|before = <?php
	foreach ($this->themeList as $code => $theme) {
?>

	[data-v-theme-list] option|value = $code
	[data-v-theme-list] option|addNewAttribute = <?php if (isset($this->site['theme']) && ($code == $this->site['theme'])) echo 'selected';?>
	[data-v-theme-list] option = <?php 
		echo $theme['name'];
	?>

[data-v-theme-list] option|after = <?php 
} ?>


@templates-select-option = [data-v-template-list] [data-v-option]

@templates-select-option|deleteAllButFirstChild

@templates-select-option|before = <?php
	$options = 	$this->templateList;
	$optgroup = false;
	foreach($options as $key => $option){?>
	
		@templates-select-option|value = $option
		@templates-select-option = <?php echo ucfirst($option);?>

@templates-select-option|after = <?php
}?>

@templates-select-option|addNewAttribute = <?php if (isset($selected) && $option == $selected) echo 'selected';?>


@templates-select-option|before = <?php
if (($optgroup != $option['folder'])) {
	$optgroup = $option['folder'];
	echo '<optgroup label="' . ucfirst($optgroup) . '">';
}
?>

@templates-select-option|after = <?php
if (($optgroup != $option['folder'])) {
	$optgroup = $option['folder'];
	echo "/<optgroup>";
}
?>

@templates-select-option|value = <?php echo $option['file'];?>
@templates-select-option|addNewAttribute = <?php if (isset($this->site['template']) && $option['file']== $this->site['template']) echo 'selected';?>
@templates-select-option = <?php echo ucfirst($option['title']);?>


input[data-v-site-*]|value = <?php
	$name = '@@__data-v-site-(*)__@@';
	 if (isset($_POST['site'][$name])) 
		echo $_POST['site'][$name]; 
	 else if (isset($this->site[$name])) 
		echo $this->site[$name];
?>		

[data-v-site-*]|innerText = $this->site['@@__data-v-site-(*)__@@']
[data-v-site-*]|title = $this->site['@@__data-v-site-(*)__@@']
a[data-v-site-*]|title|href = $this->site['@@__data-v-site-(*)__@@']


input[data-v-setting]|value = <?php 
	$_setting = '@@__data-v-setting__@@';
	echo $_POST['settings'][$_setting] ?? $this->setting[$_setting] ?? '';
	//name="settings[setting-name] > get only setting-name
	//$_setting = '@@__name:\[(.*)\]__@@';
?>

img[data-v-setting]|src = <?php 
	$_setting = '@@__data-v-setting__@@';
	echo $this->setting[$_setting] ?? '';
	//name="settings[setting-name] > get only setting-name
	//$_setting = '@@__name:\[(.*)\]__@@';
?>

[data-v-date_format]|deleteAllButFirst

[data-v-date_format]|before = <?php
	$custom = true;
	foreach($this->date_format as $format => $text) {
		$checked = ($this->site['date_format'] ?? 'F j, Y') == $format;
		if ($checked) $custom = false;
	?>
	
	[data-v-date_format-text] = $text
	[data-v-date_format-value] = $format
	input[data-v-date_format-value]|value = $format
	input[data-v-date_format-value]|addNewAttribute = <?php if ($checked) echo 'checked';?>
	#date_format_custom|addNewAttribute = <?php if ($custom) echo 'checked';?>
	
[data-v-date_format]|after = <?php
	}
?>	

[data-v-time_format]|deleteAllButFirst

[data-v-time_format]|before = <?php
	$custom = true;
	foreach($this->time_format as $format => $text) {
		$checked = ($this->site['time_format'] ?? 'H:i') == $format;
		if ($checked) $custom = false;
	?>
	
	[data-v-time_format-text] = $text
	[data-v-time_format-value] = $format
	input[data-v-time_format-value]|value = $format
	input[data-v-time_format-value]|addNewAttribute = <?php if ($checked) echo 'checked';?>
	#time_format_custom|addNewAttribute = <?php if ($custom) echo 'checked';?>
	
[data-v-time_format]|after = <?php
	}
?>	
