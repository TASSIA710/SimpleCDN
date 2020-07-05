<!DOCTYPE html>

<html>

	<head>
		<title><?= WINDOW_TITLE; ?></title>
		<link href="https://unpkg.com/@primer/css/dist/primer.css" type="text/css" rel="stylesheet"/>
		<link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" type="text/css" rel="stylesheet"/>
		<style type="text/css">
		@import url('https://fonts.googleapis.com/css?family=Nunito');
		.font {
			font-family: 'Nunito';
		}
		</style>
	</head>

	<body>

		<div class="Header">
			<div class="Header-item ml-2">
				<a href="<?= BRAND_URL; ?>" class="Header-link f4 font"><i class="fas fa-database"></i>&ensp;<?= BRAND_NAME; ?></a>
			</div>
			<div class="Header-item font">
				<span>Content Delivery Network</span>
			</div>
			<div class="Header-item">
				<form method="GET">
					<input type="text" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>" class="form-control input-dark input-monospace" placeholder="Search a document...">
				</form>
			</div>
		</div>

		<div class="h4-light mx-5 mt-5">
			<?php if (isset($search_term)) { ?>
				<span>Searching for <code>"<?= $search_term; ?>"</code></span>
				<span>&ensp;&vert;&ensp;</span>
			<?php } ?>
			<span>Index of <?= preg_replace('/\?.*/', '', $_SERVER["REQUEST_URI"]); ?></span>
		</div>

		<div class="clearfix mb-1 mx-5">
			<div class="col-3 float-left"><p class="h4 mb-0">Name</p></div>
			<div class="col-2 float-left"><p class="h4 mb-0">Last Modified (GMT)</p></div>
			<div class="col-2 float-left"><p class="h4 mb-0">Type</p></div>
			<div class="col-1 float-left"><p class="h4 mb-0">Size</p></div>
			<div class="col-1 float-left"><p class="h4 mb-0">Flags</p></div>
		</div>

		<hr>

		<?php
		// Display folders
		for ($i = 0; $i < count($folders); $i++) {
			$style = "fas fa-" . ($folders[$i] == ".." ? "reply" : "folder") . " text-blue";
			?>
			<div class="clearfix my-1 mx-5">
				<div class="col-3 float-left"><i class="<?= $style; ?>"></i>&ensp;<a href="<?= $folders[$i] . "/"; ?>"><?= $folders[$i] == ".." ? "Parent Directory" : $folders[$i] . "/"; ?></a></div>
				<div class="col-2 float-left"></div>
				<div class="col-2 float-left"></div>
				<div class="col-1 float-left"></div>
				<div class="col-1 float-left"></div>
			</div>
			<?php
		}

		if (count($folders) > 0 && count($files) > 0) {
			echo "<hr>";
		}

		// Display files
		for ($i = 0; $i < count($files); $i++) {
			$f = $files[$i];
			?>
			<div class="clearfix my-0 mx-5">
				<div class="col-3 float-left"><i class="fas fa-<?= $f["icon"]; ?> text-blue" title="<?= $f['type']; ?>"></i>&ensp;<a href="<?= $f['href']; ?>"><?= $f['href']; ?></a></div>
				<div class="col-2 float-left"><p><?= $f['mtime']; ?></p></div>
				<div class="col-2 float-left"><p><?= $f['type']; ?></p></div>
				<div class="col-1 float-left"><p><?= $f['size']; ?></p></div>
				<div class="col-1 float-left">
				</div>
			</div>
			<?php
		}
		?>

	</body>

</html>
