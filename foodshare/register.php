<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email is blocked
    $check_email_stmt = $conn->prepare("SELECT email, blocked FROM users WHERE email = ?");
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $check_email_stmt->store_result();

    if ($check_email_stmt->num_rows > 0) {
        $check_email_stmt->bind_result($db_email, $blocked);
        $check_email_stmt->fetch();

        if ($blocked) {
            die("This email is blocked and cannot be used for registration.");
        }
    }

    // Validate role to avoid any invalid entries
    $allowed_roles = ['Admin', 'Volunteer', 'Restaurant'];
    if (!in_array($role, $allowed_roles)) {
        die("Invalid role selected.");
    }

    // Check if email already exists
    $check_email_stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $check_email_stmt->store_result();

    if ($check_email_stmt->num_rows > 0) {
        echo "<script>alert('Email already registered! Please use a different email.'); window.location.href='register.php';</script>";
        exit();
    } else {
        // Assign prefix based on role
        $prefix = $role === 'Admin' ? '111' : ($role === 'Volunteer' ? '002' : '003');
        $random_number = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
        $unique_id = $prefix . $random_number;

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into the database
        $stmt = $conn->prepare("INSERT INTO users (unique_id, role, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $unique_id, $role, $email, $hashed_password);

        if ($stmt->execute()) {
            header("Location: registration_success.php?unique_id=$unique_id");
            exit();        
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check_email_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShare Connect</title>
    <link rel="icon" href="foodshare.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="register.css?v=3">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
                <li class="nav-item"><a class="nav-link" href="about_foodwaste.html">Food Wastage</a></li>
                <li class="nav-item"><a class="nav-link" href="donation-history.php">Donation History</a></li>
                <li class="nav-item"><a class="nav-link" href="contribution.php">Contribution</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                <li class="nav-item"><a class="nav-link btn-login" href="login.php">Login/Register</a></li>
            </ul>
        </div>
    </div>
</nav>


    <!-- Registration Form -->
    <div class="container">
        <h2>Register</h2>
        <form method="POST">
            <label>Select Role:</label>
            <div class="role-options">
                <input type="radio" id="volunteer" name="role" value="Volunteer" required><label for="volunteer">Volunteer</label>
                <input type="radio" id="restaurant" name="role" value="Restaurant"><label for="restaurant">Restaurant | Individual</label>
            </div>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Register</button>
        </form>
    </div>

    <style>
.popup {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #f44336;
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    font-size: 1rem;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.5s ease-in-out;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
}

.popup.show {
    opacity: 1;
    transform: translateX(0);
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
function showPopup(message) {
    document.getElementById("popupMessage").innerText = message;
    document.getElementById("popupNotification").classList.add("show");
    setTimeout(closePopup, 5000);
}

function closePopup() {
    document.getElementById("popupNotification").classList.remove("show");
}

// Encouraging restaurants to donate
function showEncouragePopup() {
    document.getElementById("popupEncourage").classList.add("show");
    setTimeout(closeEncouragePopup, 7000);
}

function closeEncouragePopup() {
    document.getElementById("popupEncourage").classList.remove("show");
}

// Auto-trigger the notifications (replace with real-time API calls)
setTimeout(() => showPopup("🔔 New Volunteer Opportunity: Food Pickup Needed!"), 3000);
setTimeout(() => showEncouragePopup(), 10000);
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
