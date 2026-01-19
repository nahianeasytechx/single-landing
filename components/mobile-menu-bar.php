<style>
	.modern_mobile_menu {
    position: fixed;
    top: 0;
    right: -100%;
    width: 82%;
    height: 100%;
    background: rgba(255, 255, 255, 0.97);
    backdrop-filter: blur(20px);
    box-shadow: -4px 0 25px rgba(0, 0, 0, 0.1);
    padding: 25px 20px;
    display: flex;
    flex-direction: column;
    transition: all 0.4s ease-in-out;
    z-index: 1000;
    overflow-y: scroll;
}
</style>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay"></div>

<!-- Mobile Menu -->
<div class="menu menu_mm modern_mobile_menu overflow-y-scroll">
	<div class="menu_header d-flex align-items-center justify-content-between">
		<a href="index.php" class="d-flex align-items-center logo_container">
			<img src="images/final-logo.png" alt="Sidratul Muntaha" class="menu_logo">
			<!-- <p class="mobile_nav_title mb-0 ms-2 fw-semibold text-dark">
				<b>Sidratul</b><br><b>Muntaha</b>
			</p> -->
		</a>
		<div class="menu_close_container">
			<div class="menu_close">
				<span></span><span></span>
			</div>
		</div>
	</div>

	<nav class="menu_nav">
		<ul class="menu_list">
			<li><a href="index.php">Home</a></li>
			<li><a href="about.php">About</a></li>
			<li><a href="projects.php">Our Projects</a></li>
			<li><a href="gallery.php">Gallery</a></li>
			<li><a href="contact.php">Contact</a></li>
			<li><a href="notice.php">Notice</a></li>
		</ul>

		<div class="menu_buttons mt-3">
			<a href="zakat-calculator.php" class="menu_btn secondary_btn text-uppercase">Zakat Calculator</a>
			<a href="donate.php" class="menu_btn primary_btn text-uppercase">Donate</a>
		</div>
	</nav>

	<div class="menu_footer mt-auto">
		<div class="menu_phone mb-3">
			<span class="menu_title">Phone: (009) 35475 6688933 32</span>
		</div>
		<div class="menu_social">
			<span class="menu_title">Follow Us</span>
			<div class="social_links mt-1">
				<a href="#"><i class="fa fa-facebook"></i></a>
				<a href="#"><i class="fa fa-instagram"></i></a>
				<a href="#"><i class="fa fa-twitter"></i></a>
				<a href="#"><i class="fa fa-pinterest"></i></a>
			</div>
		</div>
	</div>
</div>
<style>
	:root {
		--theme-color: #00a854;
	}

	/* Overlay */
	.mobile-menu-overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.45);
		backdrop-filter: blur(6px);
		opacity: 0;
		visibility: hidden;
		transition: 0.3s ease;
		z-index: 999;
	}
	.mobile_nav_title {
		font-size: 20px;
		font-weight: 800;
		text-transform: uppercase;
	}

	/* Menu Container */
	.modern_mobile_menu {
		position: fixed;
		top: 0;
		right: -100%;
		width: 82%;
		height: 100%;
		background: rgba(255, 255, 255, 0.97);
		backdrop-filter: blur(20px);
		box-shadow: -4px 0 25px rgba(0, 0, 0, 0.1);
		
		padding: 25px 20px;
		display: flex;
		flex-direction: column;
		transition: all 0.4s ease-in-out;
		z-index: 1000;
	}

	/* Active state */
	.menu_active .modern_mobile_menu {
		right: 0;
	}
	.menu_active .mobile-menu-overlay {
		opacity: 1;
		visibility: visible;
	}

	/* Header */
	.menu_header {
		border-bottom: 1px solid rgba(0, 0, 0, 0.08);
		padding-bottom: 12px;
	}
	.menu_logo {
		width: 225px;
		
	}

	/* Close Button */
	.menu_close {
		position: relative;
		width: 30px;
		height: 30px;
		cursor: pointer;
	}
	.menu_close span {
		position: absolute;
		top: 50%;
		left: 0;
		width: 100%;
		height: 2px;
		background: #222;
		border-radius: 2px;
		transition: 0.3s;
	}
	.menu_close span:first-child {
		transform: rotate(90deg);
	}
	.menu_close span:last-child {
		transform: rotate(-0deg);
	}

	/* Navigation */
	.menu_list {
		list-style: none;
		padding: 0;
		margin: 0;
	}
	.menu_list li {
		margin: 14px 0;
	}
	.menu_list a {
		color: #333;
		font-weight: 500;
		font-size: 18px;
		text-decoration: none;
		display: block;
		padding: 8px 0;
		position: relative;
		transition: 0.3s ease;
	}
	.menu_list a::after {
		content: "";
		position: absolute;
		left: 0;
		bottom: 0;
		width: 0%;
		height: 2px;
		background: var(--theme-color);
		transition: 0.3s;
	}
	.menu_list a:hover {
		color: var(--theme-color);
		transform: translateX(6px);
	}
	.menu_list a:hover::after {
		width: 40%;
	}

	/* Buttons */
	.menu_buttons {
		display: flex;
		flex-direction: column;
		gap: 12px;
	}
	.menu_btn {
		display: block;
		text-align: center;
		padding: 12px;
		border-radius: 12px;
		font-weight: 600;
		text-decoration: none;
		font-size: 16px;
		transition: 0.3s;
	}
	.primary_btn {
		background: var(--theme-color);
		color: #fff;
		box-shadow: 0 4px 10px rgba(0, 168, 84, 0.2);
	}
	.primary_btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 14px rgba(0, 168, 84, 0.35);
	}
	.secondary_btn {
		background: transparent;
		color: var(--theme-color);
		border: 2px solid var(--theme-color);
	}
	.secondary_btn:hover {
		background: var(--theme-color);
		color: #fff;
	}

	/* Footer */
	.menu_footer {
		border-top: 1px solid rgba(0, 0, 0, 0.1);
		padding-top: 15px;
	}
	.menu_title {
		font-weight: 600;
		text-transform: uppercase;
		font-size: 13px;
		color: #555;
	}
	.social_links a {
		color: #333;
		margin-right: 12px;
		font-size: 20px;
		transition: 0.3s;
	}
	.social_links a:hover {
		color: var(--theme-color);
		transform: scale(1.15);
	}

</style>