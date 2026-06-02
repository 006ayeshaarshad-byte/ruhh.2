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
    
    <title>My Posts</title>
    <link rel="stylesheet" href="my_posts.css">
</head>
<body>

<a href="landing.html" class="logo">
    <h1>Ruhh<span>.</span></h1>
</a>

<div class="back-btn">
    <a href="dashboard.php"  onclick="event.preventDefault(); const clickSound = document.getElementById('clickSound'); if(clickSound) { clickSound.play().catch(()=>{}); setTimeout(() => window.location.href='dashboard.php', 300); }"><i class="fas fa-arrow-left"></i></a>
</div>
<audio id="clickSound" src="click.mp3"></audio>

<div class="posts-container">
    <h1 class="page-title">My Posts</h1>
    <p class="subtitle">Your anonymous stories<i class="fa-solid fa-heart" style="color: #f2b1d9;"></i></p>
    
    <a href="create_post.php" class="btn-share">
        <i class="fa-solid fa-pen"></i> Share a New Story
    </a>
    <div id="posts-list">
        <div class="loading">
            <div class="typing">
                <span></span><span></span><span></span>
            </div>
            <p>Loading your posts...</p>
        </div>
    </div>
</div>

<script>
loadMyPosts();

function loadMyPosts() {
    fetch("get_my_posts.php")
        .then(res => res.json())
        .then(posts => {
            const container = document.getElementById("posts-list");
            
            if (posts.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fa-solid fa-pen-nib"></i>
                        <p>You haven't shared any stories yet.</p>
                        <a href="create_post.php" class="btn-create">Share Your First Story</a>
                    </div>`;
                return;
            }
            
            container.innerHTML = "";
            
            posts.forEach(post => {
                const postDiv = document.createElement("div");
                postDiv.classList.add("post");
                postDiv.dataset.id = post.id;
                
                const date = new Date(post.created_at);
                const dateStr = date.toLocaleDateString("en-US", { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                postDiv.innerHTML = `
                    <div class="post-header">
                        <span class="post-date">
                            <i class="fa-solid fa-calendar"></i> ${dateStr}
                        </span>
                        <button class="delete-btn" onclick="deletePost(${post.id})">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                    <div class="post-content">${post.content}</div>
                `;
                
                container.appendChild(postDiv);
            });
        })
        .catch(() => {
            document.getElementById("posts-list").innerHTML = `
                <div class="error-state">
                    <p>Could not load your posts. Please try again.</p>
                </div>`;
        });
}

function deletePost(postId) {
   
     const trash = document.getElementById("trashSound");
    if (trash) {
        trash.currentTime = 0;
        trash.play(); 
    } 
    fetch("delete_post.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ post_id: postId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Remove post with animation
            const postEl = document.querySelector(`.post[data-id="${postId}"]`);
            postEl.style.opacity = "0";
            postEl.style.transform = "translateX(20px)";
            setTimeout(() => {
                postEl.remove();
                
                // Check if empty now
                const remaining = document.querySelectorAll(".post").length;
                if (remaining === 0) {
                    loadMyPosts(); // Reload to show empty state
                }
            }, 300);
        } else {
            alert("Could not delete post. Please try again.");
        }
    })
    .catch(() => {
        alert("Connection error. Please try again.");
    });
}
</script>















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
















 <audio id="trashSound" src="trash.mp3"></audio>
</body>
</html>