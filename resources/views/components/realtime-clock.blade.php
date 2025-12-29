{{-- Real-time Clock WIB Component --}}
<div class="realtime-clock" id="realtimeClock">
    <div class="clock-header">
        <i class="fas fa-clock clock-icon"></i>
        <span class="clock-title">Waktu Indonesia Barat</span>
    </div>
    <div class="clock-time" id="clockTime">--:--:--</div>
    <div class="clock-date" id="clockDate">-- --- ----</div>
    <div class="clock-timezone">WIB (UTC+7)</div>
</div>

<script>
// Enhanced Real-time Clock WIB
class EnhancedRealtimeClock {
    constructor() {
        this.clockElement = document.getElementById('realtimeClock');
        this.timeElement = document.getElementById('clockTime');
        this.dateElement = document.getElementById('clockDate');
        this.timezoneElement = this.clockElement.querySelector('.clock-timezone');
        this.isRunning = false;
        this.intervalId = null;
        this.lastUpdateTime = null;
        
        this.init();
    }
    
    init() {
        this.startClock();
        this.setupResponsive();
        this.setupKeyboardShortcuts();
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
        
        // Check if time has changed to add animation
        if (this.lastUpdateTime !== timeString) {
            this.addUpdateAnimation();
            this.lastUpdateTime = timeString;
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
    
    addUpdateAnimation() {
        if (this.timeElement) {
            this.timeElement.classList.add('updating');
            setTimeout(() => {
                this.timeElement.classList.remove('updating');
            }, 300);
        }
    }
    
    setupResponsive() {
        window.addEventListener('resize', () => {
            this.handleResize();
        });
        this.handleResize();
    }
    
    handleResize() {
        if (!this.clockElement) return;
        
        const width = window.innerWidth;
        
        if (width <= 640) {
            this.clockElement.style.position = 'relative';
            this.clockElement.style.top = 'auto';
            this.clockElement.style.right = 'auto';
            this.clockElement.style.margin = '10px auto';
            this.clockElement.style.maxWidth = '300px';
            this.clockElement.style.textAlign = 'center';
        } else {
            this.clockElement.style.position = 'fixed';
            this.clockElement.style.top = '20px';
            this.clockElement.style.right = '20px';
            this.clockElement.style.margin = '0';
            this.clockElement.style.maxWidth = 'none';
            this.clockElement.style.textAlign = 'left';
        }
    }
    
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl+Shift+T to toggle clock
            if (e.ctrlKey && e.shiftKey && e.key === 'T') {
                e.preventDefault();
                this.toggle();
            }
        });
    }
    
    toggle() {
        if (this.clockElement.style.display === 'none') {
            this.show();
        } else {
            this.hide();
        }
    }
    
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

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.enhancedClock = new EnhancedRealtimeClock();
});
</script>
