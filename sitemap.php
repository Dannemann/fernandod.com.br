<?php
	require_once 'php/inc/autoload.php';
	require_once 'php/inc/utils.php';

	function fkd_sitemap_xml_escape($value) {
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	}

	function fkd_sitemap_url($loc, $lastmod = '') {
		$lastmod = trim((string) $lastmod);
		$xml = "  <url>\n";
		$xml .= '    <loc>'.fkd_sitemap_xml_escape($loc)."</loc>\n";

		if ($lastmod !== '') {
			$xml .= '    <lastmod>'.fkd_sitemap_xml_escape($lastmod)."</lastmod>\n";
		}

		return $xml."  </url>\n";
	}

	function fkd_sitemap_category_ids($treePath) {
		if (!is_file($treePath)) {
			return array();
		}

		$treeItems = file_get_contents($treePath);
		if ($treeItems === false) {
			return array();
		}

		preg_match_all("/\\['([0-9]{3})[^']*'/", $treeItems, $matches);
		$categoryIds = array();

		foreach ($matches[1] as $categoryId) {
			if ($categoryId !== '001') {
				$categoryIds[$categoryId] = true;
			}
		}

		$categoryIds = array_keys($categoryIds);
		sort($categoryIds);

		return $categoryIds;
	}

	header('Content-Type: application/xml; charset=UTF-8');

	$connector = new Connector();
	$connector->connect();

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
	echo fkd_sitemap_url(fkd_public_url('/'));
	echo fkd_sitemap_url(fkd_public_url('/index.php?biografia'));
	echo fkd_sitemap_url(fkd_public_url('/index.php?trabalhos'));
	echo fkd_sitemap_url(fkd_public_url('/index.php?contato'));

	foreach (fkd_sitemap_category_ids(__DIR__.'/modules/tigra_tree_menu/tree_items.js') as $categoryId) {
		echo fkd_sitemap_url(fkd_public_url('/index.php?categoria='.rawurlencode($categoryId)));
	}

	$texts = SQLBook::getSitemapTexts();
	while ($rowText = mysql_fetch_row($texts)) {
		echo fkd_sitemap_url(
			fkd_public_url('/index.php?texto='.rawurlencode($rowText[0])),
			$rowText[1]
		);
	}

	echo "</urlset>\n";
?>
