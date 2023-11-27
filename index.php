<!---
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MyLibro - Virtual Library Management System!</title>
    //Link for Tab ICON
    <link rel="icon" type="image/x-icon" href="/LibMSv1/resources/images/logov1.png">
    //Link for Bootstrap
    <link rel="stylesheet" type="text/css" href="/LibMSv1/resources/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/LibMSv1/resources/bootstrap/js/bootstrap.min.js"></script>
    //Link for CSS File
    <link rel="stylesheet" type="text/css" href="/LibMSv1/main/css/index.css">
    //Link for Font Awesome Icons
    <link rel="stylesheet" href="/LibMSv1/resources/icons/fontawesome-free-6.4.0-web/css/all.css">
    //Link for Google Font
    <link rel="stylesheet" href="/LibMSv1/resources/fonts/fonts.css"/>
-->

<?php
exec("python3 qr_codes_import.py");
?>

<!DOCTYPE html>
<html lang="en">
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!--Link for Tab ICON-->
  <link rel="icon" type="image/x-icon" href="#">
  <!--Link for Tab ICON-->
  <link rel="icon" type="image/x-icon" href="/LibMS/resources/images/logov1.png">
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Welcome to MyLibro - Pasig City Library Virtual Library Management</title>
  <!-- slider stylesheet -->
  <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />
  <!-- font wesome stylesheet -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="/LibMS/css/bootstrap.css" />
  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="/LibMS/css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="/LibMS/css/responsive.css" rel="stylesheet" />
</head>

<body>
  <div class="hero_area">
    <!-- header section strats -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container pt-3">
          <a class="navbar-brand mr-5" href="index.html">
            <img src="/LibMSv1/resources/images/logov1.png" alt="">
            <span>
              MyLibro
            </span>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="d-flex ml-auto flex-column flex-lg-row align-items-center">
              <ul class="navbar-nav  ">
                <li class="nav-item active">
                  <a class="nav-link" href="index.html">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="about.html"> About </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="service.html"> Service </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="contact.html">Contact us</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="/LibMS/main/login.php">Log In</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="/LibMS/main/signup.php/">Sign Up</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      </div>
    </header>
    <!-- end header section -->
    <!-- slider section -->
    <section class=" slider_section position-relative">
      <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="container">
              <div class="row">
                <div class="col-md-7">
                  <div class="detail-box">
                    <div>
                      <h1>
                        Welcome To <br>
                        <span>
                          Pasig City Library Virtual Library Management System for Pasig City Library
                        </span>
                      </h1>
                      <p>
                        “The public library is where place and possibility meet.” ― Stuart Dybek
                      </p>
                      <div class="btn-box">
                        <a href="" class="btn-1">
                          Contact Us
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!---
        <div class="custom_carousel-control">
          <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="sr-only">Next</span>
          </a>
        </div>-->
      </div>
    </section>
    <!-- end slider section -->
  </div>

  <!-- about section -->

  <section class="about_section layout_padding">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="detail-box">
            <div class="heading_container">
              <h2>
                About Us
              </h2>
            </div>
            <p>
              Pasig City Hall Library has a collection of over 2,000 books, serials, images, and 
              documents relating to Pasig City history. Pasig City library has been an essential provider 
              of free books, information, ideas, and education for all Pasigueños for more than 5 years. 
              Founded in 2018, PCHL (PASIG CITY HALL LIBRARY) promotes access to information, as well as 
              spaces for discussion and collaborations, bringing together an extraordinary richness of 
              resources and opportunities available to all.
            </p>
            <a href="">
              Get Started
            </a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="img-box">
            <img src="/LibMS/resources/images/lib1.png" alt="">
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end about section -->
  <div class="body_bg layout_padding">

    <!-- service section -->

    <section class="service_section ">
      <div class="container">
        <div class="heading_container">
          <h2>
            At Your Service
          </h2>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="box">
              <div class="img-box">
                <img src="/LibMS/resources/images/s-1.png" alt="">
              </div>
              <h4>
                Free Account
              </h4>
              <p>
                A "free account" for a library user's typically refers to a membership that allows individuals to use the library's 
                services and borrow books and other materials without any cost or fees. When you sign up for a free library 
                account, you become a member of the library and gain access to its resources and facilities.
              </p>
              <a href="">
                Read More
              </a>
            </div>
          </div>
          <div class="col-md-6">
            <div class="box align-items-end align-items-md-start text-right text-md-left">
              <div class="img-box">
                <img src="/LibMS/resources/images/s-2.png" alt="">
              </div>
              <h4>
                User Friendly
              </h4>
              <p>
                refers to the quality of a product, system, or interface that is designed with the user's needs, 
                preferences, and ease of use in mind. A user-friendly product is intuitive, easy to navigate, and
                requires minimal effort for users to accomplish their intended tasks or goals. The goal of creating 
                user-friendly products is to enhance the user experience and make interactions more efficient, 
                enjoyable, and accessible for a wide range of users, including those with little to no technical expertise.
              </p>
              <a href="">
                Read More
              </a>
            </div>
          </div>
        </div>
    </section>

    <!-- end client section -->

  </div>
  <!-- info section -->

  <section class="info_section layout_padding">
    <div class="footer_contact">
      <div class="heading_container">
        <h2>
          Contact Us
        </h2>
      </div>
      <div class="box">
        <a href="https://www.google.com/maps/search/pasig+city+library/@14.5607701,121.0757926,1013m/data=!3m1!1e3?entry=ttu" class="img-box">
          <img src="/LibMSv1/resources/images/location.png" alt="" class="img-1">
          <img src="/LibMSv1/resources/images/location-o.png" alt="" class="img-2" href="">
        </a>
        <a href="https://www.facebook.com/PasigLibrary" class="img-box">
          <img src="/LibMSv1/resources/images/call.png" alt="" class="img-1">
          <img src="/LibMSv1/resources/images/call-o.png" alt="" class="img-2">
        </a>
        <a href="https://mail.google.com/mail/u/0/#inbox?compose=GTvVlcSBpgPdxCfdgshTPZFSZzVlzzCGFdGbwGgZHMLrpNHBztwJzlcMknSSmlNqmKjgfzPcbXvSL" class="img-box">
          <img src="/LibMSv1/resources/images/envelope.png" alt="" class="img-1">
          <img src="/LibMSv1/resources/images/envelope-o.png" alt="" class="img-2">
        </a>
      </div>
    </div>


  </section>


  <!-- end info section -->

  <!-- footer section -->
  <section class="container-fluid footer_section">
    <p>
      Copyright &copy; 2023 All Rights Reserved By
      <a>MyLibro - Online Virtual Library Management for Pasig City Library</a>
    </p>
  </section>
  <!-- footer section -->

  <script type="text/javascript" src="/LibMS/resources/js/jquery-3.4.1.min.js"></script>
  <script type="text/javascript" src="/LibMS/resources/js/bootstrap.js"></script>

</body>
</html>