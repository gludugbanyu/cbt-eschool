<style>
#chatbotCBT {
  font-family: "Segoe UI", sans-serif;
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 320px;
  max-height: 500px;
  display: flex;
  flex-direction: column;
  background: white;
  border-radius: 18px;
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  overflow: hidden;
  z-index: 9999;
  border: 1px solid #e0e0e0;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

#chatbotCBT.minimized {
  max-height: 48px;
  overflow: visible;
}

#chatbotCBT.closed {
  opacity: 0;
  pointer-events: none;
  transform: translateY(100px);
  transition: opacity 0.3s ease, transform 0.3s ease;
}

#chatbotCBT .header {
  background: linear-gradient(135deg,rgb(76, 96, 175),rgb(15, 47, 149));
  color: white;
  padding: 12px 16px;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  user-select: none;
}

#chatbotCBT .header i {
  font-size: 18px;
  cursor: default;
}

#chatbotCBT .header .btn-minimize,
#chatbotCBT .header .btn-close {
  background: transparent;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
  padding: 0 6px;
  line-height: 1;
  user-select: none;
}

#chatbotCBT .header .btn-minimize:hover,
#chatbotCBT .header .btn-close:hover {
  color: rgb(80, 64, 174);
}

#chatbotCBT .body {
  flex: 1;
  padding: 12px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 8px;
  background: #f9f9f9;
}

#chatbotCBT .footer {
  display: flex;
  border-top: 1px solid #eee;
  background: #fff;
}

#chatbotCBT input {
  flex: 1;
  padding: 10px 12px;
  border: none;
  font-size: 14px;
  outline: none;
}

#chatbotCBT button.send-btn {
  background: transparent;
  border: none;
  padding: 0 14px;
  font-size: 18px;
  color: #4CAF50;
  cursor: pointer;
}

.bot, .user {
  max-width: 80%;
  padding: 10px 14px;
  border-radius: 18px;
  font-size: 14px;
  line-height: 1.4;
  white-space: pre-wrap;
}

.bot {
  background: #ffffff;
  align-self: flex-start;
  border: 1px solid #e0e0e0;
}

.user {
  background: #dcf8c6;
  align-self: flex-end;
}

#btnOpenChatbot {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: rgb(54, 62, 144);
  color: white;
  border-radius: 50%;
  width: 48px;
  height: 48px;
  font-size: 24px;
  display: none;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  z-index: 10000;
  user-select: none;
  transition: background-color 0.3s ease;
}

#btnOpenChatbot.new-message::after {
  content: "";
  position: absolute;
  top: 8px;
  right: 8px;
  width: 10px;
  height: 10px;
  background: red;
  border-radius: 50%;
  box-shadow: 0 0 4px red;
}

#btnOpenChatbot:hover {
  background-color: rgb(102, 88, 208);
}

/* Typing animation */
.typing-dots {
  display: inline-block;
}

.typing-dots span {
  display: inline-block;
  animation: blink 1.2s infinite;
  font-weight: bold;
  font-size: 18px;
  opacity: 0.2;
}

.typing-dots span:nth-child(1) {
  animation-delay: 0s;
}
.typing-dots span:nth-child(2) {
  animation-delay: 0.2s;
}
.typing-dots span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes blink {
  0%, 80%, 100% { opacity: 0.2; }
  40% { opacity: 1; }
}
#typingIndicator {
  display: none;
  align-self: flex-start;
  padding: 10px 14px;
  font-size: 14px;
  color: #666;
}

.typing-dots {
  display: inline-block;
  font-weight: bold;
  font-size: 16px;
  letter-spacing: 3px;
  animation: blink 1s steps(1) infinite;
}

.typing-dots span {
  animation: blink 1.2s infinite;
  opacity: 0.3;
}

.typing-dots span:nth-child(1) { animation-delay: 0s; }
.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes blink {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 1; }
}
/* =========================================
   DARK MODE – CHATBOT CBT
========================================= */

.dark-mode #chatbotCBT {
    background: #263445 !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.4) !important;
}

.dark-mode #chatbotCBT .body {
    background: #1f2a38 !important;
}

.dark-mode #chatbotCBT .footer {
    background: #222e3c !important;
    border-top: 1px solid rgba(255,255,255,0.08) !important;
}

.dark-mode #chatbotCBT input {
    background: #2b394a !important;
    color: #ffffff !important;
}

.dark-mode #chatbotCBT input::placeholder {
    color: #94a3b8 !important;
}

.dark-mode #chatbotCBT button.send-btn {
    color: #6ea8fe !important;
}

/* Bubble BOT */
.dark-mode #chatbotCBT .bot {
    background: #2b394a !important;
    color: #e4e6eb !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
}

/* Bubble USER */
.dark-mode #chatbotCBT .user {
    background: #3a4d63 !important;
    color: #ffffff !important;
}

/* Typing indicator */
.dark-mode #typingIndicator {
    color: #cfd8e3 !important;
}

/* Floating button */
.dark-mode #btnOpenChatbot {
    background: #3a4d63 !important;
}

.dark-mode #btnOpenChatbot:hover {
    background: #4a617d !important;
}
</style>

