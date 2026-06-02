// quote.js
console.log("JS LOADED");

// Bulletproof backup list in case of network latency drops
const localAffirmations = [
     { quote: "I am resilient, strong, and completely capable of navigating this day.", author: "Ruhh Therapy" },
    // { quote: "My peace of mind is worth more than any situation outside of my control.", author: "Ruhh Therapy" },
    // { quote: "I choose to be profoundly kind to myself and honor my emotions.", author: "Ruhh Therapy" },
    // { quote: "I am growing and adapting at a pace that is completely safe for me.", author: "Ruhh Therapy" },
    // { quote: "My current feelings are valid, and I allow my mind the time it needs to heal.", author: "Ruhh Therapy" }
];

function useFallbackAffirmation() {
    const randomIndex = Math.floor(Math.random() * localAffirmations.length);
    const selected = localAffirmations[randomIndex];
    document.getElementById("quote").innerText = `"${selected.quote}"`;
    document.getElementById("author").innerText = `— ${selected.author}`;
}

async function getQuote() {
    try {
        // Appended a unique timestamp string parameter to bypass browser caching locks on local PHP queries
        const response = await fetch('quote.php?v=' + Date.now());
        const data = await response.json();

        console.log("API Response Data:", data);

        // If the API returns an error or fails JSON verification, trigger our fallback
        if (data && data.error) {
            console.warn("API Error Detected:", data.error);
            useFallbackAffirmation();
            return;
        }

        // Successfully render valid generated array
        if (data && data.length > 0 && data[0].quote) {
            document.getElementById("quote").innerText = `"${data[0].quote}"`;
            document.getElementById("author").innerText = `— ${data[0].author || 'Ruhh Therapy'}`;
        } else {
            useFallbackAffirmation();
        }
    } catch (error) {
        console.error("Fetch Processing Error:", error);
        useFallbackAffirmation();
    }
}

// Global page initialization
getQuote();

// Core Click event hook assignment
document.getElementById("new-quote").addEventListener("click", (e) => {
    e.preventDefault();
    getQuote();
});

// Threads Application Posting Handler
document.getElementById("share-btn").addEventListener("click", () => {
    const mainQuote = document.getElementById("quote").innerText;
    const itemAuthor = document.getElementById("author").innerText;
    const finalPostPayload = `${mainQuote} ${itemAuthor} #RuhhTherapy`;

    const shareUrl = `https://www.threads.net/intent/post?text=${encodeURIComponent(finalPostPayload)}`;
    window.open(shareUrl, "_blank");
});