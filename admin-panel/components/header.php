<?php
session_start();

// Include functions for authentication check
require_once __DIR__ . '/../../components/functions.php';

// Protect the page - redirect to login if not authenticated
protectPage();

// Get current user info
$current_user = getCurrentUser();
$admin_username = $current_user['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - <?= $page_title; ?></title>

    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <!-- fontawsome icons     -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<!-- Bootstrap cdn   -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Custom CSS-->
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
      #success-box {
        max-width: 800px;
        margin: auto;
        text-align: center;
        font-size: 18px;
        padding: 20px;
        color: #0A3622;
        background: #D1E7DD;
      }

      /* Add Role Page */
      .form-check {
        min-width: 150px;
      }
      .form-check .form-check-label {
          margin-left: 0;
      }

      /* Add Product Page */
      .img-upload-box {
        border: 1px dashed #ccc; 
        padding: 0 20px; 
        padding-top: 10px; 
        border-radius: 10px; 
        margin-bottom: 20px;
      }

      .custum-file-upload {
        border: 2px dashed #ccc;
        padding: 25px;
        max-width: 320px;
        width: 100%;
        text-align: center;
        cursor: pointer;
        border-radius: 12px;
        transition: 0.3s ease;
        display: inline-block;
        background: #f9f9f9;
        margin-bottom: 20px;
      }

      .custum-file-upload:hover {
        background-color: #eef4ff;
        border-color: #007bff;
        color: #007bff;
      }

      .custum-file-upload .icon {
        margin-bottom: 10px;
        color: #666;
      }

      .custum-file-upload .icon svg {
        width: 40px;
        height: 40px;
        fill: currentColor;
      }

      .custum-file-upload .text span {
        font-size: 15px;
        font-weight: 500;
        color: #333;
      }

      .custum-file-upload input[type="file"] {
        display: none;
      }

      .image-block {
        margin-bottom: 30px;
      }

      .image-block h4 {
        font-size: 14px;
        color: #888;
        margin-top: 5px;
      }

      .details {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
        font-size: 16px;
      }
    </style>

    <?php include 'header-style.php'; ?>

  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_navbar.php -->
      <?php include('navbar.php'); ?>
      <!-- partial -->
      <div class="  page-body-wrapper">
        
        <!-- partial:partials/_sidebar.php -->
        <?php include('sidebar.php'); ?>
        <div class="main-panel">