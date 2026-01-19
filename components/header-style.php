 <style>
     
     @import url('https://fonts.googleapis.com/css?family=Montserrat:300,400,400i,500,600,700,800,900');

     /* ========================================
   TABLE OF CONTENTS
   ========================================
   1. GLOBAL STYLES
   2. TYPOGRAPHY
   3. HEADER & NAVIGATION
   4. HOME/BANNER SECTION
   5. FEATURED SECTION
   6. COURSES/ACTIVITIES
   7. DONATION SECTION
   8. ABOUT SECTION
   9. GALLERY
   10. CONTACT
   11. NOTICE/NEWS
   12. ZAKAT CALCULATOR
   13. FOOTER & JOIN SECTION
   14. MEDIA QUERIES
   ======================================== */

     /* ========================================
   1. GLOBAL STYLES
   ======================================== */
     body {
         overflow-x: hidden !important;
         font-family: "Poppins", sans-serif !important;
     }

     ::selection {
         background: #02BD61;
         color: #FFFFFF;
     }

     /* ========================================
   2. TYPOGRAPHY
   ======================================== */
     h1,
     h2,
     h3,
     h4,
     h5,
     h6 {
         font-family: 'Poppins', sans-serif;
         color: #000;
         -webkit-font-smoothing: antialiased;
         -webkit-text-shadow: rgba(0, 0, 0, .01) 0 0 1px;
         text-shadow: rgba(0, 0, 0, .01) 0 0 1px;
     }

     p {
         font-family: 'Poppins', sans-serif;
         color: #000;
     }

     /* ========================================
   3. HEADER & NAVIGATION
   ======================================== */
     .header_container {
         position: fixed;
         left: 50%;
         right: auto;
         transform: translateX(-50%);
         border-radius: 15px;
         background: rgba(255, 255, 255, 0.8);
         border-bottom: 1px solid #ebebeb;
         box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
         backdrop-filter: blur(8px);
         -webkit-backdrop-filter: blur(8px);
         z-index: 999;
         max-width: 1200px;
         width: 90%;
         top: 20px;
     }

     .header_content {
         height: 78px;
         -webkit-transition: all 200ms ease;
         -moz-transition: all 200ms ease;
         -ms-transition: all 200ms ease;
         -o-transition: all 200ms ease;
         transition: all 200ms ease;
     }

     .main_nav,
     .search_button {
         margin-top: 0px;
     }

     .main_nav_container {
         margin: 0 auto;
     }

     .main_nav li a {
         background: transparent;
     }

     .main_nav li a:hover,
     .main_nav li.active a {
         background: #02BD61;
         border-radius: 5px;
     }

     .menu_nav {
         padding-top: 18px;
     }

     .menu_nav ul li {
         margin-bottom: 20px;
     }

     .menu_nav ul li a {
         font-size: 17px;
         color: #000;
         font-weight: 600;
     }

     .menu_nav ul li a:hover {
         color: #008E48;
     }

     .hamburger:hover i {
         color: #008E48;
     }

     /* Logo */
     .logo_img img {
         object-fit: contain;
         width: 60px;
     }

     /* Donate Button in Nav */
     .donate-btn-section {
         margin-left: 30px;
     }

     .header_container .donate-btn-section a {
         color: white;
     }

     .header_container .donate-btn-section a:hover {
         color: white;
     }

     .nav-donate-btn {
         display: block;
         padding: 10px 25px;
         background: #05A657;
         font-weight: 500;
         border-radius: 10px;
     }

     .nav-zakat-btn {
         padding: 8px 26px;
         border: 1px solid #06CC6B;
         border-radius: 10px;
         font-weight: 500;
         text-decoration: none;
         font-size: 12px;
     }

     .nav-zakat-btn a {
         text-decoration: none;
         color: #000;
     }

     /* Language Toggle */
     .language-toggle {
         display: flex;
         border-radius: 10px;
         overflow: hidden;
         background-color: #e7efeb;
         margin-left: 15px;
     }

     .language-toggle button {
         border: none;
         outline: none;
     }

     .lang-btn {
         border: none;
         outline: none;
         padding: 5px 15px;
         background: transparent;
         font-weight: 600;
         color: #1a1a1a;
         cursor: pointer;
         transition: background 0.3s, color 0.3s;
     }

     .lang-btn.active {
         background-color: #b5e0c5;
         color: #000;
     }

     .language-toggle-mm {
         display: flex;
         border-radius: 10px;
         overflow: hidden;
         background-color: #e7efeb;
         margin-left: 15px;
     }

     .language-toggle-mm button {
         border: none;
         outline: none;
     }

     .language-toggle-mm .lang-btn {
         border: none;
         outline: none;
         padding: 5px 15px;
         background: transparent;
         font-weight: 600;
         color: #1a1a1a;
         cursor: pointer;
         transition: background 0.3s, color 0.3s;
     }

     /* ========================================
   4. HOME/BANNER SECTION
   ======================================== */
     .parallax-window {
         min-height: 308px;
         background: transparent;
     }

     .index-page .home {
         width: 100%;
         height: 90vh;
     }

     .home_container {
         position: absolute;
         bottom: 132px;
         left: 0;
         width: 100%;
     }

     .home-background {
         position: relative;
         background-image: url("images/Banner3.jpg");
         background-repeat: no-repeat;
         background-size: cover;
         background-position: center;
         min-height: 90vh;
         display: flex;
         justify-content: center;
         align-items: center;
         text-align: center;
         color: white;
         overflow: hidden;
     }

     .home-background .overlay {
         position: absolute;
         top: 0;
         left: 0;
         width: 100%;
         height: 97%;
         background: rgba(0, 0, 0, 0.6);
         z-index: 1;
     }

     .home-background .content {
         position: relative;
         z-index: 2;
         padding: 0px;
         padding-right: 196px;
         text-align: left;
     }

     .home-background .content h1 {
         color: #fff;
         font-size: 65px;
         line-height: 70px;
     }

     .home-background .content p {
         color: #fff;
         font-size: 17px;
         line-height: 28.4px;
     }

     .home-background .btn {
         display: inline-block;
         margin: 10px;
         padding: 12px 28px;
         background: #008E48;
         color: white;
         font-weight: 600;
         border-radius: 10px;
         text-decoration: none;
         transition: background 0.3s;
     }

     .home-background .btn:hover {
         background: #01a855;
     }

     .home-background .btn-outline {
         background: transparent;
         border: 1px solid white;
     }

     .home-background .btn-outline:hover {
         background: white;
         color: #222;
     }

     /* Button Styles */
     .button {
         background: #02BD61;
     }

     .button:hover {
         background: #06CC6B;
     }

     .button_arrow {
         background: #05A657;
     }

     .button_arrow:hover {
         background: #06CC6B;
     }

     .button:hover .button_arrow {
         background: #06CC6B;
     }

     .button_arrow i {
         color: #fff;
     }

     .button:hover .button_arrow i {
         color: white;
     }

     .home_slider_nav_container .home_slider_prev {
         position: absolute;
         left: 2%;
         background: #019f51;
         border-radius: 50px;
   
     }

     .home_slider_nav_container .home_slider_next {
         position: absolute;
         right: 2%;
         background: #019f51;
         border-radius: 50px;
     }

     .home_slider_nav_container {
         position: absolute;
         left: 0;
top: 50%;
         width: 103px;
         height: 51px;
         background: #02BD61;
         z-index: 3;
         width: 100%;
         background: transparent;
     }

     /* ========================================
   5. FEATURED SECTION
   ======================================== */
     .featured_container {
         width: 100%;
         padding-left: 0;
         margin-top: 0;
     }

     .featured_title h3 a:hover {
         color: #02BD61;
     }

     .featured_author_name:hover {
         color: #02BD61;
     }

     .featured_author_name a:hover {
         color: #02BD61;
     }

     .featured_content::after {
         background: #06CC6B;
     }

     .featured_text {
         margin-top: 18px;
         line-height: 1.92;
         color: black;
     }

     /* Featured Banner Overlay Styles */
     .featured_background_wrapper {
         position: relative;
         height: 100%;
         min-height: 400px;
     }

     .featured_background {
         position: absolute;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background-size: cover;
         background-position: center;
         background-repeat: no-repeat;
     }

     .featured_overlay {
         position: absolute;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background: rgba(15, 41, 32, 0.7);
         z-index: 1;
     }

     .featured_banner_content {
         position: absolute;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
         z-index: 2;
         text-align: center;
         color: #fff;
         width: 90%;
         padding: 20px;
     }

     .featured_banner_content h1 {
         font-size: 36px;
         font-weight: 700;
         margin-bottom: 20px;
         color: #fff;
     }

     .featured_banner_content p {
         font-size: 16px;
         line-height: 1.6;
         margin-bottom: 30px;
         color: #fff;
     }

     .buttons {
         display: flex;
         gap: 15px;
         justify-content: center;
         flex-wrap: wrap;
     }

     /* ========================================
   6. COURSES/ACTIVITIES
   ======================================== */
     .courses {
         padding-top: 1px;
     }

     .course_tag {
         background: #02BD61;
     }

     .course_tag:hover {
         background: #05A657;
     }

     .course_title h3 a {
         font-size: 18px;
         overflow: hidden;
         text-overflow: ellipsis;
         width: 100%;
         display: block;
         white-space: nowrap;
     }

     .course_title h3 a:hover {
         color: #02BD61;
     }

     .course_text {
         margin: 18px 0;
     }

     .course::after {
         background: #02BD61;
     }

     .home-page .course_body {
         width: 100%;
         padding-left: 34px;
         padding-right: 32px;
         padding-top: 56px;
         padding-bottom: 51px;
         background: #fff;
         margin-bottom: 20px;
         box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px, rgba(0, 0, 0, 0.05) 0px 4px 6px -2px;
         margin-top: -2px;
         -webkit-border-bottom-right-radius: 20px;
         -webkit-border-bottom-left-radius: 20px;
         -moz-border-radius-bottomright: 20px;
         -moz-border-radius-bottomleft: 20px;
         border-bottom-right-radius: 20px;
         border-bottom-left-radius: 20px;
         border: 1px solid #ccc;
     }

     .owl-carousel .owl-item img {
         -webkit-border-top-left-radius: 20px;
         -webkit-border-top-right-radius: 20px;
         -moz-border-radius-topleft: 20px;
         -moz-border-radius-topright: 20px;
         border-top-left-radius: 20px;
         border-top-right-radius: 20px;
     }

     .courses_slider_nav {
         background: #02BD61;
     }

     .courses_slider_prev {
         left: -29px;
         z-index: 20;
         border-radius: 50px;
     }

     .courses_slider_next {
         right: -29px;
         z-index: 20;
         border-radius: 50px;
     }

     .courses_slider_nav:hover {
         background: #06CC6B;
     }

     .courses_paginations ul li a:hover,
     .courses_paginations ul li.active a {
         color: #02BD61;
     }

     .text-elipsis {
         overflow: hidden;
         text-overflow: ellipsis;
         width: 200px;
         display: block;
         white-space: nowrap;
     }

     .text-truncated {
         line-clamp: 3;
         display: -webkit-box;
         -webkit-box-orient: vertical;
         -webkit-line-clamp: 3;
         overflow: hidden;
         text-overflow: ellipsis;
     }

     /* ========================================
   7. DONATION SECTION
   ======================================== */
     .section_title h2 {
         font-weight: 600;
         color: black;
     }

     .section_subtitle {
         margin-top: 40px;
         margin-bottom: 20px;
         color: black;
     }

     .donate-preview_item .course .course_image img {
         border-top-left-radius: 20px;
         border-top-right-radius: 20px;
     }

     .donate-preview_item .course .course_body {
         width: 100%;
         padding-left: 20px;
         padding-right: 32px;
         padding-top: 25px;
         padding-bottom: 25px;
         background: #fff;
         margin-bottom: 20px;
         box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px, rgba(0, 0, 0, 0.05) 0px 4px 6px -2px;
         margin-top: -2px;
         -webkit-border-bottom-right-radius: 20px;
         -webkit-border-bottom-left-radius: 20px;
         -moz-border-radius-bottomright: 20px;
         -moz-border-radius-bottomleft: 20px;
         border-bottom-right-radius: 20px;
         border-bottom-left-radius: 20px;
         border: 1px solid #ccc;
     }

     .donate-preview_item-btn {
         width: 100%;
         padding: 10px 0;
         border-radius: 8px;
         text-decoration: none;
         color: #fff;
         font-size: 17px;
         font-weight: 600;
         background: #008E48;
         text-align: center;
     }

     .donate-preview_item-btn:hover {
         color: #fff;
         background: #02BD61;
     }

     .donate-form-bg {
         margin-top: -185px;
         padding: 50px;
         background: #E8B65D;
         background-image: url("images/donate bg.svg");
         border-radius: 10px;
         z-index: 10;
         max-width: 981px;
         color: white;
     }

     .course_search_form>div::after {
         background: #06CC6B;
         width: 95%;
     }

     .course-lable {
         color: #000;
         font-size: 17px;
         font-weight: 600;
     }

     .course-lable span {
         color: red;
         font-weight: 600;
     }

     .course_input {
         position: relative;
         width: 95%;
         height: 50px;
         border: none;
         outline: none;
         background: #f2f1f8;
         padding: 0 22px;
         color: #6C6A74;
         border-radius: 10px;
     }

     .course_input:hover {
         border-radius: 0px;
         border-top-left-radius: 10px;
         border-top-right-radius: 10px;
     }

     .course_button {
         background: #02BD61;
         border-radius: 10px;
         margin-top: 34px;
     }

     .course_button:hover {
         background: #02BD61;
     }

     .course_button .button_arrow {
         background: #05A657;
     }

     .course_button:hover .button_arrow {
         background: #06CC6B;
     }

     /* Donation Page */
     .donate-page {
         margin: 125px 0;
     }

     .donate-page .donation-card {
         border: none;
         border-radius: 16px;
         overflow: hidden;
         box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
         background: #ffffff;
         transition: transform 0.3s ease;
     }

     .donate-page .card-header-custom {
         background: linear-gradient(135deg, #02BD61 0%, #019f51 100%);
         color: #ffffff;
         padding: 2.5rem 2rem;
         border: none;
     }

     .donate-page .card-header-custom h4 {
         font-weight: 700;
         margin-bottom: 0.75rem;
         font-size: 1.75rem;
         color: #ffffff;
     }

     .donate-page .card-header-custom p {
         margin: 0;
         opacity: 0.95;
         font-size: 1rem;
         line-height: 1.7;
         color: #ffffff;
     }

     .donate-page .merchant-info {
         background: linear-gradient(135deg, #02BD61 0%, #01a855 100%);
         padding: 1.25rem;
         border-radius: 12px;
         margin-bottom: 1.75rem;
         border-left: 2px solid #019f51;
         color: #ffffff;
         box-shadow: 0 2px 10px rgba(2, 189, 97, 0.2);
     }

     .donate-page .merchant-info strong {
         color: #ffffff;
         font-size: 1.15rem;
         display: flex;
         align-items: center;
         gap: 0.5rem;
     }

     .donate-page .merchant-info small {
         color: rgba(255, 255, 255, 0.9);
         display: block;
         margin-top: 0.5rem;
         font-size: 0.9rem;
     }

     .donate-page .amount-grid {
         display: grid;
         grid-template-columns: repeat(3, 1fr);
         gap: 0.875rem;
         margin-bottom: 1.75rem;
     }

     .donate-page .amount-btn {
         padding: 1rem;
         border: 2px solid #e0e0e0;
         background: #ffffff;
         border-radius: 10px;
         font-weight: 700;
         color: #333333;
         transition: all 0.25s ease;
         cursor: pointer;
         font-size: 1.05rem;
     }

     .donate-page .amount-btn:hover {
         border-color: #02BD61;
         color: #02BD61;
         transform: translateY(-3px);
         box-shadow: 0 6px 15px rgba(2, 189, 97, 0.2);
     }

     .donate-page .amount-btn.active {
         background: linear-gradient(135deg, #02BD61 0%, #019f51 100%);
         border-color: #02BD61;
         color: #ffffff;
         transform: translateY(-3px) scale(1.02);
         box-shadow: 0 8px 20px rgba(2, 189, 97, 0.4);
     }

     .donate-page .form-label {
         font-weight: 600;
         color: #2c3e50;
         margin-bottom: 0.625rem;
         font-size: 1rem;
     }

     .donate-page .payment-method {
         padding: 5px 10px;
         box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
         border-radius: 10px;
     }

     .donate-page .form-check {
         position: relative;
         display: block;
         margin-bottom: .5rem;
         margin: 1px 24px;
     }

     .donate-page .form-control {
         border: 1px solid #e9ecef;
         padding: 0.875rem 1.125rem;
         transition: all 0.2s ease;
         font-size: 1rem;
         border-radius: 10px;
         color: #000;
     }

     .donate-page .form-control:focus {
         border-color: #02BD61;
         box-shadow: 0 0 0 0.25rem rgba(2, 189, 97, 0.15);
         outline: none;
     }

     .donate-page .input-group {
         border-radius: 10px;
         overflow: hidden;
     }

     .donate-page .input-group-text {
         background: linear-gradient(135deg, #02BD61 0%, #019f51 100%);
         border: 2px solid #02BD61;
         border-right: none;
         color: #fff;
         font-weight: 700;
         padding: 0.875rem 1.25rem;
         font-size: 1.1rem;
     }

     .donate-page .category_options {
         border: 1px solid gainsboro;
         outline: none;
         color: black;
         border-radius: 10px;
     }

     .donate-page .input-group .form-control {
         border-left: none;
         border-radius: 0 10px 10px 0;
     }

     .donate-page .input-group:focus-within .input-group-text {
         border-color: #02BD61;
     }

     .donate-page .input-group:focus-within .form-control {
         border-color: #02BD61;
     }

     .donate-page .btn-donate {
         background: linear-gradient(135deg, #02BD61 0%, #019f51 100%);
         border: none;
         padding: 8px 24px;
         font-size: 17px;
         font-weight: 600;
         border-radius: 12px;
         transition: all 0.3s ease;
         text-transform: uppercase;
         letter-spacing: 0.75px;
         box-shadow: 0 4px 15px rgba(2, 189, 97, 0.3);
         color: white;
         cursor: pointer;
     }

     .donate-page .btn-donate:hover {
         background: linear-gradient(135deg, #019f51 0%, #017a3f 100%);
         transform: translateY(-3px);
         box-shadow: 0 8px 25px rgba(2, 189, 97, 0.4);
         color: white;
     }

     .donate-page .required {
         color: #dc3545;
         font-weight: 700;
     }

     .alert-success {
         background: #d4edda;
         border: 1px solid #c3e6cb;
         color: #155724;
         padding: 1rem;
         border-radius: 8px;
         margin-bottom: 1rem;
     }

     .donate_card_bg {
         margin: 20px 0;
         background-color: #02BD61;
         border-radius: 15px;
         padding: 10px 25px;
         display: flex;
         justify-content: space-between;
         flex-wrap: wrap;
         column-gap: 10px;
     }

     .donate_card_bg .cardp {
         width: 70%;
     }

     .donate_card_bg .cardp p {
         color: white;
         font-size: 32px;
         font-weight: 700;
     }

     .donate_card_bg .card-btn {
         width: 25%;
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
     }

     .donate_card_bg .card-btn a {
         text-decoration: none;
         color: #fff;
         margin: 0 auto;
         width: 75%;
         background-color: #f78b04;
         border-radius: 15px;
         font-weight: 600;
         padding: 13px 54px;
         border: 0;
         cursor: pointer;
     }

     .donate_card_bg a:hover {
         background-color: #E8B65D;
     }

     /* ========================================
   8. ABOUT SECTION
   ======================================== */
     .about {
         width: 100%;
         background: #FFFFFF;
         padding-top: 28px;
         padding-bottom: 111px;
     }

     .about-quotes .row {
         display: flex;
         flex-wrap: wrap;
         align-items: stretch;
     }

     .about-quotes .col-sm-12,
     .about-quotes .col-md-6,
     .about-quotes .col-lg-4 {
         display: flex;
     }

     .quote-item {
         padding: 30px;
         border-radius: 15px;
         box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 5px 0px, rgba(0, 0, 0, 0.1) 0px 0px 1px 0px;
         width: 100%;
         height: 100%;
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
     }

     .quote-item h1 {
         font-size: 24px;
         font-weight: 600;
     }

     .quote-item i {
         font-size: 36px;
         color: #E8B65D;
         margin-bottom: 10px;
     }

     .quote-item p {
         font-size: 14px;
         text-align: center;
     }

     .about-quote-btn {
         display: flex;
         justify-content: center;
         align-items: center;
     }

     .about-quote-btn a {
         text-decoration: none;
         color: #fff;
         font-size: 18px;
         font-weight: 600;
         background: #008E48;
         padding: 10px 35px;
         border-radius: 10px;
     }

     .about-quote-btn a:hover {
         background: #02BD61;
         transition: all .3s ease-in-out;
     }

     .breadcrumbs ul li {
         color: #02BD61;
         
     }

     .breadcrumbs ul li a:hover {
         color: #02BD61;
     }

     .tabs {
         width: 100%;
         margin-top: 20px;
         background: #FFFFFF;
     }

     .tab {
         height: 50px;
         background: #008E48;
         font-size: 16px;
         color: #fff;
         font-weight: 600;
         line-height: 50px;
         padding-left: 25px;
         padding-right: 25px;
         text-align: center;
         cursor: pointer;
         margin-left: 2px;
         border-radius: 5px;
     }

     .tabs-rounded {
         border: 1px solid #ccc;
         border-radius: 20px;
         padding: 10px;
         margin-bottom: 79px;
     }

     .tab::after {
         position: absolute;
         bottom: -8px;
         background: #000;
     }

     .tab_panels {
         padding-left: 50px;
         padding-right: 2px;
         padding-top: 47px;
         padding-bottom: 32px;
     }

     .accordion::after {
         background: #02BD61;
     }

     .associatives img {
         width: 15%;
         margin: 0 30px;
     }

     /* ========================================
   9. GALLERY
   ======================================== */
     .photos img {
         width: 100%;
         height: 100%;
         border-radius: 20px;
         object-fit: contain;
     }

     .photos .image-alignment {
         display: flex;
         justify-content: center;
         align-items: center;
         margin-bottom: 20px;
     }

     .photos .image-alignment img {
         height: auto;
         object-fit: contain;
         border-radius: 10px;
     }

     .video_container {
         width: 100%;
         height: 100%;
         background: white;
         box-shadow: 0px;
         border-radius: 20px;
     }

     .video iframe {
         border-radius: 20px;
         border: none;
         outline: none;
         box-shadow: none;
     }

     /* ========================================
   10. CONTACT
   ======================================== */
     .contact_info_title {
         color: #02BD61;
     }

     .contact_input:hover,
     .contact_input:focus {
         border-bottom: solid 3px #02BD61;
     }

     .contact_button {
         background: #02BD61;
     }

     .contact_button:hover {
         background: #06CC6B;
     }

     .contact_button .button_arrow {
         width: 47px;
         background: #05A657;
     }

     .contact_button:hover .button_arrow {
         width: 47px;
         background: #05A657;
     }

     .contact-socials i {
         margin-right: 15px;
         font-size: 25px;
         color: #008E48;

     }

     .contact-socials i:hover {
         color: #06CC6B;
         transition: all ease-in-out .3s;
     }

     /* ========================================
   11. NOTICE/NEWS
   ======================================== */
     .card_container {
         margin: 50px 0;
         width: 100%;
         border: 1px dotted blanchedalmond;
         box-shadow: rgba(0, 0, 0, 0.1) 0px 2px 8px;
         padding: 2rem;
         display: flex;
         justify-content: space-between;
         border-radius: 25px;
         flex-wrap: wrap;
         cursor: pointer;
     }

     .card_container .date {
         display: flex;
         flex-direction: column;
         column-gap: 10px;
         justify-content: center;
         align-items: center;
     }

     .card_container .notice_text {
         display: flex;
         flex-direction: column;
         column-gap: 10px;
     }

     .card_container .btn {
         display: flex;
         flex-direction: column;
         justify-content: center;
     }

     .card_container .btn button {
         background: #008E48;
         opacity: 0.8;
         color: white;
         font-weight: 600;
         padding: 10px 25px;
         border: 0;
         border-radius: 15px;
     }

     .notice-title {
         font-size: 24px;
         font-weight: 600;
         color: #02BD61;
     }

     .notice-btn {
         padding: 10px 20px;
         background: #EEF2F0;
         border: 0;
         border-radius: 15px;
         color: #008E48;
     }

     .notice-features {
         padding: 30px 40px;
         background: #008E48;
         font-weight: 600;
         color: #fff;
         border-radius: 20px;
     }

     .notice-features h1 {
         font-size: 20px;
         font-weight: 600;
         color: #fff;
     }

     .notice-features p {
         color: #fff;
     }

     .description-card {
         background-color: white;
         border-radius: 20px;
         display: flex;
         box-shadow: -2px 6px 9px -1px rgba(0, 0, 0, 0.77);
         -webkit-box-shadow: -2px 6px 9px -1px rgba(0, 0, 0, 0.77);
         -moz-box-shadow: -2px 6px 9px -1px rgba(0, 0, 0, 0.77);
     }

     .event_date {
         background: #02BD61;
     }

     .news_post_date {
         color: #02BD61;
     }

     .news_post_title a:hover {
         color: #008E48;
     }

     .event_title a:hover {
         color: #02BD61;
     }

     .course-bg h1 {
         font-size: 48px;
         font-weight: 600;
         padding: 20px 0;
     }

     .card-bg {
         background-color: #008E48;
         border-radius: 10px;
     }

     .card-bg h4 {
         color: white;
         font-weight: 600;
     }

     .card-bg ul li {
         font-size: 14px;
         font-weight: 500;
         color: white;
     }

     .card-bg ul li i {
         font-size: 16px;
         margin-right: 5px;
         margin-top: 2px;
         color: #fff;
     }

     .program-details img {
         margin: 20px 0;
         border-radius: 20px;
     }

     /* ========================================
   12. ZAKAT CALCULATOR
   ======================================== */
     .zakat-calculator {
         background: #f8f9fa;
         padding: 40px 0;
     }

     .calculator-container {
         background: white;
         border-radius: 10px;
         box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
         padding: 30px;
         max-width: 800px;
         margin: 0 auto;
     }

     .form-group {
         margin-bottom: 20px;
     }

     .form-group label {
         display: block;
         font-weight: 600;
         color: #333;
         margin-bottom: 8px;
         font-size: 14px;
     }

     .form-control {
         width: 100%;
         padding: 12px 15px;
         border: 1px solid #ddd;
         border-radius: 5px;
         font-size: 14px;
         transition: border-color 0.3s;
     }

     .form-control:focus {
         outline: none;
         border-color: #02BD61;
         box-shadow: 0 0 0 3px rgba(2, 189, 97, 0.1);
     }

     .form-group select {
         padding: 0 10px;
     }

     .form-select {
         width: 100%;
         padding: 12px 15px;
         border: 1px solid #ddd;
         border-radius: 5px;
         font-size: 14px;
         background: white;
         cursor: pointer;
     }

     .section-title {
         font-size: 18px;
         font-weight: 700;
         color: #2c3e50;
         margin: 25px 0 15px 0;
         padding-bottom: 10px;
         border-bottom: 3px solid #02BD61;
     }

     .btn-calculate {
         background: #02BD61;
         color: white;
         padding: 12px 40px;
         border: none;
         border-radius: 5px;
         font-size: 16px;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
     }

     .btn-calculate:hover {
         background: #06CC6B;
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(2, 189, 97, 0.3);
     }

     .btn-calculate:active {
         transform: translateY(0);
     }

     .btn-reset {
         background: #6c757d;
         color: white;
         padding: 12px 40px;
         border: none;
         border-radius: 5px;
         font-size: 16px;
         font-weight: 600;
         cursor: pointer;
         margin-left: 10px;
         transition: all 0.3s;
     }

     .btn-reset:hover {
         background: #5a6268;
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
     }

     .button-group {
         margin-top: 30px;
         text-align: left;
     }

     .result-box {
         margin-top: 30px;
         padding: 25px;
         border-radius: 8px;
         background: #e8f5e9;
         border-left: 4px solid #02BD61;
         animation: slideInUp 0.5s ease-out;
         scroll-margin-top: 100px;
     }

     .result-box.not-payable {
         background: #fff3cd;
         border-left-color: #ffc107;
     }

     @keyframes slideInUp {
         from {
             opacity: 0;
             transform: translateY(30px);
         }

         to {
             opacity: 1;
             transform: translateY(0);
         }
     }

     @keyframes fadeIn {
         from {
             opacity: 0;
         }

         to {
             opacity: 1;
         }
     }

     .result-title {
         font-size: 20px;
         font-weight: 700;
         color: #2c3e50;
         margin-bottom: 15px;
         animation: fadeIn 0.6s ease-out 0.2s both;
     }

     .result-item {
         display: flex;
         justify-content: space-between;
         padding: 10px 0;
         border-bottom: 1px solid rgba(0, 0, 0, 0.1);
         animation: fadeIn 0.6s ease-out 0.3s both;
     }

     .result-item:nth-child(3) {
         animation-delay: 0.4s;
     }

     .result-item:nth-child(4) {
         animation-delay: 0.5s;
     }

     .result-item:nth-child(5) {
         animation-delay: 0.6s;
     }

     .result-item:nth-child(6) {
         animation-delay: 0.7s;
     }

     .result-item:last-child {
         border-bottom: none;
         font-size: 18px;
         font-weight: 700;
         color: #02BD61;
         margin-top: 10px;
         padding-top: 15px;
         border-top: 2px solid #02BD61;
     }

     .result-label {
         font-weight: 600;
     }

     .result-value {
         font-weight: 700;
         color: #2c3e50;
     }

     .info-text {
         font-size: 13px;
         color: #666;
         margin-top: 5px;
         font-style: italic;
     }

     .currency-badge {
         display: inline-block;
         background: #02BD61;
         color: white;
         padding: 5px 15px;
         border-radius: 20px;
         font-size: 14px;
         font-weight: 600;
         margin-bottom: 20px;
     }

     .btn-calculate.calculating {
         pointer-events: none;
         opacity: 0.7;
         position: relative;
     }

     .btn-calculate.calculating:after {
         content: '';
         position: absolute;
         width: 16px;
         height: 16px;
         top: 50%;
         left: 50%;
         margin-left: -8px;
         margin-top: -8px;
         border: 2px solid #ffffff;
         border-radius: 50%;
         border-top-color: transparent;
         animation: spinner 0.6s linear infinite;
     }

     @keyframes spinner {
         to {
             transform: rotate(360deg);
         }
     }

     /* ========================================
   13. FOOTER & JOIN SECTION
   ======================================== */
     .footer {
         width: 100%;
         background: #0F2920;
         color: #fff;
         padding-top: 60px;
         padding-bottom: 50px;
     }

     .footer_title {
         color: #fff;
     }

     .footer img {
         width: 50%;
     }

     .footer_about_text {
         padding: 0;
     }

     .footer_about_text p {
         padding-top: 28px;
     }

     .footer_contact_title {
         color: #fff;
     }

     .footer_contact_line {
         color: #fff;
     }

     .footer_list li a {
         color: #fff;
     }

     .footer_social ul li a i {
         color: #fff;
     }

     .footer_social ul li a {
         color: #fff;
     }

     .footer_social ul li a i:hover {
         color: #06CC6B;
     }

     .footer_list li a:hover {
         color: #06CC6B;
     }

     .footer p {
         color: #fff;
     }

     .footer-full-content {
         margin-top: 0px;
     }

     /* Join Section */
     .join {
         position: absolute;
         top: -25%;
         left: 0;
         right: 0;
         background: transparent;
     }

     .course_search_form>div::after {
         background: #06CC6B;
         width: 96%;
         left: 6px;
     }

     .course_search_form>div {
         width: calc((100% - 284px) / 2);

     }

     .btn-margin .button {
         width: auto;
         cursor: pointer;
         width: 98%;
     }

     .join .button a {
         padding: 0 24px;
         line-height: 47px;
         font-size: 12px;
         font-weight: 600;
         color: #FFFFFF;
         text-transform: uppercase;
         white-space: nowrap;
     }

     .btn-margin .button {
         border-radius: 10px;
         border: 0;
         height: 50px;
     }

     .donation-form-card .section_subtitle p {
         color: #fff;
         text-align: center;
     }

     .join-bg {
         background-image: url("images/join bg.png");
         background-repeat: no-repeat;
         background-position: center;
         background-size: cover;
         padding: 35px 40px;
         border-radius: 20px;
     }

     .join-bg .section_title h2 {
         font-weight: 600;
         color: white;
     }

     .join-bg .course-lable {
         font-weight: 600;
         color: white;
     }

     .join-input {
         width: 40%;
         border-top-left-radius: 10px;
         border-bottom-left-radius: 10px;
         border: none;
         outline: none;
         padding: 5px 20px;
     }

     .join-btn {
         background: #E8B65D;
         border-top-right-radius: 10px;
         border-bottom-right-radius: 10px;
     }

     .join-btn:hover {
         background: #E8B65D;
     }

     .footer-newsletter input {
         width: 100%;
         border: none;
         outline: none;
         padding: 10px 15px;
         margin: 10px 0;
         border-radius: 10px;

     }

     .footer-newsletter button {
         width: 100%;
         border: none;
         outline: none;
         padding: 10px 15px;
         margin: 10px 0;
         border-radius: 10px;

     }

     /* volunteer page  start */
     .volunteer-page {
         background-color: #f8f9fa;
     }

     .section-title {
         font-size: 2rem;
         font-weight: bold;
         color: #2c3e50;
         margin-bottom: 1rem;
     }

     .section-description {
         font-size: 1.1rem;
         color: #6c757d;
         line-height: 1.8;
     }

     .volunteer-benefits {
         background: #fff;
         padding: 2rem;
         border-radius: 10px;
         box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
     }

     .benefit-item {
         padding: 0.5rem 0;
         border-bottom: 1px solid #e9ecef;
     }

     .benefit-item:last-child {
         border-bottom: none;
     }

     .opportunity-card {
         background: #fff;
         transition: transform 0.3s ease, box-shadow 0.3s ease;
     }

     .opportunity-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15) !important;
     }

     .volunteer-card {
         background: #fff;
         border-radius: 15px;
         box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
         overflow: hidden;
     }

     .card-header-custom {
         background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
         padding: 2rem;
         text-align: center;
     }

     .card-header-custom h4 {
         margin: 0;
         font-size: 1.75rem;
         font-weight: bold;
         color: white;
     }

     .card-header-custom p {
         color: white;
     }

     .section-heading {
         color: #28a745;
         font-weight: 600;
         padding-bottom: 0.5rem;
         border-bottom: 2px solid #28a745;
     }

     .required {
         color: #dc3545;
     }

     .volunteer-interests {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
         gap: 0.5rem;
     }

     .form-check {
         padding: 0.5rem;
         border-radius: 5px;
     }

     .info-box {
         background-color: #e7f3ff;
         border-left: 4px solid #0d6efd;
         padding: 1rem;
         border-radius: 5px;
     }

     .terms-text {
         text-align: center;
         font-size: 0.9rem;
         color: #6c757d;
     }

     .terms-text a {
         color: #28a745;
         text-decoration: none;
     }

     .terms-text a:hover {
         text-decoration: underline;
     }

     /* volunteer page  end*/
     /* ========================================
   14. MEDIA QUERIES
   ======================================== */

     /* ========================================
   14.1 MAX-WIDTH: 1199px (Small Desktops)
   ======================================== */
     @media(max-width: 1199px) {
         h2 {
             font-size: 28px;
             margin: 0;
         }

         .main_nav li a {
             display: block;
             font-size: 10px;
         }

         .lang-btn {
             font-size: 10px;
         }

         .nav-donate-btn {
             font-size: 10px;
         }

     }

     /* ========================================
   14.2 MAX-WIDTH: 991px (Tablets - Landscape)
   ======================================== */
     @media(max-width: 991px) {

         /* Header Reordering */
         .donate-btn-section {
             order: 1;
             margin-top: 3px;
         }

         .main_nav {
             margin-top: 1px;
         }

         .main_nav_container {
             order: 2;
             margin-right: 0;
         }

         .hamburger {
             order: 3;
             margin-top: 10px;
         }

         .header_container {
             left: 0;
             right: 0;
             transform: none;
             top: 0;
             border-radius: 0;
             width: 100%;
             max-width: none;
         }

         .header_content {
             height: 60px;
             transition: all 200ms ease;
         }

         .header_container .donate-btn-section a {
             color: white;
             font-size: 11px;
         }

         /* Home Container */
         .home_container {
             position: absolute;
             bottom: 132px;
             left: 0;
             width: 100%;
         }

         /* Featured Banner */
         .featured_background_wrapper {
             min-height: 350px;
         }

         .featured_banner_content h1 {
             font-size: 28px;
         }

         .featured_banner_content p {
             font-size: 14px;
         }

         .course_input {
             width: 96%;
         }

         /* About Section */
         .border-bottom {
             padding-bottom: 3px;
             border-bottom: 1px solid #ccc;
         }

         .about-quotes .col-sm-12,
         .about-quotes .col-md-6,
         .about-quotes .col-lg-4:last-child {
             margin-top: 30px;
         }

         /* Course Labels */
         .course-lable {
             color: #000;
             font-size: 14px;
             font-weight: 600;
         }

         /* Donation Form */
         .donate-form-bg .course_button {
             background: #02BD61;
             border-radius: 10px;
             margin-top: 34px;
             width: 100%;
         }

         /* Donation Card */
         .donate_card_bg .card-btn a {
             width: 105%;
             background-color: #f78b04;
             border-radius: 15px;
             font-weight: 600;
             padding: 10px 38px;
             border: 0;
             cursor: pointer;
             font-size: 13px;
         }

         .donate_card_bg .cardp p {
             color: white;
             font-size: 20px;
             font-weight: 700;
         }

         /* Mobile Menu */
         .menu {
             width: 40%;
             right: -100%;
             padding-left: 30px;
             padding-right: 30px;
             padding-top: 25px;
         }

         .mobile_nav_title {
             padding-left: 0px;
             padding-right: 1px;
             margin: 0px 14px;
             text-align: start;
             line-height: 26px;
             font-size: 16px;
         }

         .menu_close_container {
             position: static;
             top: 30px;
             right: 0px;
             width: 18px;
             height: 18px;
             transform-origin: center center;
             -webkit-transform: rotate(45deg);
             -moz-transform: rotate(45deg);
             -ms-transform: rotate(45deg);
             -o-transform: rotate(45deg);
             transform: rotate(45deg);
             cursor: pointer;
         }

         /* Join Section */
         .join {
             position: absolute;
             top: -32%;
             left: 0%;
             background: transparent;
             width: 100%;
         }


         .btn-margin .button {
             width: 98%;
             margin-top: 10px;
             margin-left: 4px;
         }



         /* Footer */
         .footer {
             padding-top: 60px;
         }

         .footer img {
             width: 20%;
             margin-top: 10px;
         }
     }

     /* ========================================
   14.3 MAX-WIDTH: 768px (Tablets - Portrait)
   ======================================== */
     @media(max-width: 768px) {

         /* Mobile Menu */
         .menu {
             width: 50%;
             right: -100%;
             padding-left: 30px;
             padding-right: 30px;
             padding-top: 25px;
         }

         /* Home Section */
         .home_container {
             bottom: 119px;
         }

         .index-page .home {
             width: 100%;
             height: 86vh;
         }

         .home-background {
             min-height: 92vh;
         }

         .home-background .content h1 {
             margin-top: 40px;
             color: #fff;
             font-size: 50px;
             line-height: 50px;
         }

         .home-background .content p {
             color: #fff;
             font-size: 16px;
             line-height: 28.4px;
         }

         /* Donation Form */
         .donate-form-bg {
             margin-top: -157px;
             padding: 50px 0;
             background: #E8B65D;
             background-image: url("images/donate bg.svg");
             border-radius: 10px;
         }

         /* Donation Card */
         .donate_card_bg .cardp p {
             color: white;
             font-size: 18px;
             font-weight: 700;
         }

         .donate_card_bg .card-btn a {
             width: 110%;
             padding: 10px 18px;
             font-size: 13px;
         }

         /* Program Details */
         .program-details {
             margin: 40px 0;
         }

         .course_search_form>div {
             width: 100%;
         }

         /* Join Section */
         .join {
             position: absolute;
             top: -14%;
             left: 0%;
             background: transparent;
             width: 100%;
         }

         /* Footer */
         .footer {
             padding-top: 218px;
         }

         .volunteer-interests {
             grid-template-columns: 1fr;
         }
     }

     /* ========================================
   14.4 MAX-WIDTH: 575px (Mobile Devices)
   ======================================== */
     @media(max-width: 575px) {

         /* Mobile Menu */
         .menu {
             width: 60%;
             right: -100%;
             padding-left: 30px;
             padding-right: 30px;
             padding-top: 25px;
         }

         .mobile_nav_title {
             line-height: 20px;
             font-size: 14px;
         }

         /* Home Section */
         .home_container {
             bottom: 68px;
         }

         .index-page .home {
             width: 100%;
             height: 89vh;
         }

         .home-background {
             width: 100%;
             height: 89vh;
         }

         /* Featured Banner */
         .featured_background_wrapper {
             min-height: 300px;
         }

         .featured_banner_content h1 {
             font-size: 24px;
             margin-bottom: 15px;
         }

         .featured_banner_content p {
             font-size: 13px;
             margin-bottom: 20px;
         }

         .buttons {
             flex-direction: column;
             gap: 10px;
         }

         .buttons .btn {
             width: 100%;
         }

         .tabs-rounded {
             border: 1px solid #ccc;
             border-radius: 20px;
             padding: 10px;
             margin-bottom: 79px;
         }

         /* Donation Form */
         .donate-form-bg {
             margin-top: -157px;
             padding: 40px;
             background: #E8B65D;
             background-image: url("images/donate bg.svg");
             border-radius: 10px;
         }

         .course_button {
             background: #02BD61;
             border-radius: 10px;
             width: 97%;
         }

         /* Donation Card */
         .donate_card_bg .cardp p {
             color: white;
             font-size: 17px;
             font-weight: 700;
         }

         .donate_card_bg .card-btn a {
             width: 105%;
             background-color: #D08322;
             border-radius: 15px;
             font-weight: 600;
             padding: 10px 38px;
             border: 0;
             cursor: pointer;
             font-size: 13px;
         }

         /* Notice Page */
         .notice {
             margin-top: 189px;
         }

         .notice-page .card_container {
             margin: 161px 0;
         }

         /* Donate Page */
         .donate-page .card-body {
             padding: 1.5rem !important;
         }

         .donate-page .btn-donate {
             font-size: 1.125rem;
             padding: 1rem;
     
         }

         .join {
             position: absolute;
             top: -24%;
             left: 3%;
             background: transparent;
             width: 95%;
         }

         .join-bg {
             background-image: url("images/join bg.png");
             background-repeat: no-repeat;
             background-position: center;
             background-size: cover;
             padding: 35px 40px;
             border-radius: 20px;
         }

         /* Footer */
         .footer {
             padding-top: 228px;
             padding-bottom: 50px;
         }

         .footer_about_text p {
             margin-top: 0;
             padding-top: 10px;
         }

         .footer .logo_img img {
             width: 100px;
         }
     }

     /* ========================================
   14.5 MAX-WIDTH: 460px (Small Mobile Devices)
   ======================================== */
     @media(max-width: 460px) {

         /* Mobile Menu */
         .menu {
             width: 80%;
             right: -100%;
             padding-left: 30px;
             padding-right: 30px;
             padding-top: 25px;
         }

         .menu_nav ul li {
             margin-bottom: 18px;
         }

         .menu_nav ul li a {
             font-size: 17px;
             color: #000;
             font-weight: 600;
         }

         /* Home Section */
         .index-page .home {
             width: 100%;
             height: 89vh;
         }

         .home-background {
             width: 100%;
             height: 89vh;
         }

         .home-background .btn {
             margin: 10px;
             padding: 10px 17px;
             font-size: 14px;
         }

         .home_container {
             bottom: 68px;
         }

         .home-background .content h1 {
             margin-top: -55px;
             color: #fff;
             font-size: 36px;
             line-height: 50px;
         }

         .home-background .content p {
             color: #fff;
             font-size: 14px;
             line-height: 28.4px;
         }

         /* Donation Card */
         .donate_card_bg .cardp p {
             color: white;
             font-size: 17px;
             font-weight: 700;
         }

         .donate_card_bg .card-btn a {
             width: 100%;
             border-radius: 15px;
             padding: 10px 12px;
             font-size: 13px;
             margin-left: 0 auto;
         }

         /* Donation Form */
         .donate-form-bg {
             margin-top: -214px;
             padding: 40px 0;
             background: #E8B65D;
             background-image: url("images/donate bg.svg");
             border-radius: 10px;
         }

         .course_search_form>div {
             width: 102%;
             margin-bottom: 15px;
         }

         /* Notice Page */
         .notice {
             margin-top: 189px;
         }

         /* Join Section */
         .join {
             position: absolute;
             top: -26%;
             left: 3%;
             background: transparent;
             width: 95%;
         }

         /* Footer */
         .footer {
             padding-top: 228px;
         }
     }

     /* ========================================
   END OF CSS
   ======================================== */
 </style>