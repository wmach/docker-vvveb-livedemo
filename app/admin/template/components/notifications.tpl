@notifications = [data-v-component-notifications]

@notifications|deleteAllButFirstChild

[data-v-component-notifications]|prepend = <?php
if (isset($_notifications_idx)) $_notifications_idx++; else $_notifications_idx = 0;

$notificationComponent = $this->_component['notifications'][$_notifications_idx] ?? [];

$notifications = $notificationComponent['notifications'] ?? [];
$count = $notificationComponent['count'] ?? 0;
?>


@notifications [data-v-notification-*] = <?php 
$name = '@@__data-v-notification-(*)__@@';
$path = str_replace('-', '.', $name);
if (isset($notifications) && $path) {
	echo \Vvveb\arrayPath($notifications, $path);
}
?>