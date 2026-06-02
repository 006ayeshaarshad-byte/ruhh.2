<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
 
    <link rel="stylesheet" href="dashboard.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;900&display=swap" rel="stylesheet">
</head>

<body>
<main>



<div class="nav-overlay">

<a href="landing.html" class="logo" >
  <h1>Ruhh<span>.</span> </h1>
</a>

 <div class= "container">
    <div class="nav-item  write-post"><a href="create_post.php">
      <i class="fa-solid fa-pen-to-square" style="color: #f2b1d9;"></i>
      <span> WRITE POST </span> 
      </a> 
      </div>
    <div class="nav-item community-post"><a href="community.php">
      <i class="fa-solid fa-users" style="color: #f2b1d9;"></i> 
      <span> COMMUNITY POST </span>
    </a></div>
    <div class="nav-item AI-chat"><a href="ai_chat.html">
      <i class="fa-solid fa-comment-dots" style="color: #f2b1d9;"></i>
      <span> AI CHAT </span>
      </a></div>
    <div class="nav-item random-quote"><a href="quote.html">
      <i class="fa-solid fa-quote-left" style="color: #f2b1d9;"></i>
      <span> Quote Generator </span>
      </a></div>
    <div class="nav-item blogs"><a href="my_posts.php">
      <i class="fa-solid fa-blog" style="color: #f2b1d9;"></i>
      <span> MY POSTS </span>
      </a></div>
<div class="nav-item journal"><a href="journal.php">
  <i class="fa-solid fa-book-open" style="color: #f2b1d9;"></i> 
  <span> JOURNAL </span>
</a></div>

    </div>
</div>


















<div class="top-right-links">
  <a href="about.html">About Us</a>
  <div class="dropdown">
    <a href="blogs.html" class="dropdown-toggle">Blogs <i class="fas fa-chevron-down"></i></a>
    <div class="dropdown-menu">
      <a href="blogs.html#depression"><i class="fa-solid fa-cloud-rain"></i> Depression</a>
      <a href="blogs.html#anxiety"><i class="fa-solid fa-heart-pulse"></i> Clinical Anxiety</a>
      <a href="blogs.html#bipolar"><i class="fa-solid fa-bolt"></i> Bipolar Disorder</a>
      <a href="blogs.html#ptsd"><i class="fa-solid fa-user-shield"></i> PTSD</a>
      <a href="blogs.html#autism"><i class="fa-solid fa-puzzle-piece"></i> Autism</a>
    </div>
  </div>
 
<!-- Music Toggle -->
 
    <label class="switch">
        <input type="checkbox" class="toggle" id="music-toggle">
        <span class="slider"></span>
    </label> 
</div> 



 














</main>





<audio id="background-music" loop>
    <source src="music.mp3" type="audio/mpeg">
</audio>

<script src="music_landing.js"></script> 







<section class="spacer"></section> 
<!--
<image src="grid.jpg" alt="grid"></a>

<a href="logout.php">Logout</a>
-->

<footer class="footer">
  <div class="waves">
    <div class="wave" id="wave1"></div>
    <div class="wave" id="wave2"></div>
    <div class="wave" id="wave3"></div>
    <div class="wave" id="wave4"></div>
  </div>

  <div class="footer-links">
    <h3>Quick Links</h3>
    <a href="about.html">About Us</a>
    <a href="blogs.html">Blogs</a>
    <a href="helplines.html">Helpline</a>
  </div>

  <div class="footer-center">
    <div class="footer-top">
      <h3>Join Our Community</h3>
    </div>
    <form class="sub" id="subscribe-form">
      <input type="email" name="email" id="email-input" placeholder="Enter your email" required>
      <button type="submit"><i class="fas fa-paper-plane"></i></button>
    </form>
    <p id="subscribe-msg" style="display:none; color:#a22fb3; font-size:13px; margin-top:8px; font-family:monospace;"></p>
    <div class="socials">
      <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#"><i class="fa-brands fa-twitter"></i></a>
      <a href="#"><i class="fa-brands fa-instagram"></i></a>
    </div>
  </div>

  <div class="footer-bottom">
    <p>2026 Ruhh. All Rights Reserved.</p>
  </div>
</footer>











<script>
document.getElementById("subscribe-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const email = document.getElementById("email-input").value.trim();
    const msg = document.getElementById("subscribe-msg");
    const btn = this.querySelector("button");

    if (!email) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch("subscribe.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: email })
    })
    .then(res => res.json())
    .then(data => {
        msg.style.display = "block";
        if (data.success) {
            msg.style.color = "#a22fb3";
            msg.textContent = "💜 Thank you! You're now part of our community.";
            document.getElementById("email-input").value = "";
        } else {
            msg.style.color = "#ff6b6b";
            msg.textContent = "Something went wrong. Please try again.";
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        setTimeout(() => { msg.style.display = "none"; }, 4000);
    })
    .catch(() => {
        msg.style.display = "block";
        msg.style.color = "#ff6b6b";
        msg.textContent = "Connection error. Please try again.";
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        setTimeout(() => { msg.style.display = "none"; }, 4000);
    });
});
</script>

</body>
</html>
