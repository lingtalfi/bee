<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="easy-download-counter.js"></script>
	<title>Html page</title>
</head>

<body>


<h1>Demo easy-download-counter</h1>

<a data-edc-id="score-chantdusoleil" class="countdownload" href="download/chant-soleil-2014-09-27-sample.pdf">Download the score "Chant du soleil"</a>
Downloads: <span data-edc-counter="score-chantdusoleil"></span>
<br />

<a data-edc-id="score-nora" class="countdownload" href="download/chant-soleil-2014-09-27-sample.pdf">Download the score "Nora"</a>
Downloads: <span data-edc-counter="score-nora"></span>

<hr>
<a href="visualizer/visualizer.php" target="_blank">Visualiser les statistiques des liens dans un autre onglet</a>


<script>
	(function ($) {


		$.fn.easyDownloadCounter.settings.serviceUrl = 'service/edc-service.php';
		$(document).ready(function () {
			$('.countdownload').easyDownloadCounter();
		});


	})(jQuery);

</script>


</body>
</html>