<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "food_share");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total volunteers
$volunteerResult = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'Volunteer'");
$volunteerCount = $volunteerResult->fetch_assoc()['total'];

// Get total restaurants
$restaurantResult = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'Restaurant'");
$restaurantCount = $restaurantResult->fetch_assoc()['total'];

// Get total meals served
$mealsResult = $conn->query("SELECT SUM(people_served) AS total FROM bookings");
$mealsCount = $mealsResult->fetch_assoc()['total'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShare Connect</title>
    <link rel="icon" href="foodshare.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css?v=2">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php" style="margin-left: -20px;">
            <img src="logo.png" alt="FoodShare Logo" height="60" class="me-2"> 
            <span class="fs-3 fw-bold">FoodShare Connect</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about_foodwaste.php">Food Wastage</a></li>
                <li class="nav-item"><a class="nav-link" href="donation-history.php">Donation History</a></li>
                <li class="nav-item"><a class="nav-link" href="contribution.php">Contribution</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                <li class="nav-item"><a class="nav-link btn-login" href="login.php">Login/Register</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Flowing News & Events Bar -->
<div class="news-bar">
    <div class="news-content">
        <span>📰 New Partnership: GreenEats joins FoodShare!</span>
        <span>🍽 Volunteer Drive this Saturday - Join Us!</span>
        <span>🚚 Urgent: Food Pickup Needed at Downtown Market!</span>
        <span>⚠️ Food Safety Guidelines Updated - Stay Informed!</span>
    </div>
</div>

<style>
.news-bar {
    width: 100%;
    background: #ff9800;
    color: white;
    padding: 10px;
    font-size: 1rem;
    font-weight: bold;
    overflow: hidden;
    white-space: nowrap;
    position: relative;
}

.news-content {
    display: flex;
    gap: 50px;
    animation: newsScroll 20s linear infinite;
}

@keyframes newsScroll {
    from { transform: translateX(100%); }
    to { transform: translateX(-100%); }
}
</style>


<!-- Scrolling Image Carousel -->
<div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="1.webp" class="d-block w-100" alt="Food Donation 1">
        </div>
        <div class="carousel-item">
            <img src="2.webp" class="d-block w-100" alt="Food Donation 2">
        </div>
        <div class="carousel-item">
            <img src="3.webp" class="d-block w-100" alt="Food Donation 3">
        </div>
        <div class="carousel-item">
            <img src="4.webp" class="d-block w-100" alt="Food Donation 4">
        </div>
        <div class="carousel-item">
            <img src="5.webp" class="d-block w-100" alt="Food Donation 5">
        </div>
    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>

<!-- Welcome Section -->
<section class="welcome-section text-center">
    <div class="container">
        <h1>Welcome to FoodShare Connect</h1>
        <p>Join us in reducing food waste and fighting hunger.</p>

        <!-- Learn More Button -->
        <a href="javascript:void(0);" class="btn btn-learn" onclick="toggleLearnMore()">Learn More</a>

        <!-- Hidden Learn More Content -->
        <div id="how-it-works" class="hidden-content" style="display: none; margin-top: 20px;">
            <p>
                <strong>How FoodShare Connect Works:</strong>  
                We at <strong>FoodShare Connect</strong> are committed to reducing food waste and fighting hunger.  
                Our platform connects food donors with volunteers who distribute surplus food to those in need.
            </p>
            <ul style="list-style-type: none; padding: 0;">
                <li>✅ Restaurants and individuals post surplus food.</li>
                <li>✅ Our verified volunteers collect the food.</li>
                <li>✅ The food is distributed to shelters and the needy.</li>
            </ul>
            <p>Together, we make a difference! 💖</p>
        </div>
    </div>
</section>

<!-- JavaScript Fix -->
<script>
   function toggleLearnMore() {
    var content = document.getElementById("how-it-works");
    var button = document.querySelector(".btn-learn");

    if (!content.classList.contains("show")) {
        content.style.display = "block"; // Ensure it's visible
        content.classList.add("show"); // Expand smoothly
        button.innerText = "Show Less";
    } else {
        content.classList.remove("show"); // Collapse smoothly
        setTimeout(() => {
            content.style.display = "none"; // Hide after transition
        }, 500); // Match CSS transition duration
        button.innerText = "Learn More";
    }
}


</script>
<style>
.dashboard-stats {
    display: flex;
    flex-wrap: wrap; /* Allow wrap on small screens */
    justify-content: space-around;
    gap: 20px;
    padding: 30px;
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    margin: 30px auto;
    max-width: 1000px; /* Optional: controls container width */
}

.stat-item {
    flex: 1 1 200px; /* Responsive width */
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.05);
    text-align: center;
    font-size: 1.2rem;
    font-weight: bold;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease-in-out, transform 0.6s ease-in-out;
}

