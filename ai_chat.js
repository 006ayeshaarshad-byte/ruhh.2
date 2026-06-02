const chatBody        = document.querySelector(".chat-body");
const messageInput    = document.querySelector(".message-input");
const sendMessageBtn  = document.querySelector(".send-message");
const newChatBtn      = document.querySelector(".new-chat-btn");
const sidebarList     = document.querySelector(".sidebar-list");
const emojiBtn        = document.querySelector(".emoji-btn");
const charCount       = document.querySelector(".char-count");
const clickSound      = document.getElementById("clickSound");
const trashSound      = document.getElementById("trashSound");

let currentConversationId = null;
let pickerVisible = false;

// ── CHARACTER COUNTER ────────────────────────────────────────────────────────
messageInput.addEventListener("input", () => {
    const len = messageInput.value.length;
    charCount.textContent = `${len} / 1000`;
    charCount.classList.toggle("warn",  len > 800 && len <= 950);
    charCount.classList.toggle("limit", len > 950);

    // Auto-resize textarea
    messageInput.style.height = "auto";
    messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + "px";
});

// ── MARKDOWN → HTML (basic) ──────────────────────────────────────────────────
function renderMarkdown(text) {
    return text
        .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
        .replace(/\*(.*?)\*/g,     "<em>$1</em>")
        .replace(/`(.*?)`/g,       "<code style='background:#f0f0f0;padding:1px 5px;border-radius:4px;font-size:13px;'>$1</code>")
        .replace(/\n/g,            "<br>");
}

// ── WELCOME MESSAGE ──────────────────────────────────────────────────────────
function showWelcomeMessage() {
    chatBody.innerHTML = `
        <div class="message bot-message">
            <i class="fa-solid fa-robot"></i>
            <div class="message-text">
                Hey, I am luna. your AI therapist 💜<br>
                I'm here to listen to your troubles, and help you, ofcourse. 
            </div>
        </div>`;
}

// ── CREATE USER BUBBLE ───────────────────────────────────────────────────────
function createUserBubble(text) {
    const div = document.createElement("div");
    div.classList.add("message", "user-message");
    const bubble = document.createElement("div");
    bubble.classList.add("message-text");
    bubble.textContent = text;
    div.appendChild(bubble);
    return div;
}

// ── SHOW BOT RESPONSE WITH TYPING ANIMATION ──────────────────────────────────
function showBotMessage(htmlText) {
    const wrapper = document.createElement("div");
    wrapper.classList.add("message", "bot-message");
    wrapper.innerHTML = `
        <i class="fa-solid fa-robot"></i>
        <div class="message-text typing">
            <span></span><span></span><span></span>
        </div>`;
    chatBody.appendChild(wrapper);
    chatBody.scrollTop = chatBody.scrollHeight;

    setTimeout(() => {
        wrapper.innerHTML = `
            <i class="fa-solid fa-robot"></i>
            <div class="message-text">${htmlText}</div>`;
        chatBody.scrollTop = chatBody.scrollHeight;
    }, 1400);
}

// ── LOAD CONVERSATIONS IN SIDEBAR ────────────────────────────────────────────
function loadConversations(activeId = null) {
    fetch("get_conversations.php")
        .then(r => r.json())
        .then(conversations => {
            sidebarList.innerHTML = "";
            conversations.forEach(conv => {
                const li = document.createElement("li");
                li.classList.add("sidebar-item");
                if (conv.id == activeId) li.classList.add("active");

                const textSpan = document.createElement("span");
                textSpan.classList.add("sidebar-item-text");
                textSpan.textContent = conv.title;

                const trashIcon = document.createElement("i");
                trashIcon.classList.add("fa-solid", "fa-trash-can", "trash-icon");
                trashIcon.addEventListener("click", (e) => {
                    e.stopPropagation();
                    deleteConversation(conv.id);
                });

                li.appendChild(textSpan);
                li.appendChild(trashIcon);
                li.title = conv.title;
                li.dataset.id = conv.id;
                li.addEventListener("click", () => switchConversation(conv.id, li));
                sidebarList.appendChild(li);
            });
        });
}