<div id="chatbotCBT" class="minimized closed" role="dialog" aria-label="Chatbot Asisten CBT">
  <div class="header">
    <i class="fa-solid fa-robot"></i> Asisten CBT
    <button class="btn-minimize" onclick="event.stopPropagation(); minimizeChatbot()">—</button>
    <button class="btn-close" onclick="event.stopPropagation(); closeChatbot()">✕</button>
  </div>
  <div class="body" id="cbtBody" style="display:none;">
    <!-- indikator animasi -->
    <div id="typingIndicator" class="bot">
      <span class="typing-dots"><span>.</span><span>.</span><span>.</span></span>
    </div>
  </div>
  <div class="footer" style="display:none;">
    <input type="text" id="cbtInput" placeholder="Ketik pertanyaan..." onkeydown="if(event.key==='Enter'){sendCBT()}" />
    <button class="send-btn" onclick="sendCBT()">➤</button>
  </div>
</div>

<div id="btnOpenChatbot" onclick="openChatbot()" onkeydown="if(event.key==='Enter' || event.key===' ') openChatbot()">
  <i class="fa-solid fa-robot"></i>
</div>

<script>
const chatbot = document.getElementById("chatbotCBT");
const btnOpen = document.getElementById("btnOpenChatbot");
const body = chatbot.querySelector(".body");
const footer = chatbot.querySelector(".footer");
const input = document.getElementById("cbtInput");
const typing = document.getElementById("typingIndicator");

let faqCBT = {};

fetch("get_faq.php")
  .then(res => res.json())
  .then(data => {
    faqCBT = data;
    if (!welcomed) {
      welcomeMessage();
      welcomed = true;
    }
  });

// Fungsi untuk menambahkan pesan
function appendCBT(text, sender, delay = 0) {
  if (sender === "bot" && delay > 0) {
    typing.style.display = "block";
    setTimeout(() => {
      typing.style.display = "none";
      _append(text, sender);
    }, delay);
  } else {
    _append(text, sender);
  }
}

function _append(text, sender) {
  const div = document.createElement("div");
  div.className = sender;
  div.innerHTML = text.replace(/\n/g, "<br>");
  body.insertBefore(div, typing); // sisipkan sebelum typing
  body.scrollTop = body.scrollHeight;
  if (sender === "bot" && chatbot.classList.contains("closed")) {
    showNewMessageNotif();
  }
}

function welcomeMessage() {
  const welcomeText = `Halo! 👋 Saya Asisten CBT.<br>Kamu bisa tanya hal-hal berikut:<br>` +
    Object.keys(faqCBT).map(k => `- ${k}`).join("<br>");
  appendCBT(welcomeText, "bot", 500);
}

// Fuzzy Matching
function similarity(str1, str2) {
  str1 = str1.toLowerCase();
  str2 = str2.toLowerCase();
  let longer = str1.length > str2.length ? str1 : str2;
  let shorter = str1.length > str2.length ? str2 : str1;
  let longerLength = longer.length;
  if (longerLength === 0) return 1.0;
  return (longerLength - levenshtein(longer, shorter)) / longerLength;
}

function levenshtein(a, b) {
  const matrix = Array.from({ length: b.length + 1 }, (_, i) => [i]);
  for (let j = 0; j <= a.length; j++) matrix[0][j] = j;
  for (let i = 1; i <= b.length; i++) {
    for (let j = 1; j <= a.length; j++) {
      matrix[i][j] = Math.min(
        matrix[i - 1][j - 1] + (a[j - 1] === b[i - 1] ? 0 : 1),
        matrix[i][j - 1] + 1,
        matrix[i - 1][j] + 1
      );
    }
  }
  return matrix[b.length][a.length];
}

function sendCBT() {
  const text = input.value.trim();
  if (!text) return;
  appendCBT(text, "user");

  const lower = text.toLowerCase();
  let bestMatch = "";
  let bestScore = 0;

  for (const key in faqCBT) {
    const score = similarity(lower, key);
    if (score > bestScore) {
      bestScore = score;
      bestMatch = key;
    }
  }

  let botReply;
  if (bestScore >= 0.6) {
    botReply = faqCBT[bestMatch];
  } else if (bestScore >= 0.3) {
    botReply = `Mungkin maksud kamu: <b>"${bestMatch}"</b>?<br>👉 ${faqCBT[bestMatch]}`;
  } else {
    const suggestion = Object.keys(faqCBT).slice(0, 5).map(k => `- ${k}`).join("<br>");
    botReply = "Maaf, saya belum mengerti pertanyaan itu. Coba gunakan kata kunci seperti:<br>" + suggestion;
  }

  appendCBT(botReply, "bot", 1200);
  input.value = "";
  input.focus();
}

// Animasi buka-tutup
let welcomed = false;
function openChatbot() {
  chatbot.classList.remove("closed", "minimized");
  body.style.display = "flex";
  footer.style.display = "flex";
  btnOpen.style.display = "none";
  input.focus();
  removeNewMessageNotif();
  if (!welcomed) {
    welcomeMessage();
    welcomed = true;
  }
}
function minimizeChatbot() {
  chatbot.classList.add("minimized");
  body.style.display = "none";
  footer.style.display = "none";
  btnOpen.style.display = "flex";
}
function closeChatbot() {
  chatbot.classList.add("closed");
  body.style.display = "none";
  footer.style.display = "none";
  btnOpen.style.display = "flex";
}

// Notifikasi pesan baru
function showNewMessageNotif() {
  btnOpen.classList.add("new-message");
}
function removeNewMessageNotif() {
  btnOpen.classList.remove("new-message");
}

// Awal load
window.onload = () => {
  closeChatbot();
};
</script>