.stat-item i {
    font-size: 2rem;
    color: #ff9800;
    margin-bottom: 10px;
    display: block;
}


</style>

</style>
<div class="dashboard-stats">
    <div class="stat-item">
        <i class="fas fa-store"></i>
        <strong>Partnered Restaurants:</strong>
        <span class="count" data-count="<?= $restaurantCount ?>">0</span>
    </div>
    <div class="stat-item">
        <i class="fas fa-hands-helping"></i>
        <strong>Active Volunteers:</strong>
        <span class="count" data-count="<?= $volunteerCount ?>">0</span>
    </div>
    <div class="stat-item">
        <i class="fas fa-utensils"></i>
        <strong>Meals Served:</strong>
        <span class="count" data-count="<?= $mealsCount ?>">0</span>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let statItems = document.querySelectorAll('.count');

    statItems.forEach(stat => {
        let target = parseInt(stat.getAttribute('data-count'));
        let count = 0;
        let increment = Math.ceil(target / 100);

        let interval = setInterval(() => {
            count += increment;
            if (count >= target) {
                stat.innerText = target.toLocaleString();
                clearInterval(interval);
            } else {
                stat.innerText = count.toLocaleString();
            }
        }, 30);
    });

    // Animate card reveal
    document.querySelectorAll(".stat-item").forEach(item => {
        item.style.opacity = "1";
        item.style.transform = "translateY(0)";
    });
});
</script>


<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <h2 class="text-center">Key Features</h2>
        <div class="row text-center">
            <div class="col-md-4">
                <i class="fas fa-hand-holding-heart fa-3x feature-icon"></i>
                <h4>Easy Donations</h4>
                <p>Post surplus food easily and help those in need.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-key fa-3x feature-icon"></i>
                <h4>OTP Verification</h4>
                <p>Ensure secure and verified food collection.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-history fa-3x feature-icon"></i>
                <h4>Donation History</h4>
                <p>Track your past donations and impact on the community.</p>
            </div>
        </div>
        <div class="row text-center mt-4">
            <div class="col-md-4">
                <i class="fas fa-utensils fa-3x feature-icon"></i>
                <h4>Good Food Quality</h4>
                <p>Ensure safe and nutritious food reaches those in need.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-sync-alt fa-3x feature-icon"></i>
                <h4>Real-Time Updates</h4>
                <p>Get live status updates on food donations and bookings.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-users fa-3x feature-icon"></i>
                <h4>Community Engagement</h4>
                <p>Connect with volunteers and donors to make an impact.</p>
            </div>
        </div>
    </div>
</section>


<!-- Contact Section -->
<section id="contact" class="contact-section">
    <div class="container text-center">
        <h2>Contact Us</h2>
        <p>Email: foodshareconnect@gmail.com | Phone: +91-9999999999</p>
    </div>
</section>

<!-- Footer -->
<footer class="footer text-center">
    <p>&copy; 2024 FoodShare Connect. All Rights Reserved.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Notification Popups -->
<div id="popupNotification" class="popup">
    <span id="popupMessage"></span>
    <button onclick="closePopup()">×</button>
</div>

<style>.popup {
    position: fixed;
    bottom: 20px;
    left: 0px; /* Keep on the left side */
    background: #f44336;
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    font-size: 1rem;
    opacity: 0;
    transform: translateX(100%); /* Start off-screen to the right */
    transition: all 0.5s ease-in-out;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
}

.popup.show {
    opacity: 1;
    transform: translateX(0); /* Slide into view from right to left */
}

