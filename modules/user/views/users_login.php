<?php 
$this->load->view('top_application',array('has_header'=>false,'has_body_class'=>'login_bg'));
$site_title_text = escape_chars($this->config->item('site_name'));
?>
<div class="container-xxl">
    <div class="login_box">
        <p class="text-center logo">
            <img src="<?php echo theme_url();?>images/auva.jpg" width="175" height="83" alt="<?php echo $site_title_text;?>">
        </p>
        <p class="text-center mt-2 text-black fw-medium">Please sign in to your existing account</p>
        
        <div id="err_msg" class="text-center me-4"></div>
        <div class="form_box2">
            <?php echo form_open(current_url_query_string(),'name="login_frm" autocomplete="off"'); ?>
            <div class="login_email">
                <input type="text" id="user_name" name="user_name" value="<?php echo set_value('user_name',$posted_user_name);?>" class="border-0 bg-transparent w-100" placeholder="Email Address *">
            </div>
            <div id="err_user_name" class="text-danger"></div>
            <div class="login_password position-relative">
                <a href="#" class="login_eye" onclick="togglePasswordVisibility(event)">
                    <img src="<?php echo theme_url();?>images/eye.svg" alt="Show Password" id="eye_icon">
                </a>
                <input type="password" id="password" name="password" class="border-0 bg-transparent w-100" value="<?php echo set_value('password',$posted_password);?>" placeholder="Password *">
            </div>
            <div id="err_password" class="text-danger"></div>
            <p class="clearfix"></p>
            <div class="float-start form-check">
                <input class="form-check-input" type="checkbox" id="flexCheckChecked" name="remember" value="Y" <?php echo set_value('remember',$remember)=='Y' ? ' checked="checked"' : '';?>>
                <label class="form-check-label fs-7" for="flexCheckChecked">Remember me</label>
            </div>
            <div class="float-end fs-7 fw-medium"> 
                <a data-fancybox="" data-type="iframe" data-src="<?php echo site_url('forgot-password');?>" href="javascript:void(0);" class="pop1 blue">Forgot Your Password?</a>
            </div>
            <p class="clearfix"></p>
            <div class="login_tab mt-3 text-center">    
                <input type="hidden" name="action" value="Y" />
                <input type="hidden" class="member_type" name="member_type" value="">
                <button type="submit" id="login_btn" class="text-white rounded-5 fw-bold d-inline-block trans_eff">Sign In</button>
            </div>
            <p class="mt-4 text-center fw-bold text-uppercase">
                <a href="<?php echo site_url('register');?>" class="text-primary">Create an Account</a>
            </p>
            <?php echo form_close();?>
        </div>
    </div>
    
    <!-- Email OTP Verification Modal - Music Distribution Style -->
    <div class="modal fade" id="verify_otp_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content music-modal">
                <!-- Animated Vinyl Record Background -->
                <div class="vinyl-background">
                    <div class="vinyl-record">
                        <div class="vinyl-center"></div>
                        <div class="vinyl-groove"></div>
                        <div class="vinyl-groove"></div>
                        <div class="vinyl-groove"></div>
                    </div>
                    <div class="tonearm">
                        <div class="tonearm-base"></div>
                        <div class="tonearm-arm"></div>
                        <div class="tonearm-head"></div>
                    </div>
                </div>

                <!-- Floating Music Notes -->
                <div class="floating-notes">
                    <div class="music-note note1">♪</div>
                    <div class="music-note note2">♫</div>
                    <div class="music-note note3">♪</div>
                    <div class="music-note note4">♩</div>
                    <div class="music-note note5">♬</div>
                    <div class="music-note note6">♫</div>
                </div>

                <!-- Sound Waves -->
                <div class="sound-waves">
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                </div>

                <!-- Toast Notification Container -->
                <div class="toast-container" id="toastContainer"></div>

                <!-- Modal Header -->
                <div class="modal-header music-header">
                    <div class="header-icon">
                        <div class="equalizer">
                            <div class="eq-bar"></div>
                            <div class="eq-bar"></div>
                            <div class="eq-bar"></div>
                            <div class="eq-bar"></div>
                        </div>
                    </div>
                    <div class="header-content">
                        <h5 class="modal-title">Verify Your Identity</h5>
                        <p class="header-subtitle">Enter the verification code to continue</p>
                    </div>
                    <button type="button" class="music-close" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body music-body">
                    <form id="otp_frm" action="<?php echo site_url('user/verify_email_otp');?>" method="post">
                        
                        <!-- OTP Display Card -->
                        <div class="otp-card">
                            <div class="card-glow"></div>
                            <div class="otp-display">
                                <div class="otp-digit" data-index="0">_</div>
                                <div class="otp-digit" data-index="1">_</div>
                                <div class="otp-digit" data-index="2">_</div>
                                <div class="otp-digit" data-index="3">_</div>
                                <div class="otp-digit" data-index="4">_</div>
                                <div class="otp-digit" data-index="5">_</div>
                            </div>
                        </div>

                        <!-- OTP Input Section -->
                        <div class="input-section">
                            <input type="text" class="otp-input" id="otp_code" name="otp_code" maxlength="6" 
                                   placeholder="••••••" autocomplete="off" inputmode="numeric">
                            <label class="input-label">
                                <i class="fas fa-headphones"></i>
                                <span>Enter 6-digit code</span>
                            </label>
                            <div class="input-highlight"></div>
                        </div>

                        <div id="err_otp_code" class="error-message"></div>
                        
                        <input type="hidden" id="temp_user_id" name="temp_user_id" value="">
                        <input type="hidden" id="temp_member_type" name="temp_member_type" value="">
                        <input type="hidden" id="remember_me" name="remember_me" value="">
                        
                        <!-- Timer & Resend Section -->
                        <div class="timer-resend">
                            <div class="timer-container">
                                <svg class="timer-ring" width="80" height="80" viewBox="0 0 80 80">
                                    <circle cx="40" cy="40" r="35" fill="none" stroke="rgba(108, 99, 255, 0.1)" stroke-width="3"/>
                                    <circle class="timer-ring-progress" cx="40" cy="40" r="35" fill="none" stroke="url(#timerGradient)" stroke-width="3" stroke-dasharray="219.8" stroke-dashoffset="0"/>
                                    <defs>
                                        <linearGradient id="timerGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#6C63FF"/>
                                            <stop offset="100%" style="stop-color:#FF6584"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <div class="timer-content">
                                    <span class="timer-seconds" id="timerSeconds">60</span>
                                    <span class="timer-label">seconds</span>
                                </div>
                            </div>
                            
                            <button type="button" id="btn_resend" class="resend-button">
                                <i class="fas fa-redo-alt"></i>
                                <span>Resend Code</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer music-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                    <button type="button" id="btn_verify" class="btn-verify">
                        <span class="btn-text">Verify & Continue</span>
                        <i class="fas fa-arrow-right"></i>
                        <div class="btn-loader"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <p class="clearfix"></p>
