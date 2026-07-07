// ============================================================
// InnerCanvas Notification Sound
// File: public/js/notification-sound.js
// Purpose: Generate pleasant notification chime sound
// ============================================================

class NotificationSound {
    constructor() {
        this.audioContext = null;
    }
    
    // Initialize audio context
    init() {
        try {
            const audioContextClass = window.AudioContext || window.webkitAudioContext;
            this.audioContext = new audioContextClass();
        } catch (e) {
            console.log('Web Audio API not supported');
        }
    }
    
    // Play notification chime (pleasant, non-intrusive)
    playChime() {
        if (!this.audioContext) return;
        
        // Create pleasant notification sequence
        const now = this.audioContext.currentTime;
        const tempo = 0.15;
        
        // Note frequencies (in Hz)
        const notes = [
            { freq: 523.25, duration: 0.1 },  // C5
            { freq: 659.25, duration: 0.1 },  // E5
            { freq: 783.99, duration: 0.2 }   // G5 (held longer)
        ];
        
        notes.forEach((note, index) => {
            this.playTone(now + (index * tempo), note.freq, note.duration);
        });
    }
    
    // Play single tone
    playTone(startTime, frequency, duration) {
        const osc = this.audioContext.createOscillator();
        const gain = this.audioContext.createGain();
        
        osc.connect(gain);
        gain.connect(this.audioContext.destination);
        
        osc.frequency.value = frequency;
        osc.type = 'sine';
        
        // Envelope
        gain.gain.setValueAtTime(0, startTime);
        gain.gain.linearRampToValueAtTime(0.3, startTime + 0.01);
        gain.gain.exponentialRampToValueAtTime(0.01, startTime + duration);
        
        osc.start(startTime);
        osc.stop(startTime + duration);
    }
}

// Initialize and use
const notificationSound = new NotificationSound();
notificationSound.init();

// Export for use in other files
window.playNotificationChime = () => notificationSound.playChime();