// ── SWITCH CONVERSATION ──────────────────────────────────────────────────────
function switchConversation(conversationId, liElement) {
    currentConversationId = conversationId;
    document.querySelectorAll(".sidebar-item").forEach(el => el.classList.remove("active"));
    if (liElement) liElement.classList.add("active");
    showWelcomeMessage();

    fetch(`get_history.php?conversation_id=${conversationId}`)
        .then(r => r.json())
        .then(messages => {
            messages.forEach(msg => {
                if (msg.role === "user") {
                    chatBody.appendChild(createUserBubble(msg.message));
                } else {
                    const div = document.createElement("div");
                    div.classList.add("message", "bot-message");
                    div.innerHTML = `
                        <i class="fa-solid fa-robot"></i>
                        <div class="message-text">${renderMarkdown(msg.message)}</div>`;
                    chatBody.appendChild(div);
                }
            });
            chatBody.scrollTop = chatBody.scrollHeight;
        });
}

// ── DELETE CONVERSATION ──────────────────────────────────────────────────────
function deleteConversation(conversationId) {
    if (trashSound) { trashSound.currentTime = 0; trashSound.play(); }

    fetch("delete_conversation.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ conversation_id: conversationId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (conversationId == currentConversationId) {
                startNewConversation();
            } else {
                loadConversations(currentConversationId);
            }
        }
    });
}

// ── START NEW CONVERSATION ───────────────────────────────────────────────────
function startNewConversation() {
    messageInput.disabled = true;
    sendMessageBtn.disabled = true;

    fetch("new_conversation.php")
        .then(r => r.json())
        .then(data => {
            currentConversationId = data.conversation_id;
            showWelcomeMessage();
            loadConversations(currentConversationId);
            messageInput.disabled = false;
            sendMessageBtn.disabled = false;
            messageInput.value = "";
            messageInput.style.height = "auto";
            charCount.textContent = "0 / 1000";
            charCount.className = "char-count";
            messageInput.focus();
        });
}

// ── SEND MESSAGE ─────────────────────────────────────────────────────────────
function handleOutgoingMessage() {
    const userMessage = messageInput.value.trim();
    if (!userMessage || !currentConversationId) return;

    chatBody.appendChild(createUserBubble(userMessage));
    messageInput.value = "";
    messageInput.style.height = "auto";
    charCount.textContent = "0 / 1000";
    charCount.className = "char-count";
    chatBody.scrollTop = chatBody.scrollHeight;

    // Disable while waiting
    sendMessageBtn.disabled = true;
    messageInput.disabled = true;

    fetch("ai_chat.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: userMessage, conversation_id: currentConversationId })
    })
    .then(r => r.json())
    .then(data => {
        showBotMessage(renderMarkdown(data.reply));
        loadConversations(currentConversationId);
    })
    .catch(() => showBotMessage("Something went wrong 💔 Please try again."))
    .finally(() => {
        sendMessageBtn.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
    });
}

// ── EMOJI PICKER ─────────────────────────────────────────────────────────────
const picker = new EmojiMart.Picker({
    theme: "light",
    onEmojiSelect: (emoji) => {
        messageInput.value += emoji.native;
        messageInput.dispatchEvent(new Event("input")); // update char count
        messageInput.focus();
    }
});

emojiBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    if (!pickerVisible) {
        // Position relative to chat popup
        const chatPopup = document.querySelector(".chatbot-popup");
        const wrapper   = document.createElement("div");
        wrapper.classList.add("emoji-picker-wrapper");
        chatPopup.appendChild(wrapper);
        wrapper.appendChild(picker);
        pickerVisible = true;
    } else {
        const wrapper = document.querySelector(".emoji-picker-wrapper");
        if (wrapper) wrapper.remove();
        pickerVisible = false;
    }
});

// Close picker when clicking outside
document.addEventListener("click", (e) => {
    if (pickerVisible && !e.target.closest(".emoji-picker-wrapper") && !e.target.closest(".emoji-btn")) {
        const wrapper = document.querySelector(".emoji-picker-wrapper");
        if (wrapper) wrapper.remove();
        pickerVisible = false;
    }
});

// ── EVENT LISTENERS ──────────────────────────────────────────────────────────
messageInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        handleOutgoingMessage();
    }
});
sendMessageBtn.addEventListener("click", handleOutgoingMessage);
newChatBtn.addEventListener("click", startNewConversation);

// ── INIT ─────────────────────────────────────────────────────────────────────
startNewConversation();