.popup button {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    margin-left: 10px;
}

</style>

<script>
const messages = [
    "🍱 What’s useless to you could be a lifeline for someone else.",
    "🤝 Your excess food can nourish lives. Donate now!",
    "🚫 Don't waste it — someone out there needs it.",
    "🍽️ Leftovers for you, full meals for the hungry.",
    "💖 Share food, spread love. Every bite counts."
];

let currentIndex = 0;

function showPopup(message) {
    document.getElementById("popupMessage").innerText = message;
    document.getElementById("popupNotification").classList.add("show");
    setTimeout(closePopup, 5000); // Hide after 5 seconds
}

function closePopup() {
    document.getElementById("popupNotification").classList.remove("show");
}

function startPopupCycle() {
    showPopup(messages[currentIndex]);
    currentIndex = (currentIndex + 1) % messages.length;
}

// Start notifications cycle every 30 seconds
startPopupCycle(); // Show the first one immediately
setInterval(startPopupCycle, 10000); // Then every 30 seconds
</script>


<style>
    /* Icon button */
.chatbot-toggle {
    position: fixed;
    bottom: 25px;
    right: 25px;         /* 👈 Moved to right */
    left: auto;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #6a11cb, #2575fc);
    color: white;
    font-size: 28px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    cursor: pointer;
    z-index: 1000;
    transition: transform 0.3s ease;
}
.chatbot-toggle:hover {
    transform: scale(1.1);
}

.chatbot-box {
    position: fixed;
    bottom: 100px;
    right: 25px;         /* 👈 Moved to right */
    left: auto;
    width: 320px;
    height: 450px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
    font-family: 'Segoe UI', sans-serif;
    z-index: 999;
}