</div>

<style>
/* Music Distribution Modal Styles */
.music-modal {
    background: linear-gradient(135deg, #0a0a2a 0%, #1a0a2a 50%, #0a0a2a 100%);
    border-radius: 40px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5), 0 0 0 2px rgba(108, 99, 255, 0.3);
    animation: modalPulse 2s ease-in-out infinite;
}

@keyframes modalPulse {
    0%, 100% {
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5), 0 0 0 2px rgba(108, 99, 255, 0.3);
    }
    50% {
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5), 0 0 0 4px rgba(255, 101, 132, 0.5);
    }
}

/* Toast Notifications */
.toast-container {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}

.music-toast {
    min-width: 280px;
    background: rgba(10, 10, 42, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-left: 4px solid;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    transform: translateX(400px);
    animation: slideIn 0.3s ease forwards;
    pointer-events: auto;
}

@keyframes slideIn {
    to {
        transform: translateX(0);
    }
}

.music-toast.success {
    border-left-color: #00ff88;
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 255, 136, 0.05));
}

.music-toast.error {
    border-left-color: #ff4466;
    background: linear-gradient(135deg, rgba(255, 68, 102, 0.1), rgba(255, 68, 102, 0.05));
}

.music-toast.warning {
    border-left-color: #ffaa00;
    background: linear-gradient(135deg, rgba(255, 170, 0, 0.1), rgba(255, 170, 0, 0.05));
}

