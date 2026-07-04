// ============================================================
// InnerCanvas Reminder System
// File: public/js/reminders.js
// Purpose: Daily check-in reminders with notifications + sound
// ============================================================

class InnerCanvasReminder {
    constructor() {
        this.reminderTime = 9; // 9 AM
        this.storageKey = 'innerCanvas_lastReminder';
    }
    
    // Initialize reminder system
    init() {
        this.checkReminder();
        // Check every 5 minutes
        setInterval(() => this.checkReminder(), 300000);
    }
    
    // Check if reminder should show
    checkReminder() {
        const now = new Date();
        const lastReminder = this.getLastReminder();
        const today = now.toDateString();
        
        // Show reminder if hasn't been shown today and it's after 9 AM
        if (lastReminder !== today && now.getHours() >= this.reminderTime) {
            this.showReminder();
            this.saveReminder(today);
        }
    }
    
    // Get last reminder date
    getLastReminder() {
        return localStorage.getItem(this.storageKey);
    }
    
    // Save reminder date
    saveReminder(date) {
        localStorage.setItem(this.storageKey, date);
    }
    
    // Show reminder with sound
    showReminder() {
        // Request notification permission if needed
        if (Notification.permission === 'granted') {
            this.displayNotification();
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    this.displayNotification();
                }
            });
        }
        
        // Always play sound and show alert
        this.playSound();
        this.showAlert();
    }
    
    // Display browser notification
    displayNotification() {
        const notification = new Notification('InnerCanvas - Time to Check In! 💙', {
            body: 'How are you feeling today? Let\'s check in together.',
            icon: '🌈',
            badge: '💙',
            tag: 'innercanvas-checkin',
            requireInteraction: true
        });
        
        notification.onclick = () => {
            window.location.href = '/InnerCanvas/pages/youth_member/dashboard.php';
        };
    }
    
    // Play notification sound
    playSound() {
        try {
            // Simple beep sound
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gain = audioContext.createGain();
            
            oscillator.connect(gain);
            gain.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gain.gain.setValueAtTime(0.3, audioContext.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            console.log('Audio notification not supported');
        }
    }
    
    // Show browser alert
    showAlert() {
        const message = '💙 Remember to check in today! How are you feeling?\n\nTake a moment to notice your emotions and choose an activity tailored to how you feel.';
        alert(message);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    // Start reminder system
    const reminder = new InnerCanvasReminder();
    reminder.init();
});