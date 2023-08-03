import(common.tpl)

.settings input[type="text"]|value = <?php 
	$_setting = '@@__name:\[(.*)\]__@@';
	echo $_POST['settings'][$_setting]  ?? Vvveb\get_setting('flat-rate-shipping', $_setting, null) ?? '@@__value__@@';
	//name="settings[setting-name] > get only setting-name
?>

.settings input[type="number"]|value = <?php 
	$_setting = '@@__name:\[(.*)\]__@@';
	echo $_POST['settings'][$_setting]  ?? Vvveb\get_setting('flat-rate-shipping', $_setting, null) ?? '@@__value__@@';
	//name="settings[setting-name] > get only setting-name
?>

.settings input[type="radio"]|addNewAttribute = <?php 
	$_setting = '@@__name:\[(.*)\]__@@';
	$_value = '@@__value__@@';
	
	if (isset($_POST['settings'][$_setting]) && ($_POST['settings'][$_setting] == $_value) ||
		(Vvveb\get_setting($_setting, null) == $_value)  ||
		 '@@__checked__@@') { 
			echo 'checked';
	}
?>

.settings textarea = <?php 
	$_setting = '@@__name:\[(.*)\]__@@';
	echo $_POST['settings'][$_setting] ?? Vvveb\get_setting('flat-rate-shipping', $_setting, null) '@@__innerHTML__@@';
?>


@zone_group = [data-v-zone_group_id] [data-v-option]
@zone_group|deleteAllButFirstChild

@zone_group|before = <?php
$count = 0;
if(isset($this->zone_group) && is_array($this->zone_group)) {
	foreach ($this->zone_group as $zone_group_index => $zone_group) {?>
	
	
	@zone_group|innerText = $zone_group
	@zone_group|value	  = $zone_group_index
	@zone_group|addNewAttribute = <?php if (isset($this->zone_group_id) && $zone_group == $this->zone_group_id) echo 'selected';?>
	
	
	@zone_group|after = <?php 
		$count++;
	} 
}?>

@tax_class = [data-v-tax_class_id] [data-v-option]
@tax_class|deleteAllButFirstChild

@tax_class|before = <?php
$count = 0;
if(isset($this->tax_class) && is_array($this->tax_class)) {
	foreach ($this->tax_class as $tax_class_index => $tax_class) {?>
	
	
	@tax_class|innerText = $tax_class
	@tax_class|value	  = $tax_class_index
	@tax_class|addNewAttribute = <?php if (isset($this->tax_class_id) && $tax_class == $this->tax_class_id) echo 'selected';?>
	
	
	@tax_class|after = <?php 
		$count++;
	} 
}?>
