
const clickSound = document.getElementById('clickSound');
function playClick() { clickSound?.play().catch(() => {}); }

// ── MOOD CONFIG ───────────────────────────────────────────────────────────────
const moodConfig = {
    happy:    { emoji: "(˶˃ ᵕ ˂˶)", label: "Happy",       color: "#f5a8d7" },
    loved:    { emoji: "(´｡• ◡ •｡`)❤︎", label: "Loved",        color: "#fba5ba" },
    excited:  { emoji: "₍₍⚞(˶˃ ꒳ ˂˶)⚟⁾⁾", label: "Excited",      color: "#fdcc8f" },
    grateful: { emoji: " (ㅅ´ ˘ `)", label: "Grateful",     color: "#bcfcaf" },
    confused: { emoji: " (´･_･`)", label: "Confused",     color: "#bca9f0" },
    hug:      { emoji: " (つ｡˃ ᵕ ˂)つ", label: "Needs a Hug",  color: "#fcff9c" },
    annoyed:  { emoji: " (￣へ￣)", label: "Annoyed",      color: "#f3aeae" },
    sad:      { emoji: " (╥﹏╥)", label: "Sad",          color: "#a0e8ea" },
    angry:    { emoji: " ", label: "Angry",        color: "#ee7979" },
};

// Legacy emoji text map (for old entries stored with kaomoji)
const legacyMoodMap = {
    '(˶˃ ᵕ ˂˶)': 'happy',
    '(´｡• ◡ •｡`)❤︎': 'loved',
    '(╥﹏╥)': 'sad',
    '( ,,⩌\'︿\'⩌ꐦ,,)': 'angry',
    '(￣へ￣)': 'annoyed',
    '(つ｡˃ ᵕ ˂)つ': 'hug',
    '(´･_･`)': 'confused',
    '(ㅅ´ ˘ `)': 'grateful',
    '₍₍⚞(˶˃ ꒳ ˂˶)⚟⁾⁾': 'excited',
};

function normaliseMood(mood) {
    return moodConfig[mood] ? mood : (legacyMoodMap[mood] || 'happy');
}

let globalEntries = [];

// ── LOAD NOTES ────────────────────────────────────────────────────────────────
async function loadNotes() {
    try {
        const response = await fetch('load-journal.php');
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        globalEntries = await response.json();

        const container = document.querySelector('.newnotes');
        if (container) {
            container.innerHTML = '';
            globalEntries.forEach(entry => createNoteElement(entry));
        }
        renderMoodTracker(globalEntries);
    } catch (error) {
        console.error('Load failed:', error);
    }
}

// ── CREATE NOTE CARD ──────────────────────────────────────────────────────────
function createNoteElement(entry) {
    const mood = normaliseMood(entry.mood);
    const cfg  = moodConfig[mood];

    const div = document.createElement('div');
    div.classList.add('mynote');
    div.dataset.entryId = entry.id;
    div.innerHTML = `
        <h2>${entry.title || 'Untitled'}</h2>
        <p class="note-container">${entry.content || ''}</p>
        <p class="date"><i class="fa-regular fa-calendar" style="margin-right:4px;"></i>${entry.entry_date}</p>
        <p class="mood">${cfg.emoji} ${cfg.label}</p>
        <button class="delete-btn"><i class="fa-solid fa-trash-can"></i> Delete</button>
    `;

    // Delete
    div.querySelector('.delete-btn').addEventListener('click', async (e) => {
        e.stopPropagation();
        playClick();
        const formData = new FormData();
        formData.append('entry_id', entry.id);
        try {
            const res = await fetch('delete-journal.php', { method: 'POST', body: formData });
            const result = await res.json();
            if (result.status === 'success') {
                div.style.transition = 'all 0.3s ease';
                div.style.opacity = '0';
                div.style.transform = 'scale(0.9)';
                setTimeout(() => { div.remove(); loadNotes(); }, 300);
            }
        } catch(e) { console.error(e); }
    });

    // Click to view full entry
    div.addEventListener('click', () => {
        const viewNote = document.getElementById("viewNote");
        document.getElementById("viewTitle").innerText = entry.title || 'Untitled';
        document.getElementById("viewDate").innerText  = entry.entry_date || '';
        document.getElementById("viewMood").innerText  = cfg.emoji + "  " + cfg.label;
        document.getElementById("viewText").innerText  = entry.content || '';
        viewNote.style.display = "flex";
    });

    const targetContainer = document.querySelector('.newnotes');
    if (targetContainer) targetContainer.appendChild(div);
}


