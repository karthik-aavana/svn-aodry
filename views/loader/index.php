<!DOCTYPE html>
<html>
	<head>
		<title>Aodry</title>
		<link rel="shortcut icon" type="image/png" href="<?php echo base_url('assets/images/favicon.png'); ?>" /> 
		<link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/moving-letters.css">
		<link href="https://fonts.googleapis.com/css?family=PT+Sans&display=swap" rel="stylesheet">
		<script src="<?php echo base_url('assets/'); ?>js/anime.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url('assets/'); ?>js/app3860.js?v=1"></script>
		<script type="text/javascript" src="<?php echo base_url('assets/'); ?>js/moving-letters.js"></script>		
	</head>
	<body class="moving-letters">
		<div class="composition" style="background-color: #ffffff">
			<div class="composition-wrapper">
				<h1 class="ml4"><span class="letters letters-1">Billing</span><span class="letters letters-2">Accounting</span><span class="letters letters-3">Taxation</span></h1>
				<!-- <h1 class="ml11"><span class="text-wrapper"> <span class="line line1"></span><img src="../images/logo.png" class="letters" width="200px"/></span></h1> -->
				<h1 class="ml11"><span class="text-wrapper"> <span class="line line1"></span><span class="letters">&nbsp;</span></span></h1>
			</div>
		</div>		
		<script type="text/javascript">
		   var ml4 = {};
		   ml4.opacityIn = [0,1];
		   ml4.scaleIn = [0.2, 1];
		   ml4.scaleOut = 3;
		   ml4.durationIn = 800;
		   ml4.durationOut = 600;
		   ml4.delay = 500;               
		   var textWrapper = document.querySelector('.ml11 .letters');
		   textWrapper.innerHTML = textWrapper.textContent.replace(/([^\x00-\x80]|\w)/g, "<img src='<?php echo base_url('assets/'); ?>images/logo.png' width='300px' class='letter'>$&</span>");               
		   ml.timelines["ml4"] = anime.timeline({loop: true})
		   ml.timelines["ml11"] = anime.timeline({loop: true})
		     .add({
		       targets: '.ml4 .letters-1',
		       opacity: ml4.opacityIn,
		       scale: ml4.scaleIn,
		       duration: ml4.durationIn
		     }).add({
		       targets: '.ml4 .letters-1',
		       opacity: 0,
		       scale: ml4.scaleOut,
		       duration: ml4.durationOut,
		       easing: "easeInExpo",
		       delay: ml4.delay
		     }).add({
		       targets: '.ml4 .letters-2',
		       opacity: ml4.opacityIn,
		       scale: ml4.scaleIn,
		       duration: ml4.durationIn
		     }).add({
		       targets: '.ml4 .letters-2',
		       opacity: 0,
		       scale: ml4.scaleOut,
		       duration: ml4.durationOut,
		       easing: "easeInExpo",
		       delay: ml4.delay
		     }).add({
		       targets: '.ml4 .letters-3',
		       opacity: ml4.opacityIn,
		       scale: ml4.scaleIn,
		       duration: ml4.durationIn
		     }).add({
		       targets: '.ml4 .letters-3',
		       opacity: 0,
		       scale: ml4.scaleOut,
		       duration: ml4.durationOut,
		       easing: "easeInExpo",
		       delay: ml4.delay
		     }).add({
		    targets: '.ml11 .line',
		    scaleY: [0,1],
		    opacity: [0.5,1],
		    easing: "easeOutExpo",
		    duration: 700
		    })
		    .add({
		    targets: '.ml11 .line',
		    translateX: [0, document.querySelector('.ml11 .letters').getBoundingClientRect().width + 0],
		    easing: "easeOutExpo",
		    duration: 700,
		    delay: 100
		    }).add({
		    targets: '.ml11 .letter',
		    opacity: [0,1],
		    easing: "easeOutExpo",
		    duration: 600,
		    offset: '-=775',
		    delay: (el, i) => 34 * (i+1)
		    }).add({
		    targets: '.ml11',
		    opacity: 0,
		    duration: 1000,
		    easing: "easeOutExpo",
		    delay: 1000
		    }).add({
		        targets: '.ml4',
		        opacity: 0,
		        duration: 500,
		        delay: 500
		      }); 
		</script>		
	</body>
</html>