.music-toast.info {
    border-left-color: #6C63FF;
    background: linear-gradient(135deg, rgba(108, 99, 255, 0.1), rgba(108, 99, 255, 0.05));
}

.toast-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.music-toast.success .toast-icon {
    background: rgba(0, 255, 136, 0.2);
    color: #00ff88;
}

.music-toast.error .toast-icon {
    background: rgba(255, 68, 102, 0.2);
    color: #ff4466;
}

.music-toast.warning .toast-icon {
    background: rgba(255, 170, 0, 0.2);
    color: #ffaa00;
}

.music-toast.info .toast-icon {
    background: rgba(108, 99, 255, 0.2);
    color: #6C63FF;
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-weight: 600;
    color: white;
    font-size: 14px;
    margin-bottom: 4px;
}

.toast-message {
    color: rgba(255, 255, 255, 0.7);
    font-size: 12px;
}

.toast-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    font-size: 14px;
    transition: color 0.3s ease;
}

.toast-close:hover {
    color: white;
}

.progress-container {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 0 0 12px 12px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    width: 100%;
    background: linear-gradient(90deg, #6C63FF, #FF6584);
    animation: progress 3s linear forwards;
}

@keyframes progress {
    to {
        width: 0%;
    }
}

/* Vinyl Record Animation */
.vinyl-background {
    position: absolute;
    top: -50%;
    right: -50%;
    width: 300px;
    height: 300px;
    opacity: 0.15;
    pointer-events: none;
}

.vinyl-record {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
    animation: spin 20s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.vinyl-center {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 40px;
    background: radial-gradient(circle, #FF6584, #6C63FF);
    border-radius: 50%;
    transform: translate(-50%, -50%);
}

.vinyl-groove {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 80%;
    height: 80%;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
}

.vinyl-groove:nth-child(2) { width: 60%; height: 60%; }
.vinyl-groove:nth-child(3) { width: 40%; height: 40%; }
.vinyl-groove:nth-child(4) { width: 20%; height: 20%; }

.tonearm {
    position: absolute;
    top: 20%;
    left: 70%;
    transform-origin: 0% 0%;
    animation: playRecord 3s ease-in-out infinite;
}

@keyframes playRecord {
    0%, 100% { transform: rotate(0deg); }
    50% { transform: rotate(5deg); }
}

.tonearm-base {
    width: 20px;
    height: 20px;
    background: radial-gradient(circle, #FFD700, #FFA500);
    border-radius: 50%;
}

.tonearm-arm {
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #FFD700, #C0C0C0);
    transform: rotate(-45deg);
    transform-origin: left center;
    margin-left: 10px;
}

.tonearm-head {
    width: 15px;
    height: 15px;
    background: #C0C0C0;
    border-radius: 50%;
    position: relative;
    left: 85px;
    top: -10px;
}

/* Floating Music Notes */
.floating-notes {
    position: absolute;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
}

.music-note {
    position: absolute;
    font-size: 24px;
    color: rgba(108, 99, 255, 0.3);
    animation: floatNote 8s ease-in-out infinite;
}

.note1 { top: 10%; left: 10%; animation-delay: 0s; }
.note2 { top: 70%; left: 85%; animation-delay: 1s; font-size: 32px; }
.note3 { top: 30%; right: 15%; animation-delay: 2s; font-size: 28px; }
.note4 { bottom: 15%; left: 20%; animation-delay: 3s; font-size: 36px; }
.note5 { top: 50%; left: 5%; animation-delay: 4s; font-size: 20px; }
.note6 { bottom: 40%; right: 10%; animation-delay: 5s; font-size: 40px; }

@keyframes floatNote {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
        opacity: 0.3;
    }
    50% {
        transform: translateY(-50px) rotate(15deg);
        opacity: 0.6;
    }
}

/* Sound Waves */
.sound-waves {
    position: absolute;
    bottom: 20px;
    left: 20px;
    display: flex;
    gap: 3px;
    pointer-events: none;
}

.wave-bar {
    width: 3px;
    height: 20px;
    background: linear-gradient(to top, #6C63FF, #FF6584);
    border-radius: 2px;
    animation: soundWave 1s ease-in-out infinite;
}

.wave-bar:nth-child(1) { animation-delay: 0s; height: 15px; }
.wave-bar:nth-child(2) { animation-delay: 0.2s; height: 25px; }
.wave-bar:nth-child(3) { animation-delay: 0.4s; height: 35px; }
.wave-bar:nth-child(4) { animation-delay: 0.6s; height: 25px; }
.wave-bar:nth-child(5) { animation-delay: 0.8s; height: 15px; }
.wave-bar:nth-child(6) { animation-delay: 1s; height: 20px; }
.wave-bar:nth-child(7) { animation-delay: 1.2s; height: 30px; }
.wave-bar:nth-child(8) { animation-delay: 1.4s; height: 20px; }

@keyframes soundWave {
    0%, 100% {
        transform: scaleY(1);
    }
    50% {
        transform: scaleY(1.5);
    }
}

/* Modal Header */
.music-header {
    background: linear-gradient(135deg, rgba(108, 99, 255, 0.3), rgba(255, 101, 132, 0.1));
    backdrop-filter: blur(20px);
    padding: 25px 30px;
    position: relative;
    z-index: 1;
    border-bottom: 1px solid rgba(108, 99, 255, 0.3);
}

.header-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #6C63FF, #FF6584);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: rotate(45deg);
    animation: rotateIcon 3s ease-in-out infinite;
}

@keyframes rotateIcon {
    0%, 100% { transform: rotate(45deg); }
    50% { transform: rotate(60deg); }
}

.equalizer {
    display: flex;
    gap: 3px;
    transform: rotate(-45deg);
}

.eq-bar {
    width: 3px;
    background: white;
    border-radius: 2px;
    animation: equalizer 1s ease-in-out infinite;
}

.eq-bar:nth-child(1) { height: 15px; animation-delay: 0s; }
.eq-bar:nth-child(2) { height: 25px; animation-delay: 0.2s; }
.eq-bar:nth-child(3) { height: 35px; animation-delay: 0.4s; }
.eq-bar:nth-child(4) { height: 25px; animation-delay: 0.6s; }

@keyframes equalizer {
    0%, 100% { transform: scaleY(1); }
    50% { transform: scaleY(0.5); }
}

.header-content {
    flex: 1;
    margin-left: 15px;
}

.modal-title {
    font-size: 28px;
    font-weight: 800;
    background: linear-gradient(135deg, #6C63FF, #FF6584);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0 0 5px 0;
}

.header-subtitle {
    color: rgba(255, 255, 255, 0.6);
    margin: 0;
    font-size: 14px;
}

.music-close {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: white;
    transition: all 0.3s ease;
}

.music-close:hover {
    background: rgba(255, 101, 132, 0.3);
    transform: rotate(90deg);
}

/* Modal Body */
.music-body {
    padding: 40px;
    position: relative;
    z-index: 1;
}

/* OTP Card */
.otp-card {
    position: relative;
    background: linear-gradient(135deg, rgba(108, 99, 255, 0.1), rgba(255, 101, 132, 0.05));
    border-radius: 30px;
    padding: 40px;
    margin-bottom: 30px;
    border: 1px solid rgba(108, 99, 255, 0.2);
    backdrop-filter: blur(10px);
    overflow: hidden;
}

.card-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(108, 99, 255, 0.1), transparent);
    animation: glowPulse 3s ease-in-out infinite;
}

@keyframes glowPulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

.otp-display {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.otp-digit {
    width: 55px;
    height: 70px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 800;
    color: white;
    border: 2px solid rgba(108, 99, 255, 0.5);
    transition: all 0.3s ease;
}

.otp-digit.filled {
    border-color: #00ff88;
    box-shadow: 0 0 15px rgba(0, 255, 136, 0.3);
}

.otp-digit.error-shake {
    animation: shake 0.5s ease;
    border-color: #ff4466;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Input Section */
.input-section {
    position: relative;
    margin-bottom: 20px;
}

.otp-input {
    width: 100%;
    padding: 20px;
    font-size: 24px;
    text-align: center;
    letter-spacing: 10px;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(108, 99, 255, 0.3);
    border-radius: 20px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.otp-input:focus {
    outline: none;
    border-color: #6C63FF;
    background: rgba(108, 99, 255, 0.1);
    box-shadow: 0 0 30px rgba(108, 99, 255, 0.3);
}

.otp-input.error {
    border-color: #ff4466;
    animation: shake 0.5s ease;
}

.input-label {
    position: absolute;
    left: 20px;
    top: -12px;
    background: #0a0a2a;
    padding: 0 10px;
    color: rgba(108, 99, 255, 0.8);
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.input-highlight {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #6C63FF, #FF6584);
    transition: width 0.3s ease;
}

.otp-input:focus ~ .input-highlight {
    width: 100%;
}

.error-message {
    color: #FF6584;
    font-size: 13px;
    text-align: center;
    margin-top: 10px;
    display: none;
}

.error-message.show {
    display: block;
    animation: shake 0.5s ease;
}

/* Timer & Resend */
.timer-resend {
    text-align: center;
    margin-top: 30px;
}

.timer-container {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto 15px;
}

.timer-ring {
    position: absolute;
    top: 0;
    left: 0;
}

.timer-ring-progress {
    stroke-dasharray: 219.8;
    transition: stroke-dashoffset 1s linear;
}

.timer-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.timer-seconds {
    font-size: 24px;
    font-weight: 800;
    color: white;
    display: block;
    line-height: 1;
}

.timer-label {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.6);
}

.resend-button {
    background: none;
    border: none;
    color: #6C63FF;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
}

.resend-button.show {
    opacity: 1;
    visibility: visible;
}

.resend-button:hover {
    background: rgba(108, 99, 255, 0.1);
    transform: translateY(-2px);
}

/* Modal Footer */
.music-footer {
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    padding: 20px 30px;
    border-top: 1px solid rgba(108, 99, 255, 0.2);
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    position: relative;
    z-index: 1;
}

.btn-cancel {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 12px 25px;
    border-radius: 15px;
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.btn-verify {
    background: linear-gradient(135deg, #6C63FF, #FF6584);
    border: none;
    padding: 12px 30px;
    border-radius: 15px;
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-verify:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(108, 99, 255, 0.4);
}

.btn-verify:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-loader {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    display: none;
    animation: spin 0.6s linear infinite;
}

.btn-verify.loading .btn-text,
.btn-verify.loading i {
    display: none;
}

.btn-verify.loading .btn-loader {
    display: block;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Modal Animation */
.modal.fade .modal-dialog {
    transform: scale(0.9) translateY(-50px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
}

/* Responsive */
@media (max-width: 768px) {
    .music-body {
        padding: 25px;
    }
    
    .otp-digit {
        width: 45px;
        height: 60px;
        font-size: 28px;
    }
    
    .modal-title {
        font-size: 22px;
    }
    
    .btn-cancel,
    .btn-verify {
        padding: 10px 20px;
    }
    
    .toast-container {
        top: 10px;
        right: 10px;
        left: 10px;
    }
    
    .music-toast {
        min-width: auto;
    }
}

.required {
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
}
.alert {
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 4px;
}
.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}
.text-success {
    color: #28a745;
}
.disabled_btn {
    opacity: 0.6;
    cursor: not-allowed;
}
#otp_code {
    font-size: 24px;
    letter-spacing: 5px;
    text-align: center;
    font-weight: bold;
}
</style>

<script>
var site_url = "<?php echo site_url();?>";
var resendTimer;
var timerSeconds = 60;
var isProcessingLogin = false;

function togglePasswordVisibility(event) {
    event.preventDefault();
    var passwordInput = document.getElementById("password");
    var eyeIcon = document.getElementById("eye_icon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.src = "<?php echo theme_url(); ?>images/eye2.svg";
    } else {
        passwordInput.type = "password";
        eyeIcon.src = "<?php echo theme_url(); ?>images/eye.svg";
    }
}

// Toast notification for OTP modal
function showToastMessage(type, title, message, duration) {
    var toastContainer = $('#toastContainer');
    var icons = {
        success: '<i class="fas fa-check"></i>',
        error: '<i class="fas fa-times"></i>',
        warning: '<i class="fas fa-exclamation-triangle"></i>',
        info: '<i class="fas fa-info"></i>'
    };
    var colors = {
        success: '#00ff88',
        error: '#ff4466',
        warning: '#ffaa00',
        info: '#6C63FF'
    };
    
    var toast = $('<div class="music-toast ' + type + '">' +
        '<div class="toast-icon" style="background: ' + colors[type] + '20; color: ' + colors[type] + ';">' + icons[type] + '</div>' +
        '<div class="toast-content">' +
            '<div class="toast-title">' + title + '</div>' +
            '<div class="toast-message">' + message + '</div>' +
        '</div>' +
        '<button class="toast-close" onclick="$(this).closest(\'.music-toast\').fadeOut(300, function(){ $(this).remove(); })">' +
            '<i class="fas fa-times"></i>' +
        '</button>' +
        '<div class="progress-container">' +
            '<div class="progress-bar" style="animation: progress ' + (duration/1000) + 's linear forwards;"></div>' +
        '</div>' +
    '</div>');
    
    toastContainer.append(toast);
    setTimeout(function() { toast.fadeOut(300, function() { $(this).remove(); }); }, duration);
}

function startResendTimer() {
    timerSeconds = 60;
    var btnResend = $('#btn_resend');
    var timerContainer = $('.timer-container');
    
    btnResend.removeClass('show');
    timerContainer.css('opacity', '1');
    
    if (resendTimer) clearInterval(resendTimer);
    
    var ring = $('.timer-ring-progress');
    var circumference = 219.8;
    
    resendTimer = setInterval(function() {
        timerSeconds--;
        $('#timerSeconds').text(timerSeconds);
        var offset = circumference - (timerSeconds / 60) * circumference;
        ring.css('stroke-dashoffset', offset);
        
        if (timerSeconds <= 0) {
            clearInterval(resendTimer);
            btnResend.addClass('show');
            timerContainer.css('opacity', '0.5');
            showToastMessage('warning', 'OTP Expired', 'Your verification code has expired. Please request a new one.', 5000);
            $('#btn_verify').prop('disabled', true);
        } else if (timerSeconds === 10) {
            showToastMessage('warning', 'Time Running Out', 'OTP will expire in 10 seconds', 3000);
        }
    }, 1000);
}

function resetOTPModal() {
    timerSeconds = 60;
    $('#timerSeconds').text('60');
    $('#otp_code').val('').trigger('input');
    $('#otp_code').removeClass('error');
    $('#err_otp_code').html('');
    $('#btn_verify').prop('disabled', true).removeClass('loading');
    $('.otp-digit').removeClass('filled error-shake');
    $('.otp-digit').css({ 'border-color': '', 'box-shadow': '' });
    var ring = $('.timer-ring-progress');
    var circumference = 219.8;
    ring.css('stroke-dashoffset', '0');
}

function verifyOTP() {
    var otp = $('#otp_code').val();
    var verifyBtn = $('#btn_verify');
    
    if(otp.length !== 6) {
        showToastMessage('warning', 'Invalid OTP', 'Please enter the complete 6-digit verification code', 3000);
        $('#otp_code').addClass('error');
        $('.otp-digit').addClass('error-shake');
        setTimeout(function() {
            $('#otp_code').removeClass('error');
            $('.otp-digit').removeClass('error-shake');
        }, 500);
        return;
    }
    
    verifyBtn.addClass('loading').prop('disabled', true);
    
    $.ajax({
        url: site_url + 'user/verify_email_otp',
        type: 'post',
        data: {
            otp_code: otp,
            temp_user_id: $('#temp_user_id').val(),
            member_type: $('#temp_member_type').val(),
            remember: $('#remember_me').val(),
            action: 'verify'
        },
        dataType: 'json',
        timeout: 30000
    }).done(function(data) {
        if(data.status == '1') {
            showToastMessage('success', 'Verification Successful', data.msg, 2000);
            $('.otp-digit').css({
                'border-color': '#00ff88',
                'box-shadow': '0 0 20px rgba(0, 255, 136, 0.5)'
            });
            setTimeout(function() {
                $('#verify_otp_modal').modal('hide');
                window.location.href = data.redirect_url;
            }, 1500);
        } else {
            showToastMessage('error', 'Verification Failed', data.msg, 4000);
            $('#otp_code').addClass('error');
            $('.otp-digit').addClass('error-shake');
            setTimeout(function() {
                $('#otp_code').removeClass('error');
                $('.otp-digit').removeClass('error-shake');
            }, 500);
            $('#otp_code').val('').focus();
            verifyBtn.removeClass('loading').prop('disabled', true);
        }
    }).fail(function() {
        showToastMessage('error', 'Error', 'An error occurred. Please try again.', 4000);
        verifyBtn.removeClass('loading').prop('disabled', false);
    });
}

function resendOTP() {
    var btnResend = $('#btn_resend');
    var temp_user_id = $('#temp_user_id').val();
    
    btnResend.prop('disabled', true);
    
    $.ajax({
        url: site_url + 'user/resend_email_otp',
        type: 'post',
        data: {temp_user_id: temp_user_id},
        dataType: 'json',
        timeout: 30000
    }).done(function(data) {
        if(data.status == '1') {
            showToastMessage('success', 'OTP Sent', data.msg, 3000);
            resetOTPModal();
            startResendTimer();
            $('#otp_code').focus();
        } else {
            showToastMessage('error', 'Failed', data.msg, 4000);
        }
        btnResend.prop('disabled', false);
    }).fail(function() {
        showToastMessage('error', 'Error', 'Failed to resend OTP. Please try again.', 4000);
        btnResend.prop('disabled', false);
    });
}


$('.btn-cancel, .music-close').click(function() {
    clearOTPSession();
    $('#verify_otp_modal').modal('hide');
});


// Login form submission
$('#login_btn').click(function(e){
    e.preventDefault();
    
    if(isProcessingLogin) return;
    isProcessingLogin = true;
    
    var frmobj = $('#login_frm');
    var action_url = frmobj.attr('action');
    var user_name = $('#user_name').val();
    var password = $('#password').val();
    var member_type = $('.member_type').val();
    var remember = $('#flexCheckChecked').is(':checked') ? 'Y' : '';
    
    $('.required, .alert').remove();
    $('#err_user_name, #err_password, #err_msg').html('');
    
    if(user_name.trim() == '') {
        $('#err_user_name').html('<div class="required">Email address is required</div>');
        isProcessingLogin = false;
        return;
    }
    
    if(password.trim() == '') {
        $('#err_password').html('<div class="required">Password is required</div>');
        isProcessingLogin = false;
        return;
    }
    
    var loginBtn = $(this);
    var originalText = loginBtn.text();
    loginBtn.text('Verifying...').prop('disabled', true);
    
    $.ajax({
        url: action_url,
        type: 'post',
        data: {
            user_name: user_name, 
            password: password, 
            member_type: member_type, 
            action: 'Y', 
            ajax_request: true,
            remember: remember
        },
        dataType: 'json',
        timeout: 30000
    }).done(function(data){
        if(data.status == 'expired') {
            showErrorMessage('Session expired. Please refresh the page.');
            loginBtn.text(originalText).prop('disabled', false);
            isProcessingLogin = false;
            return;
        }
        
        if(data.status == '1'){
            $('#temp_user_id').val(data.temp_user_id);
            $('#temp_member_type').val(member_type);
            $('#remember_me').val(remember);
            resetOTPModal();
            startResendTimer();
            $('#verify_otp_modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#verify_otp_modal').modal('show');
            setTimeout(function() { $('#otp_code').focus(); }, 500);
        } else {
            if(data.error_flds && Object.keys(data.error_flds).length){
                $.each(data.error_flds, function(m,n){
                    $('#err_' + m).html('<div class="required">' + n + '</div>');
                });
            }
            if(data.msg && data.msg != ''){
                showErrorMessage(data.msg);
            } else {
                showErrorMessage('Invalid username or password');
            }
        }
    }).fail(function(xhr, status, error) {
        console.log('AJAX Error:', error);
        showErrorMessage('An error occurred. Please try again.');
    }).always(function(){
        loginBtn.text(originalText).prop('disabled', false);
        isProcessingLogin = false;
    });
});

// OTP input handling
$('#otp_code').on('input', function() {
    var value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
    this.value = value;
    
    $('.otp-digit').each(function(index) {
        if(index < value.length) {
            $(this).text(value[index]).addClass('filled');
        } else {
            $(this).text('_').removeClass('filled');
        }
    });
    
    if(value.length === 6) {
        $('#btn_verify').prop('disabled', false);
    } else {
        $('#btn_verify').prop('disabled', true);
    }
});

$('#otp_code').on('paste', function(e) {
    var pastedData = e.originalEvent.clipboardData.getData('text');
    var cleanedData = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
    $(this).val(cleanedData).trigger('input');
});

$('#otp_code').on('keypress', function(e) {
    if(e.which === 13 && $(this).val().length === 6) {
        verifyOTP();
    }
});

$('#btn_verify').click(verifyOTP);
$('#btn_resend').click(resendOTP);

$('#verify_otp_modal').on('hidden.bs.modal', function() {
    if(resendTimer) clearInterval(resendTimer);
    resetOTPModal();
    clearOTPSession();  // Add this line to clear OTP session on modal close
});

$('#user_name, #password').on('keyup', function() {
    $('#err_user_name, #err_password, #err_msg').html('');
    $('.required, .alert').remove();
});

$('#password, #user_name').on('keypress', function(e) {
    if(e.which == 13 || e.keyCode == 13) {
        $('#login_btn').click();
    }
});

function showErrorMessage(message) {
    $('#err_msg').html('<div class="alert alert-danger">' + message + '</div>');
    setTimeout(function() { $('#err_msg').fadeOut(500, function() { $(this).html('').show(); }); }, 5000);
}



// Check if user is trying to access protected pages without OTP verification
function checkAuthStatus() {
    // This function runs on every page load to verify session
    $.ajax({
        url: site_url + 'user/check_session_status',
        type: 'post',
        dataType: 'json',
        timeout: 10000
    }).done(function(data) {
        if(data.status == 'expired') {
            // Session expired, redirect to login
            if(window.location.pathname.indexOf('/members') !== -1) {
                window.location.href = site_url + 'login';
            }
        }
    });
}

// Call this on page load for protected pages
if(window.location.pathname.indexOf('/members') !== -1) {
    checkAuthStatus();
}


// Clear OTP session when modal is closed (only OTP data, not login session)
function clearOTPSession() {
    $.ajax({
        url: site_url + 'user/clear_otp_session_ajax',
        type: 'post',
        data: {},
        dataType: 'json',
        timeout: 5000
    }).done(function(data) {
        console.log('OTP session cleared');
    }).fail(function(xhr, status, error) {
        console.log('Failed to clear OTP session:', error);
    });
}
</script>

<?php $this->load->view('bottom_application',array('has_footer'=>false));?>