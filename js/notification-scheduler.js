// ============================================================
// InnerCanvas Browser Notification Scheduler
// File: public/js/notification-scheduler.js
// Purpose: Show daily reminder notifications like DuoLingo
// Storage: localStorage (no server required)
// ============================================================

class NotificationScheduler {
    constructor() {
        this.storageKey = 'innercanvas_reminder_' + new Date().toDateString();
        this.reminderShown = localStorage.getItem(this.storageKey) === 'true';
    }
    
    // Initialize notification system
    init() {
        this.requestPermission();
        this.checkAndShowReminder();
    }
    
    // Request browser notification permission
    requestPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }
    
    // Check if reminder should show (once per day at 9 AM+)
    checkAndShowReminder() {
        const now = new Date();
        const hour = now.getHours();
        
        // Only show after 9 AM
        if (hour >= 9 && !this.reminderShown) {
            this.showReminder();
            localStorage.setItem(this.storageKey, 'true');
        }
    }
    
    // Show the reminder notification
    showReminder() {
        const reminders = [
            {
                title: "💙 We're missing you!",
                body: "How are you feeling today? Let's check in.",
                icon: '🌈'
            },
            {
                title: "🔥 Keep your streak alive!",
                body: "Consistency is key. One quick check-in?",
                icon: '⚡'
            },
            {
                title: "✨ Your wellness matters",
                body: "Take 2 minutes to check in with yourself.",
                icon: '💪'
            },
            {
                title: "🎯 Daily check-in reminder",
                body: "How's your mental health today?",
                icon: '📊'
            },
            {
                title: "🌟 Don't break the chain!",
                body: "Come back and log your mood.",
                icon: '🔗'
            }
        ];
        
        const reminder = reminders[Math.floor(Math.random() * reminders.length)];
        
        // Show browser notification
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification(reminder.title, {
                body: reminder.body,
                icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="80" font-size="80">' + reminder.icon + '</text></svg>',
                badge: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="80" font-size="80">🌈</text></svg>',
                tag: 'innercanvas-reminder',
                requireInteraction: false
            });
            
            // Click handler - redirect to dashboard
            notification.onclick = () => {
                window.location.href = '/InnerCanvas/pages/youth_member/dashboard.php';
                notification.close();
            };
            
            // Auto close after 8 seconds
            setTimeout(() => notification.close(), 8000);
        }
    }
    
    // Show in-app banner alternative (fallback if notifications disabled)
    showFallbackBanner() {
        const banners = [
            { msg: "How are you feeling today? 💭", color: '#87CEEB' },
            { msg: "Keep your streak alive! 🔥", color: '#F39C12' },
            { msg: "Your wellness matters. 💙", color: '#4A90E2' },
        ];
        
        const banner = banners[Math.floor(Math.random() * banners.length)];
        
        const bannerEl = document.createElement('div');
        bannerEl.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${banner.color};
            color: white;
            padding: 18px 25px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            animation: slideInRight 0.5s ease-out;
            cursor: pointer;
            max-width: 300px;
        `;
        
        bannerEl.innerHTML = banner.msg + ' <button style="margin-left: 10px; background: rgba(255,255,255,0.3); border: none; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-weight: 700;">✕</button>';
        
        document.body.appendChild(bannerEl);
        
        // Close on click
        bannerEl.querySelector('button').onclick = (e) => {
            e.stopPropagation();
            bannerEl.remove();
        };
        
        // Auto remove after 10 seconds
        setTimeout(() => {
            if (bannerEl.parentNode) bannerEl.remove();
        }, 10000);
    }
}

// Initialize on page load
const notificationScheduler = new NotificationScheduler();
window.addEventListener('load', () => {
    notificationScheduler.init();
});

// CSS animation (add to stylesheet if not present)
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);