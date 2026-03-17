<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>University Medical Management System</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:#f4f7fb;
}

/* Fixed Navbar */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 60px;
    background: rgba(13, 27, 42, 0.9); /* Slight transparency */
    color: white;
    backdrop-filter: blur(10px);
    position: fixed;   /* fixed position */
    top: 0;            /* always at top */
    left: 0;
    width: 100%;       /* full width */
    z-index: 1000;     /* stay above other content */
    box-shadow: 0 2px 5px rgba(0,0,0,0.2); /* optional shadow */
}

/* Navbar Links */
.navbar a {
    color: white;
    text-decoration: none;
    margin-left: 20px;
    font-weight: 500;
}

.navbar a:hover {
    color: #0d6efd; /* hover color */
}

/* For responsive: small screens */
@media screen and (max-width: 768px){
    .navbar{
        flex-direction: column;
        padding: 10px 20px;
    }

    .navbar a{
        margin: 10px 0 0 0;
    }
}

/* To prevent content hiding behind navbar */
body{
    padding-top: 70px; /* same height as navbar */
}

.logo-area{
display:flex;
align-items:center;
gap:10px;
}

.logo-area img{
height:100px;
width: 100px;
}

.menu{
display:flex;
gap:25px;

}

.menu a{
color:white;
text-decoration:none;
font-weight:500;
}

.login-btn{
background:#2a5298;
padding:8px 18px;
border-radius:6px;
}

.login-btn:hover{
background:#1e3c72;
}

/* HERO */

.hero{
height:500px;
position:relative;
display:flex;
align-items:center;
justify-content:center;
color:white;
text-align:center;
overflow:hidden;
}

.hero img{
position:absolute;
width:100%;
height:100%;
object-fit:cover;
opacity:0;
transition:1.5s;
}

.hero img.active{
opacity:1;
}

.hero::after{
content:"";
position:absolute;
width:100%;
height:100%;
background:rgba(0,0,0,0.6);
}

.hero-content{
position:relative;
z-index:2;
}

.hero-content h1{
font-size:45px;
margin-bottom:10px;
}

.hero-content p{
font-size:18px;
}

/* SERVICES */

.services{
padding:70px;
text-align:center;
}

.service-box{
display:flex;
justify-content:center;
gap:30px;
flex-wrap:wrap;
margin-top:30px;
}

.service{
background:white;
width:230px;
padding:25px;
border-radius:10px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.service h3{
color:#1e3c72;
margin-bottom:10px;
}

/* DOCTORS */

.doctors{
padding:70px;
background:#eef2f7;
text-align:center;
}

.doctor-box{
display:flex;
justify-content:center;
gap:30px;
flex-wrap:wrap;
margin-top:30px;
}

.doctor{
background:white;
padding:20px;
width:200px;
border-radius:10px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.doctor img{
width:100%;
border-radius:10px;
margin-bottom:10px;
}

/* FOOTER */

.footer{
background:#0d1b2a;
color:white;
text-align:center;
padding:20px;
}

</style>
</head>

<body>

<!-- NAVBAR -->

<div class="navbar">

<div class="logo-area">
<img src="mclogo.png">

</div>

<div class="menu">



<a href="loginpage.php" class="login-btn">Login</a>

</div>

</div>

<!-- HERO -->

<div class="hero">

<img src="lg1.jpg" class="active">
<img src="lg2.jpg">
<img src="lg3.jpg">

<div class="hero-content">

<h1>University Medical Management System</h1>
<p>Providing Efficient Healthcare Through Smart Technology</p>

</div>

</div>

<!-- SERVICES -->

<div class="services">

<h2>Our Services</h2>

<div class="service-box">

<div class="service">
<h3>Patient Records</h3>
<p>Manage patient information easily.</p>
</div>

<div class="service">
<h3>Doctor Scheduling</h3>
<p>Efficient appointment system.</p>
</div>

<div class="service">
<h3>Medical Reports</h3>
<p>Secure report management.</p>
</div>

</div>

</div>

<!-- DOCTORS -->



<!-- FOOTER -->

<div class="footer">

University Medical Center © 2026

</div>

<script>

let slides=document.querySelectorAll(".hero img");

let i=0;

function changeSlide(){

slides[i].classList.remove("active");

i=(i+1)%slides.length;

slides[i].classList.add("active");

}

setInterval(changeSlide,4000);

</script>

</body>
</html>