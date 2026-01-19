<style>
    .footer-logo{
        max-width: 350px;
        width: 2000px;
    }
</style>
<div class="footer-full-content">
    <!-- Footer Content -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <!-- About -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="footer-section">
                        <div class="footer-logo mb-4">
                            <a href="index.php" class="d-flex align-items-center text-decoration-none">
                                <img src="images/final-logo.png" alt="Sidratul Muntaha Logo">
                            </a>
                        </div>
                        <p class="footer-description mb-4">Empowering communities through compassion and dedication to making a positive difference.</p>
                        <div class="footer-social-links">
                            <a href="#" class="social-link" aria-label="YouTube"><i class="fa fa-youtube"></i></a>
                            <a href="#" class="social-link" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
                            <a href="#" class="social-link" aria-label="Twitter"><i class="fa fa-twitter"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Quick Menu -->
                <div class="col-12 col-md-6 col-lg-3 mt-4 mt-md-0">
                    <div class="footer-section">
                        <h5 class="footer-heading mb-4">Quick Menu</h5>
                        <ul class="footer-links list-unstyled">
                            <li><a href="index.php"><i class="fa fa-angle-right me-2 mr-3"></i> Home</a></li>
                            <li><a href="about.php"><i class="fa fa-angle-right me-2 mr-3"></i> About Us</a></li>
                            <li><a href="activities.php"><i class="fa fa-angle-right me-2 mr-3"></i> Activities</a></li>
                            <li><a href="donation-fund.php"><i class="fa fa-angle-right me-2 mr-3"></i> Donate Categories</a></li>
                            <li><a href="scholarship.php"><i class="fa fa-angle-right me-2 mr-3"></i> Scholarship</a></li>
                            <li><a href="volunteer.php"><i class="fa fa-angle-right me-2 mr-3"></i> Join as Volunteer</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="col-12 col-md-6 col-lg-3 mt-4 mt-md-0">
                    <div class="footer-section">
                        <h5 class="footer-heading mb-4">Contact Us</h5>
                        <ul class="footer-contact list-unstyled">
                            <li class="mb-3">
                                <div class="contact-item">
                                    <i class="fa fa-map-marker me-3 mr-2"></i>
                                    <div>
                                        <strong>Address:</strong>
                                        <p class="mb-0">Mirpur Tower, Level-12, Suit-12/C, 4 Darus Salam Road, Mirpur-1, Dhaka-1216</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="contact-item">
                                    <i class="fa fa-phone me-3 mr-2"></i>
                                    <div>
                                        <strong>Phone:</strong>
                                        <p class="mb-0"><a href="tel:+533457953234 53">+53 345 7953 32453</a></p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="contact-item">
                                    <i class="fa fa-envelope me-3 mr-2"></i>
                                    <div>
                                        <strong>Email:</strong>
                                        <p class="mb-0"><a href="mailto:yourmail@gmail.com">yourmail@gmail.com</a></p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Newsletter -->
                <div class="col-12 col-md-6 col-lg-3 mt-4 mt-md-0">
                    <div class="footer-section">
                        <h5 class="footer-heading mb-4">Newsletter</h5>
                        <p class="newsletter-text mb-3">Stay updated with our latest news and promotions.</p>
                        <form class="newsletter-form">
                            <div class="input-group mb-3">
                                <input type="email" class="form-control newsletter-input" placeholder="Your email address" aria-label="Email address" required>
                            </div>
                            <button type="submit" class="btn btn-subscribe w-100">
                                <i class="fa fa-paper-plane me-2"></i>Subscribe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright Bar -->
        <div class="footer-bottom mt-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="copyright-text mb-0">
                            &copy; <script>document.write(new Date().getFullYear());</script> Sidratul Muntaha. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="developer-credit mb-0">
                            Developed by <a href="https://easytechsolutions.xyz/" target="_blank" rel="noopener noreferrer">Easy Tech Solutions</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

<style>
/* Modern Footer Styles */
.footer-full-content {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: #ffffff;
}

.footer {
    padding: 60px 0 0;
}

/* Footer Sections */
.footer-section {
    height: 100%;
}

/* Logo & Brand */
.footer-logo-img {
    width: 120px;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.footer-logo:hover .footer-logo-img {
    transform: scale(1.05);
}

.footer-brand-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
}

.footer-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.95rem;
    line-height: 1.6;
}

/* Social Links */
.footer-social-links {
    display: flex;
    gap: 12px;
}

.social-link {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.social-link:hover {
    background: #ffffff;
    color: #1e3c72;
    transform: translateY(-3px);
}

/* Footer Heading */
.footer-heading {
    font-size: 1.25rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 12px;
}

.footer-heading::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #4CAF50, transparent);
    border-radius: 2px;
}

/* Footer Links */
.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.footer-links a:hover {
    color: #4CAF50;
    padding-left: 5px;
}

/* Contact Info */
.footer-contact li {
    margin-bottom: 20px;
}

.contact-item {
    display: flex;
    align-items: start;
}

.contact-item i {
    font-size: 1.2rem;
    color: #4CAF50;
    margin-top: 3px;
    min-width: 20px;
}

.contact-item strong {
    display: block;
    color: #ffffff;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.contact-item p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    line-height: 1.5;
}

.contact-item a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-item a:hover {
    color: #4CAF50;
}

/* Newsletter */
.newsletter-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.95rem;
    line-height: 1.6;
}

.newsletter-input {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    padding: 12px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.newsletter-input:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #4CAF50;
    color: #ffffff;
    box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
}

.newsletter-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.btn-subscribe {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: #ffffff;
    border: none;
    padding: 12px 25px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.btn-subscribe:hover {
    background: linear-gradient(135deg, #45a049 0%, #3d8b40 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    color: #ffffff;
}

/* Footer Bottom */
.footer-bottom {
    background: rgba(0, 0, 0, 0.2);
    padding: 25px 0;
    margin-top: 50px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.copyright-text,
.developer-credit {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.developer-credit a {
    color: #4CAF50;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.developer-credit a:hover {
    color: #66BB6A;
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer {
        padding: 40px 0 0;
    }
    
    .footer-heading {
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
    
    .footer-bottom {
        margin-top: 30px;
        padding: 20px 0;
    }
    
    .footer-logo-img {
        width: 50px;
        height: 50px;
    }
    
    .footer-brand-name {
        font-size: 1.25rem;
    }
}
</style>