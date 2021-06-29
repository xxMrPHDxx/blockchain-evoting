<div id="sidebar">
<?php
foreach(scandir('manage/') as $file){
	if(substr($file, -4) != '.php') continue;
	$file = explode('.', $file)[0];
?>
	<a class="" href="home.php?page=manage&type=<?php echo $file ?>">
		<?php echo $file ?>s
	</a>
<?php
}
?>
</div>
