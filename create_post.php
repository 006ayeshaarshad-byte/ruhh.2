<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="https://www.fontspace.com/j-journey-diary-font-f108742" type="text/css"/>
    <title>Share Your Story</title>
    <link rel="stylesheet" href="create.css">
</head>
<body>

<a href="landing.html" class="logo">
    <h1>Ruhh<span>.</span></h1>
</a>

<div class="back-btn">
    <a href="dashboard.php" onclick="event.preventDefault(); const clickSound = document.getElementById('clickSound'); if(clickSound) { clickSound.play().catch(()=>{}); setTimeout(() => window.location.href='dashboard.php', 300); }"><i class="fas fa-arrow-left"></i></a>
</div>
<audio id="clickSound" src="click.mp3"></audio>
<div class="post-container">
    <h1 class="page-title">Share Your Story</h1>
    <p class="subtitle">Your post will be published anonymously</p>
    
    <form id="post-form">
        <div class="form-group">
            <textarea 
                id="post-content" 
                placeholder="Write your thoughts here...You're not alone 💜"
                maxlength="3000"
            ></textarea>
            <div class="word-count">
                <span id="word-counter">0 / 150 words</span>
            </div>
        </div>
        
        <button type="submit" class="publish-btn">
            <i class="fa-solid fa-paper-plane"></i>
            Publish Anonymously
        </button>
    </form>
</div>



<section class="spacer"></section> 









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
const textarea = document.getElementById("post-content");
const counter = document.getElementById("word-counter");
const form = document.getElementById("post-form");

textarea.addEventListener("input", () => {
    const words = textarea.value.trim().split(/\s+/).filter(w => w.length > 0).length;
    counter.textContent = `${words} / 150 words`;
    
    if (words > 150) {
        counter.style.color = "#ff6b6b";
        counter.style.fontWeight = "700";
    } else if (words > 100) {
        counter.style.color = "#f2b1d9";
        counter.style.fontWeight = "600";
    } else {
        counter.style.color = "#aaa";
        counter.style.fontWeight = "500";
    }
});

form.addEventListener("submit", (e) => {
    e.preventDefault();
    
    const content = textarea.value.trim();
    const words = content.split(/\s+/).filter(w => w.length > 0).length;
    
    if (!content) {
        showMessage("Please write something before publishing.", "error");
        return;
    }
    
    if (words > 150) {
        showMessage("Your post exceeds 150 words. Please shorten it.", "error");
        return;
    }
    
    const btn = form.querySelector("button");
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publishing...';
    
    fetch("submit_post.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ content: content })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showMessage("Your story has been published anonymously 💜", "success");
            setTimeout(() => {
                window.location.href = "community.php";
            }, 2000);
        } else {
    showMessage(data.error, "error");
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Publish Anonymously';
}
    })
    .catch(() => {
        showMessage("Connection error. Please try again.", "error");
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Publish Anonymously';
    });
});

function showMessage(text, type) {
    const existing = document.querySelector(".message-box");
    if (existing) existing.remove();
    
    const msg = document.createElement("div");
    msg.classList.add("message-box", type);
    msg.textContent = text;
    
    document.querySelector(".post-container").insertBefore(msg, form);
    
    setTimeout(() => msg.remove(), 5000);
}
</script>





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