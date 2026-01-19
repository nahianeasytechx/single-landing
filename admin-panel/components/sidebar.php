<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile"> <a href="" class="nav-link">
        <div class="nav-profile-image"> <img src="img/admin.jpg" alt="profile" /> <span class="login-status online"></span> <!--change to offline or busy as needed--> </div>
        <div class="nav-profile-text d-flex flex-column"> <span class="font-weight-bold mb-2">Name</span> <span class="text-secondary text-small">Role</span> </div> <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a> </li>
    <li class="nav-item"> <a class="nav-link" href="index.php"> <span class="menu-title">Dashboard</span> <i class="mdi mdi-home menu-icon"></i> </a> </li>


    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#product" aria-expanded="false" aria-controls="product">
        <span class="menu-title">Product</span>
        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
      </a>
      <div class="collapse" id="product">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="create-product.php">Create Product</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="add-product-sets.php">Add Sets Details </a>
          </li>
        </ul>
      </div>
    </li>

        <li class="nav-item"> <a class="nav-link" href="create-template.php"> <span class="menu-title">Create Landing Page</span> <i class="mdi mdi-home menu-icon"></i> </a> </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#orders" aria-expanded="false" aria-controls="orders">
        <span class="menu-title">Orders</span>
        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
      </a>
      <div class="collapse" id="orders">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="add-activity.php">Create Order</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="all-activities.php">All Orders </a>
          </li>
        </ul>
      </div>
    </li>
  </ul>
</nav>