/* Header */
.chatbot-header {
    background: linear-gradient(135deg, #6a11cb, #2575fc);
    color: white;
    padding: 15px;
    text-align: center;
    font-weight: bold;
    font-size: 18px;
}

/* Body */
.chatbot-content {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    font-size: 14px;
    color: #333;
}
.chatbot-content div {
    margin-bottom: 10px;
    animation: fadeInChat 0.3s ease;
}

/* Footer */
.chatbot-footer {
    display: flex;
    padding: 10px;
    border-top: 1px solid #eee;
}
.chatbot-footer input {
    flex: 1;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
.chatbot-footer button {
    margin-left: 8px;
    background: #6a11cb;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 8px 12px;
    cursor: pointer;
}

@keyframes fadeInChat {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
        <!-- New Chatbot Icon -->
        <div class="chatbot-toggle" onclick="toggleNewChatbot()">💬</div>

<!-- New Chatbot Box -->
<div class="chatbot-box" id="newChatBot">
    <div class="chatbot-header">🤖 FoodShare Assistant</div>
    <div class="chatbot-content" id="newChatContent">
        <div>👋 Hello there! How can I assist you today?</div>
    </div>
    <div class="chatbot-footer">
        <input type="text" id="newChatInput" placeholder="Type your message..." onkeypress="handleNewChatKey(event)">
        <button onclick="sendNewMessage()">Send</button>
    </div>
</div>

<script>
function toggleNewChatbot() {
    const bot = document.getElementById('newChatBot');
    bot.style.display = (bot.style.display === 'none' || bot.style.display === '') ? 'flex' : 'none';
}

function handleNewChatKey(e) {
    if (e.key === 'Enter') sendNewMessage();
}

function sendNewMessage() {
    const input = document.getElementById("newChatInput");
    const chatContent = document.getElementById("newChatContent");

    if (input.value.trim() === "") return;

    const userText = input.value.toLowerCase();
    chatContent.innerHTML += `<div><strong>You:</strong> ${input.value}</div>`;
    chatContent.innerHTML += `<div><strong>FoodShare Bot:</strong> <em>Typing...</em></div>`;
    chatContent.scrollTop = chatContent.scrollHeight;
    input.value = "";

    setTimeout(() => {
        const response = getBotResponse(userText); // ✅ Fixed function name here

        // Remove "Typing..."
        const typing = chatContent.querySelector("em");
        if (typing) typing.parentElement.remove();

        const botResponse = document.createElement("div");
        botResponse.innerHTML = `<strong>FoodShare Bot:</strong> ${response}`;
        chatContent.appendChild(botResponse);

        // Open links in new tab
        botResponse.querySelectorAll("a").forEach(link => {
            link.setAttribute("target", "_blank");
        });

        chatContent.scrollTop = chatContent.scrollHeight;
    }, 1000);
}

// Bad words and response checker
const badWords = [
    "stupid", "idiot", "dumb", "useless", "shut up", "hate you",
    "bastard", "nonsense", "trash", "shit", "fuck", "fck", "f*ck", "f**k", "f***",
    "fuck u", "fuck you", "f u", "f you", "fu", "f.u", "f.u.", "phuck", "fock",
    "asshole", "a-hole", "a$$hole", "arsehole", "fuck you asshole",
    "suck", "you suck", "suck it", "suck my", "sucka",
    "bitch", "b!tch", "biatch", "b!@#$", "btch",
    "damn", "d@mn", "darn", "dammit", "goddamn",
    "crap", "bullshit", "bullsh*t", "bullsh!t", "bs", "b.s.",
    "piss", "pissed", "pissing", "piss off",
    "whore", "w***e", "hoe", "h0e", "slut", "s!ut",
    "retard", "r3tard", "r*tard", "retarded",
    "gay" /* when used as insult */,
    "kill yourself", "kys", "go die", "go to hell",
    "die", "moron", "loser", "jerk", "freak", "douche", "douchebag",
    "weirdo", "nutjob", "maniac", "psycho", "creep", "pervert"
];

function checkBadWords(input) {
    input = input.toLowerCase();
    for (let word of badWords) {
        if (input.includes(word)) {
            return word;
        }
    }
    return null;
}

function getBotResponse(input) {
    const badWord = checkBadWords(input);
    if (badWord) {
        return `Whoa there! 😳 This is FoodShare — we help feed people, not roast them... but hey, since you said "<span style="color:red;">${badWord}</span>", I’ll kindly return the favor: "<span style="color:green;">${badWord}</span>" 😎🔥<br>
Now, are we done with the spicy words or should I get my apron on for a roast battle? 🍿🤣`;
    }

    // Smart keyword responses
    if (input.includes("food waste")) {
        return `🌍 Did you know 1.3 billion tons of food is wasted globally every year? Learn more on our <a href='about_foodwaste.php'>Food Waste</a> page.`;
    }
    if (input.includes("contribute") || input.includes("donate money")) {
        return `💖 You can donate funds to our cause at the <a href='contribution.php'>Contribution Page</a>.`;
    }
    if (input.includes("login")) {
        return `🔐 Log in to your dashboard here: <a href='login.php'>Login Page</a>.`;
    }
    if (input.includes("register") || input.includes("sign up")) {
        return `📝 Join us by registering here: <a href='register.php'>Register Page</a>.`;
    }
    if (input.includes("donate food")) {
        return `🍱 You can donate food through your restaurant dashboard. <a href='login.php'>Login</a> first.`;
    }
    if (input.includes("donation history")) {
        return `📈 Check out how people are contributing! View our <a href='donation-history.php'>Donation History</a>.`;
    }
    if (input.includes("how to donate")) {
        return `🙌 You can either donate food after logging in or contribute funds via the <a href='contribution.php'>Contribution Page</a>.`;
    }
    if (input.includes("contact") || input.includes("call") || input.includes("address")) {
        return `📝 you can connect to us using email, click here to know more <a href='contact.php'>contact Page</a>.`;
    }

    // Default replies map
    const responses = {
        "hello": "Hello! How can I assist you today? 😊",
    "hlo": "Hello! How can I assist you today? 😊",
    "hi": "Hi there! What would you like help with?",
    "hii": "Hi there! What would you like help with?",
    "hey": "Hey! Feel free to ask me anything.",
    "how are you": "I'm great! Thanks for asking. How can I help?",
    "hru": "I'm great! Thanks for asking. How can I help?",
    "who are you": "I'm your friendly FoodShare Assistant 🤖 here to guide you!",
    "wru": "I'm your friendly FoodShare Assistant 🤖 here to guide you!",
    "volunteer": "Join our amazing volunteer team here: <a href='register.php'>Register Page</a>",
    "restaurant": "If you represent a restaurant, sign up as one on our <a href='register.php'>Register Page</a>.",
    "thank you": "You're welcome! 😊do u need anything?",
    "thanks": "You're welcome! 😊do u need anything?",
    "bye": "Goodbye! Thanks for supporting our mission! 🌱",
    "help": "Sure! Ask me about food waste, donating, registering, or anything else!",
    "what's up": "Not much! Just here to help you. 😊",
    "wassup": "Hey there! All good here. Need help?",
    "how’s it going": "Everything's going well! How about you?",
    "what do you do": "I help you navigate FoodShare Connect and answer your questions!",
    "are you a bot": "Yes, but a friendly one! 🤖",
    "are you human": "Not quite! I'm a bot trained to assist you.",
    "do you eat": "Nope, I don't eat — but I help reduce food waste for those who do! 🍽️",
    "tell me a joke": "Why did the tomato blush? Because it saw the salad dressing! 😂",
    "make me laugh": "Why don’t skeletons fight each other? They don’t have the guts! 😄",
    "do you sleep": "I don’t sleep — I'm always here for you!",
    "you’re smart": "Thank you! I try my best. 😊",
    "you’re funny": "Haha, glad you think so!",
    "good job": "Thanks! That means a lot. 💪",
    "i love you": "Aww! I’m flattered. ❤️",
    "love u": "Aww! love u too. ❤️",
    "love you": "Aww! Louve you too. ❤️",
    "i like you": "You're awesome too! 😊",
    "who made you": "I was built by the developers of FoodShare Connect.",
    "are you real": "I'm real in the digital world!",
    "what can you do": "I can help you register, donate, learn about food waste, and more!",
    "how old are you": "I’m as young as the latest update! 😉",
    "tell me something": "Did you know? 1/3 of all food produced is wasted globally!",
    "how’s the weather": "I can’t check weather, but I’m always sunny here ☀️",
    "can you help me": "Of course! Ask me anything you need help with.",
    "talk to me": "Sure! Let’s chat. What’s on your mind?",
    "say something": "You're doing great. Keep shining! ✨",
    "can we be friends": "Absolutely! I'm your chatbot buddy. 🤝",
    "sing a song": "La la la 🎶 Just imagine a happy tune!",
    "dance": "If I had legs, I’d bust a move! 🕺",
    "do you like me": "Of course! You’re a great user. 😄",
    "i’m bored": "Let’s explore how you can help reduce food waste!",
    "motivate me": "You're capable of amazing things. Keep going! 💪",
    "i feel sad": "Sending you positive vibes! You're not alone. 💛",
    "you’re annoying": "Sorry! I'll do better. 🙏",
    "stop talking": "Okay! I’ll be quiet until you need me. 😊",
    "go away": "I'll be right here if you need me later!",
    "leave me alone": "Alright, I’ll give you some space. ✨",
    "thank you so much": "Anytime! 😊do u need anything?",
    "good morning": "Good morning! Ready to make a difference today?",
    "good night": "Good night! Sleep well and thank you for caring about food waste. 🌙",
    "good evening": "Good evening! Need any help?",
    "good afternoon": "Good afternoon! How can I assist you?",
    "i’m tired": "Rest is important! Take a break if you need to. 😌",
    "you’re the best": "You are! Thanks for the kind words. 😊",
    "tell me a secret": "Shh 🤫... The best way to help is to care — like you do!",
    "help me donate": "You can donate food through your dashboard or money via our <a href='contribution.php'>Contribution Page</a>.",
    "how to register": "You can sign up easily through our <a href='register.php'>Register Page</a>.",
    "i’m hungry": "Let’s help others not feel that way! Want to volunteer?",
    "i want to help": "That's amazing! You can start by registering as a volunteer or restaurant.",
    "why foodshare": "Because we believe in reducing waste and feeding people. 🌍",
    "what is foodshare connect": "It's a platform to connect surplus food with people in need. 💚",
    "how to login": "Head over to our <a href='login.php'>Login Page</a> to access your dashboard.",
    "show me something": "Sure! You can learn about <a href='about_foodwaste.php'>food waste here</a>.",
    "who needs help": "Everyone can use a little help sometimes. Especially those without access to food.",
    "what time is it": "Time to make a difference! ⏰",
    "can i donate food": "Absolutely! Restaurants can donate through their dashboard after login.",
    "can i donate money": "Yes! Visit our <a href='contribution.php'>Contribution Page</a> to donate.",
    "what is your name": "I’m FoodShare Bot, your friendly helper!",
    "do you have a name": "You can call me FoodieBot! 🍽️",
    "you’re cute": "Aww, thank you! 😊",
    "i like this": "I’m glad you’re enjoying it!",
    "can you repeat": "Sure! Just ask me again.",
    "repeat please": "Of course! What would you like me to repeat?",
    "say hi": "Hi there! 👋",
    "are you busy": "Never too busy for you!",
    "show me volunteer info": "You can register as a volunteer <a href='register.php'>here</a>!",
    "show me restaurant info": "Restaurants can register on our <a href='register.php'>Register Page</a> and donate food.",
    "do you speak other languages": "Not yet, but I hope to in the future!",
    "how do i join": "Just register through our <a href='register.php'>Register Page</a>!",
    "is this safe": "Yes, your data is handled securely on our platform.",
    "how to contact you": "You can use our <a href='contact.php'>Contact Page</a> to reach us anytime!",
    "open login": "Here's the <a href='login.php'>Login Page</a>.",
    "open register": "Here's the <a href='register.php'>Register Page</a>.",
    "open donation page": "Click here to donate: <a href='contribution.php'>Contribution Page</a>.",
    "how do i complain": "You can submit a complaint on our <a href='contact.php'>Contact Page</a>.",
    "are there volunteers": "Yes! We have many amazing volunteers helping reduce food waste!",
    "how to become volunteer": "Sign up as a volunteer <a href='register.php'>here</a> to get started.",
    "tell me about donation history": "Visit the <a href='donation-history.php'>Donation History Page</a> to see contributions made by others.",
    "goodbye": "Take care! Come back anytime!",
    "sup": "All good here! Need help with anything?",
    "yo": "Hey there! What can I help you with?",
    "nice to meet you": "Nice to meet you too! 😊",
    "how do i start": "You can start by registering on our site: <a href='register.php'>Click here to register</a>.",
    "you're cute": "Aww, thank you! You're pretty awesome too! 😄",
    "lol": "Glad I could make you laugh!",
    "haha": "😄 Laughter is always welcome here!",
    "you are smart": "Thanks! I’ve been trained well!",
    "cool": "Cool indeed 😎 What else would you like to know?",
    "awesome": "You're awesome too!",
    "great": "Glad to hear that!",
    "amazing": "You're amazing for helping support our cause!",
    "how can i help": "You can donate, volunteer, or help spread awareness about food waste!",
    "i want to donate": "That's wonderful! 💖 You can donate food or contribute funds via our platform.",
    "what is this": "This is FoodShare Connect – a platform to reduce food waste and help communities.",
    "tell me a fact": "🍌 Bananas are the most wasted fruit globally due to quick spoilage!",
    "how to use this": "Just explore our site, and let me know if you need help finding something.",
    "can i ask you something": "Of course! I'm here to help 😊",
    "can you talk": "I can chat with you all day long!",
    "you are helpful": "That means a lot, thank you! 😊",
    "you're awesome": "You're making me blush! Thanks!",
    "ok": "Cool! Let me know if you need anything.",
    "okay": "Great! I’m here whenever you’re ready.",
    "got it": "Awesome! ✅",
    "thanks bot": "You're always welcome! 🤖 do u need anything?",
    "s": "ok let's go",
    "yes": "ok let's go"
        
    };

    // Simple match if in response keys
    for (let key in responses) {
        if (input.includes(key)) return responses[key];
    }

    // Fallback
    return "🤔 I'm not sure how to respond to that... but feel free to ask about food waste, donating, registering, or anything else related to FoodShare!";
}

</script>
</body>
</html>
