// Real-time Clock WIB (Waktu Indonesia Barat)
class RealtimeClock {
    constructor() {
        this.clockElement = null;
        this.timeElement = null;
        this.dateElement = null;
        this.timezoneElement = null;
        this.isRunning = false;
        this.intervalId = null;
        
        this.init();
    }
    
    init() {
        this.createClockElement();
        this.startClock();
        this.setupResponsive();
    }
    
    createClockElement() {
        // Create clock container
        this.clockElement = document.createElement('div');
        this.clockElement.className = 'realtime-clock';
        this.clockElement.innerHTML = `
            <div class="clock-header">
                <i class="fas fa-clock clock-icon"></i>
                <span class="clock-title">Waktu Indonesia Barat</span>
            </div>
            <div class="clock-time" id="clockTime">--:--:--</div>
            <div class="clock-date" id="clockDate">-- --- ----</div>
            <div class="clock-timezone">WIB (UTC+7)</div>
        `;
        
        // Append to body
        document.body.appendChild(this.clockElement);
        
        // Get elements
        this.timeElement = document.getElementById('clockTime');
        this.dateElement = document.getElementById('clockDate');
        this.timezoneElement = this.clockElement.querySelector('.clock-timezone');
    }
    
    startClock() {
        if (this.isRunning) return;
        
        this.isRunning = true;
        this.updateTime();
        
        // Update every second
        this.intervalId = setInterval(() => {
            this.updateTime();
        }, 1000);
    }
    
    stopClock() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
        this.isRunning = false;
    }
    
    updateTime() {
        const now = new Date();
        
        // Convert to WIB (UTC+7)
        const wibTime = new Date(now.getTime() + (7 * 60 * 60 * 1000));
        
        // Format time
        const timeString = this.formatTime(wibTime);
        const dateString = this.formatDate(wibTime);
        
        // Add update animation
        if (this.timeElement) {
            this.timeElement.classList.add('updating');
            setTimeout(() => {
                this.timeElement.classList.remove('updating');
            }, 300);
        }
        
        // Update elements
        if (this.timeElement) {
            this.timeElement.textContent = timeString;
        }
        if (this.dateElement) {
            this.dateElement.textContent = dateString;
        }
    }
    
    formatTime(date) {
        const hours = date.getUTCHours().toString().padStart(2, '0');
        const minutes = date.getUTCMinutes().toString().padStart(2, '0');
        const seconds = date.getUTCSeconds().toString().padStart(2, '0');
        
        return `${hours}:${minutes}:${seconds}`;
    }
    
    formatDate(date) {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dayName = days[date.getUTCDay()];
        const day = date.getUTCDate();
        const month = months[date.getUTCMonth()];
        const year = date.getUTCFullYear();
        
        return `${dayName}, ${day} ${month} ${year}`;
    }
    
    setupResponsive() {
        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });
        
        // Initial resize check
        this.handleResize();
    }
    
    handleResize() {
        if (!this.clockElement) return;
        
        const width = window.innerWidth;
        
        if (width <= 640) {
            // Move to top of page for mobile
            this.clockElement.style.position = 'relative';
            this.clockElement.style.top = 'auto';
            this.clockElement.style.right = 'auto';
            this.clockElement.style.margin = '10px auto';
            this.clockElement.style.maxWidth = '300px';
            this.clockElement.style.textAlign = 'center';
        } else {
            // Fixed position for desktop
            this.clockElement.style.position = 'fixed';
            this.clockElement.style.top = '20px';
            this.clockElement.style.right = '20px';
            this.clockElement.style.margin = '0';
            this.clockElement.style.maxWidth = 'none';
            this.clockElement.style.textAlign = 'left';
        }
    }
    
    // Public methods
    show() {
        if (this.clockElement) {
            this.clockElement.style.display = 'block';
        }
    }
    
    hide() {
        if (this.clockElement) {
            this.clockElement.style.display = 'none';
        }
    }
    
    destroy() {
        this.stopClock();
        if (this.clockElement && this.clockElement.parentNode) {
            this.clockElement.parentNode.removeChild(this.clockElement);
        }
    }
}

// Initialize clock when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Create global clock instance
    window.realtimeClock = new RealtimeClock();
    
    // Optional: Add clock toggle functionality
    window.toggleClock = function() {
        if (window.realtimeClock) {
            const isVisible = window.realtimeClock.clockElement.style.display !== 'none';
            if (isVisible) {
                window.realtimeClock.hide();
            } else {
                window.realtimeClock.show();
            }
        }
    };
    
    // Optional: Add keyboard shortcut (Ctrl+Shift+T)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key === 'T') {
            e.preventDefault();
            window.toggleClock();
        }
    });
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RealtimeClock;
}