// ── mood tracker and sab kuch 
function renderMoodTracker(entries) {
    const freqSection = document.getElementById('moodFreqSection');
    const freqGrid = document.getElementById('moodFreqGrid');
    const svg = document.getElementById('moodGraphSvg');
    const line = document.getElementById('graphLine');
    const nodesContainer = document.getElementById('graphNodesContainer');
    const xAxis = document.getElementById('graphXAxis');

    if (!entries || entries.length === 0) {
        if (freqSection) freqSection.style.display = 'none';
        return;
    }

    

    // 2. Build the 9-Tier Mood App Line Graph
    const moodWeights = {
        excited:  9,
        loved:    8,
        happy:    7,
        grateful: 6,
        confused: 5,
        hug:      4,
        annoyed:  3,
        sad:      2,
        angry:    1
    };

    // Reverse to display oldest on left and newest entries on right (last 7 logs)
    const chronologicalEntries = [...entries].reverse().slice(-7);

    if (!svg || chronologicalEntries.length === 0) return;

    nodesContainer.innerHTML = '';
    xAxis.innerHTML = '';

    const width = 1000;
    const height = 380; // Expanded aspect ratio height for 9 tiers
    const paddingY = 30; 
    const pointCount = chronologicalEntries.length;
    const stepX = pointCount > 1 ? width / (pointCount - 1) : width;
    let pointsCoordinates = [];

    chronologicalEntries.forEach((entry, idx) => {
        const mood = normaliseMood(entry.mood);
        const cfg = moodConfig[mood];
        const weight = moodWeights[mood] || 5;

        const x = pointCount > 1 ? idx * stepX : width / 2;
        // Mathematically project points across a 9-tier grid system
        const y = paddingY + ((9 - weight) / 8) * (height - paddingY * 2);
        pointsCoordinates.push({ x, y, cfg, entry });

        // Generate interactive avatar node markers with full kaomojis
        const dotNode = document.createElement('div');
        dotNode.classList.add('graph-dot-node');
        dotNode.style.left = `${(x / width) * 100}%`;
        dotNode.style.top = `${(y / height) * 100}%`;
        dotNode.style.setProperty('--node-color', cfg.color);
        dotNode.innerHTML = `
            <div class="node-pop-avatar">${cfg.emoji}</div>
            <div class="node-tooltip">${cfg.label}<br><small>${entry.title || 'Untitled'}</small></div>
        `;
        nodesContainer.appendChild(dotNode);

        // Append date labels to horizontal axis
        const dateLabel = document.createElement('span');
        const splitDate = entry.entry_date.split('-');
        dateLabel.innerText = splitDate.length > 2 ? `${splitDate[1]}/${splitDate[2]}` : entry.entry_date;
        xAxis.appendChild(dateLabel);
    });

    if (pointsCoordinates.length > 0) {
        let dPath = `M ${pointsCoordinates[0].x} ${pointsCoordinates[0].y}`;
        for (let i = 1; i < pointsCoordinates.length; i++) {
            const prev = pointsCoordinates[i - 1];
            const curr = pointsCoordinates[i];
            const cpX1 = prev.x + (curr.x - prev.x) / 2;
            const cpY1 = prev.y;
            const cpX2 = prev.x + (curr.x - prev.x) / 2;
            const cpY2 = curr.y;
            dPath += ` C ${cpX1} ${cpY1}, ${cpX2} ${cpY2}, ${curr.x} ${curr.y}`;
        }
        line.setAttribute('d', dPath);
        line.setAttribute('stroke', '#f2b1d9');
    }

    // Trigger distribution ring animations
    setTimeout(() => {
        document.querySelectorAll('.circle-progress circle.progress').forEach(circle => {
            circle.style.strokeDashoffset = circle.dataset.offset;
        });
    }, 150);

    if (pointsCoordinates.length > 0) {
        let dPath = `M ${pointsCoordinates[0].x} ${pointsCoordinates[0].y}`;
        for (let i = 1; i < pointsCoordinates.length; i++) {
            const prev = pointsCoordinates[i - 1];
            const curr = pointsCoordinates[i];
            const cpX1 = prev.x + (curr.x - prev.x) / 2;
            const cpY1 = prev.y;
            const cpX2 = prev.x + (curr.x - prev.x) / 2;
            const cpY2 = curr.y;
            dPath += ` C ${cpX1} ${cpY1}, ${cpX2} ${cpY2}, ${curr.x} ${curr.y}`;
        }
        line.setAttribute('d', dPath);
        line.setAttribute('stroke', '#f2b1d9');
    }

    // Trigger ring animations
    setTimeout(() => {
        document.querySelectorAll('.circle-progress circle.progress').forEach(circle => {
            circle.style.strokeDashoffset = circle.dataset.offset;
        });
    }, 150);
}

