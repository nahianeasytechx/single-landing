	<?php require 'footer-content.php';  ?>
	</div>

	<!-- Page Wise JS File linking -->
	<?php
	if ($current_page == 'index.php') {
	?>
		<!-- Home -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/popper.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/greensock/TweenMax.min.js"></script>
		<script src="plugins/greensock/TimelineMax.min.js"></script>
		<script src="plugins/scrollmagic/ScrollMagic.min.js"></script>
		<script src="plugins/greensock/animation.gsap.min.js"></script>
		<script src="plugins/greensock/ScrollToPlugin.min.js"></script>
		<script src="plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/video-js/video.min.js"></script>
		<script src="plugins/video-js/Youtube.min.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

		<script src="js/custom.js"></script>

		<script src="js/lightbox.js"></script>
	<?php
	} else if ($current_page == 'about.php') {
	?>
		<!-- About -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/popper.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/greensock/TweenMax.min.js"></script>
		<script src="plugins/greensock/TimelineMax.min.js"></script>
		<script src="plugins/scrollmagic/ScrollMagic.min.js"></script>
		<script src="plugins/greensock/animation.gsap.min.js"></script>
		<script src="plugins/greensock/ScrollToPlugin.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/elements.js"></script>
		<script src="js/about.js"></script>
	<?php
	} else if ($current_page == 'contact.php') {
	?>
		<!-- Contact -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/popper.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyCIwF204lFZg1y4kPSIhKaHEXMLYxxuMhA"></script>
		<script src="js/contact.js"></script>
	<?php
	} else if ($current_page == 'projects.php') {
	?>
		<!-- Courses -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/popper.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/projects.js"></script>
		<script src="js/courses.js"></script>
	<?php
	} else if ($current_page == 'gallery.php') {
	?>
		<!-- News -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/popper.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
		<script src="js/news.js"></script>
	<?php
	} else if ($current_page == 'notice.php') {
	?>
		<!-- Notice -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/news.js"></script>
	<?php
	} else if ($current_page == 'notice-details.php') {
	?>
		<!-- Notice -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/news.js"></script>
	<?php
	} else if ($current_page == 'project-details.php') {
	?>
		<!-- Notice -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/projectsDetails.js.js"></script>
		<script src="js/news.js"></script>
	<?php
	} else if ($current_page == 'donate.php') {
	?>
		<!-- Notice -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/donate.js"></script>
		<script src="js/news.js"></script>
	<?php
	}
	 else if ($current_page == 'scholarship.php') {
	?>
		<!-- Notice -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/donate.js"></script>
		<script src="js/news.js"></script>
	<?php
	}
	else if ($current_page == 'donation-fund.php') {
	?>
		<!-- Courses -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/popper.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/donationFund.js"></script>
		<script src="js/courses.js"></script>
	<?php
	}
	else if ($current_page == 'zakat-calculator.php') {
	?>
		<!-- Courses -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/popper.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/donationFund.js"></script>
		<script src="js/courses.js"></script>
	<?php
	}
	else if ($current_page == 'volunteer.php') {
	?>
		<!-- Courses -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="styles/bootstrap4/popper.js"></script>
		<script src="styles/bootstrap4/bootstrap.min.js"></script>
		<script src="plugins/easing/easing.js"></script>
		<script src="plugins/parallax-js-master/parallax.min.js"></script>
		<script src="js/donationFund.js"></script>
		<script src="js/courses.js"></script>
	<?php
	}
	?>
			<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
	<script src="js/lanToggle.js"></script>
	    <script>
      document.addEventListener("DOMContentLoaded", function() {
        AOS.init({
          duration: 1000,
          once: true
        });
      });
    </script>
	<!-- Hidden Google Translate Element -->
<div id="google_translate_element" style="display: none;"></div>



	</body>

	</html>