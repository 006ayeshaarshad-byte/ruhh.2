
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Journal — Ruhh</title>
    <link rel="stylesheet" href="journal.css">
</head>
<body>

<a href="landing.html" class="logo"><h1>Ruhh<span>.</span></h1></a>

<div class="back-btn">
    <a href="dashboard.php" onclick="event.preventDefault(); const clickSound = document.getElementById('clickSound'); if(clickSound) { clickSound.play().catch(()=>{}); setTimeout(() => window.location.href='dashboard.php', 300); }"><i class="fas fa-arrow-left"></i></a>
</div>

<audio id="clickSound" src="click.mp3"></audio>

<h1 class="page-title">Journal<span>.</span></h1>

<div class="container">
    <div class="note-container">
        <button id="addnote">
            <i class="fa-regular fa-pen-to-square"></i>
            <span style="font-size:14px; font-weight:600;">Add Entry</span>
        </button>
    </div>
    <div class="newnotes"></div>
</div>

<div class="addform" id="addForm">
    <div class="head">
        <h3>New Journal Entry</h3>
        <button class="icon">
            <i class="fa-regular fa-circle-xmark" style="color:rgb(194,178,237); font-size:28px;"></i>
        </button>
    </div>
    <div class="inputs">
        <p>Title</p>
        <input type="text" id="entryTitle" placeholder="Give your entry a label..." required>
    </div>
    <div class="inputs">
        <p>Date</p>
        <input type="date" id="entryDate" required>
    </div>
    <div class="inputs">
        <p>Thoughts</p>
        <textarea id="entryContent" rows="5" placeholder="Write away your thoughts... 💜" required></textarea>
    </div>
    <div class="inputs">
        <p>How are you feeling?</p>
        <select id="entryMood">
            <option value="happy">(˶˃ ᵕ ˂˶) Happy</option>
            <option value="loved">(´｡• ◡ •｡`)❤︎ Loved</option>
            <option value="excited">₍₍⚞(˶˃ ꒳ ˂˶)⚟⁾⁾ Excited</option>
            <option value="grateful"> (ㅅ´ ˘ `) Grateful</option>
            <option value="confused"> (´･_･`) Confused</option>
            <option value="hug"> (つ｡˃ ᵕ ˂)つ Needs a Hug</option>
            <option value="annoyed"> (￣へ￣) Annoyed</option>
            <option value="sad"> (╥﹏╥) Sad</option>
            <option value="angry"> ( ,,⩌\'︿\'⩌ꐦ,,) Angry</option>
        </select>
    </div>
    <br>
    <button id="addbtn">Save Journal Entry</button>
</div>

<div class="viewNote" id="viewNote">
    <div class="viewContent">
        <button id="closeView" class="icon">
            <i class="fa-regular fa-circle-xmark" style="color:rgb(194,178,237); font-size:28px;"></i>
        </button>
        <h2 id="viewTitle"></h2>
        <p id="viewDate"></p>
        <p id="viewMood"></p>
        <div id="viewText"></div>
    </div>
</div>
<div class= "spacer1"></div>

<div class="mood-tracker-section">
    <h2>My Mood Graph<i class="fa-solid fa-heart" style="color: #f2b1d9;"></i> </h2>
    <p class="tracker-sub">Your Past Mood Based On Your Notes. Take Care Of Your Mind</p>

    <div class="mood-graph-panel">
        <h3>Mood Flow Graph</h3>
        <div class="graph-wrapper">
            <div class="graph-y-axis">
                <span title="Excited">🤩</span>
                <span title="Loved">🥰</span>
                <span title="Happy">😊</span>
                <span title="Grateful">😇</span>
                <span title="Confused">😕</span>
                <span title="Needs a Hug">🥺</span>
                <span title="Annoyed">😑</span>
                <span title="Sad">😭</span>
                <span title="Angry">😡</span>
            </div>
            <div class="canvas-container">
                <svg id="moodGraphSvg" viewBox="0 0 1000 380" preserveAspectRatio="none">
                    <path id="graphLine" d="" fill="none" stroke-width="4" stroke-linecap="round"></path>
                </svg>
                <div id="graphNodesContainer"></div>
            </div>
        </div>
        <div class="graph-x-axis" id="graphXAxis"></div>
    </div>

   
</div>

<div class="spacer"></div>

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
        <a href="helplines.html">Helplines</a>
    </div>
    <div class="footer-center">
        <div class="footer-top"><h3>Join Our Community</h3></div>
        <form class="sub" id="subscribe-form">
            <input type="email" id="email-input" placeholder="Enter your email" required>
            <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
        <p id="subscribe-msg" style="display:none; color:#a22fb3; font-size:13px; margin-top:8px;"></p>
        <div class="socials">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
        </div>
    </div>
    <div class="footer-bottom"><p>2026 Ruhh. All Rights Reserved.</p></div>
</footer>

<script>
document.getElementById("subscribe-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const email = document.getElementById("email-input").value.trim();
    const msg = document.getElementById("subscribe-msg");
    const btn = this.querySelector("button");
    if (!email) return;
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    fetch("subscribe.php", { method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({email}) })
    .then(r=>r.json()).then(data=>{
        msg.style.display="block";
        msg.style.color = data.success ? "#a22fb3" : "#ff6b6b";
        msg.textContent = data.success ? "💜 Thank you! You're now part of our community." : "Something went wrong.";
        if(data.success) document.getElementById("email-input").value="";
        btn.disabled=false; btn.innerHTML='<i class="fas fa-paper-plane"></i>';
        setTimeout(()=>msg.style.display="none",4000);
    });
});
</script>

<script src="journal.js"></script>
</body>
</html>