// ── OPEN / CLOSE FORM ─────────────────────────────────────────────────────────
document.getElementById('addnote')?.addEventListener('click', () => {
    playClick();
    const form = document.querySelector('.addform');
    if (form) form.style.display = 'flex';
    document.getElementById('entryTitle').value = '';
    document.getElementById('entryContent').value = '';
    document.getElementById('entryDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('entryMood').value = 'happy';
});

document.querySelector('.icon')?.addEventListener('click', () => {
    playClick();
    const form = document.querySelector('.addform');
    if (form) form.style.display = 'none';
});

document.addEventListener('click', (e) => {
    const form = document.querySelector('.addform');
    if (form && form.style.display === 'flex' && !form.contains(e.target) && e.target.id !== 'addnote') {
        form.style.display = 'none';
    }
});

// ── SAVE NOTE ─────────────────────────────────────────────────────────────────
document.getElementById('addbtn')?.addEventListener('click', async () => {
    const title    = document.getElementById('entryTitle').value.trim();
    const content  = document.getElementById('entryContent').value.trim();
    const entryDate= document.getElementById('entryDate').value;
    const mood     = document.getElementById('entryMood').value;

    if (!title || !content || !entryDate) {
        alert('Please fill in title, thoughts, and date.');
        return;
    }

    playClick();
    const formData = new FormData();
    formData.append('title', title);
    formData.append('content', content);
    formData.append('entry_date', entryDate);
    formData.append('mood', mood);

    try {
        const res    = await fetch('save-journal.php', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.status === 'success') {
            const form = document.querySelector('.addform');
            if (form) form.style.display = 'none';
            loadNotes();
        } else {
            alert('Save failed: ' + (result.message || 'Unknown error'));
        }
    } catch(e) {
        alert('Error: ' + e.message);
    }
});

// ── INITIALIZE ────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const viewNote = document.getElementById("viewNote");
    const closeView= document.getElementById("closeView");

    closeView?.addEventListener("click", () => { playClick(); if (viewNote) viewNote.style.display = "none"; });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && viewNote?.style.display === 'flex') viewNote.style.display = 'none';
    });
    viewNote?.addEventListener('click', (e) => {
        if (e.target === viewNote) viewNote.style.display = 'none';
    });

    loadNotes();
});

