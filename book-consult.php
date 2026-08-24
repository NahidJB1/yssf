<?php
// book-consult.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Consult - YS Study Focus</title>
  
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    /* Reset & Global */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
    body { background-color: #f2f5f9; color: #333; }
    
    /* Navbar styles (mimicking index.html) */
    header {
      position: relative;
      height: 65vh; 
      background-color: #000;
      color: yellow;
      overflow: hidden;
      display: flex;
      align-items: center; 
    }
    
    .nav-menu {
      position: absolute;
      top: 80px; 
      right: 25px;
      width: 220px;
      background: rgba(0, 20, 40, 0.95);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 15px 0;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      z-index: 2000;
    }
    /* Menu Links */
    .nav-menu a {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #fff;
      padding: 12px 25px;
      text-decoration: none;
      font-size: 16px;
      border-left: 3px solid transparent;
      transition: all 0.2s ease;
    }
    .nav-menu a:hover {
      background: rgba(255, 255, 255, 0.1);
      border-left: 3px solid #FFD700;
      color: #FFD700;
      padding-left: 30px;
    }
    .nav-menu a i {
      width: 20px;
      text-align: center;
      color: #00BFFF;
    }

    .header-slider {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
    }
    .slide {
      position: absolute;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      transition: opacity 1.5s ease-in-out;
    }
    .slide.active {
      opacity: 1;
    }
    .header-slider::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4); 
      z-index: 1;
    }
    .text-overlay {
      position: absolute;
      top: 30%;
      left: 5%;
      width: 90%;
      z-index: 1;
      color: white;
    }
    .text-overlay h3 { font-size: 18px; }
    .text-overlay h1 { font-size: 26px; margin: 10px 0; }
    .start-btn {
      background-color: red; color: white; padding: 10px 20px;
      display: inline-block; margin-top: 20px; text-decoration: none;
      font-weight: bold; border-radius: 4px;
    }

    /* Form Container */
    .form-wrapper {
      display: flex; justify-content: center; align-items: center;
      padding: 40px 20px; min-height: calc(100vh - 200px);
    }
    .form-container {
      background: #fff; width: 100%; max-width: 650px;
      border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      overflow: hidden; padding: 40px;
      position: relative;
      z-index: 10;
      margin-top: -80px; /* Pull it slightly over the hero section */
    }

    /* Header */
    .form-header { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #eaeaea; padding-bottom: 20px; }
    .form-header h2 { color: #0077ff; font-size: 28px; margin-bottom: 8px; font-weight: 700; }
    .form-header p { color: #666; font-size: 15px; }

    /* Sections */
    .section-title {
      color: #00b4d8; font-size: 14px; font-weight: 700; text-transform: uppercase;
      letter-spacing: 1px; margin: 25px 0 15px 0; display: flex; align-items: center; gap: 8px;
    }
    .section-title i { font-size: 16px; }

    /* Form Grid */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full-width { grid-column: 1 / -1; }

    /* Inputs */
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #222; }
    .form-group label span { color: #888; font-size: 12px; font-weight: normal; }
    
    .input-icon-wrapper { position: relative; }
    .input-icon-wrapper i {
      position: absolute; left: 15px; top: 50%; transform: translateY(-50%);
      color: #999; font-size: 16px;
    }
    
    input[type="text"], input[type="tel"], input[type="email"], select {
      width: 100%; padding: 12px 15px; border: 1px solid #ddd;
      border-radius: 8px; font-size: 15px; color: #333; background: #fdfdfd;
      transition: all 0.3s ease;
    }
    .input-icon-wrapper input { padding-left: 45px; }
    
    input:focus, select:focus {
      outline: none; border-color: #00b4d8; background: #fff;
      box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.15);
    }
    
    /* Result Custom Dropdowns */
    .score-container { display: flex; align-items: center; gap: 10px; }
    .score-container select { width: 33%; }
    .score-container span { font-size: 24px; font-weight: bold; color: #555; }

    /* Custom Radio for Passport */
    .radio-group { display: flex; gap: 15px; }
    .radio-option {
      flex: 1; position: relative;
    }
    .radio-option input { display: none; }
    .radio-label {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 15px; border: 1px solid #ddd; border-radius: 8px;
      cursor: pointer; font-weight: 600; color: #555; transition: all 0.2s;
      background: #fff;
    }
    .radio-option input:checked + .radio-label {
      border-color: #0077ff; color: #0077ff; background: rgba(0, 119, 255, 0.05);
    }
    .radio-label i { font-size: 18px; color: #aaa; }
    .radio-option input:checked + .radio-label i { color: #0077ff; }

    /* Submit Button */
    .btn-submit {
      width: 100%; padding: 16px; background-color: #00a8e8; color: white;
      border: none; border-radius: 8px; font-size: 18px; font-weight: 600;
      cursor: pointer; transition: background-color 0.3s; margin-top: 25px;
      display: flex; justify-content: center; align-items: center; gap: 10px;
    }
    .btn-submit:hover { background-color: #0096cf; }
    
    /* Alerts */
    .message {
      padding: 15px; border-radius: 8px; margin-bottom: 20px; display: none; text-align: center; font-weight: 500;
    }
    .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; display: block; }
    .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; display: block; }

    @media (max-width: 600px) {
      .form-grid { grid-template-columns: 1fr; gap: 10px; }
      .form-container { padding: 25px 20px; }
      .radio-group { flex-direction: column; gap: 10px; }
      .nav-menu { display: none; } /* On mobile, usually hidden behind a hamburger, we keep it simple here */
    }

    /* Success Popup Modal */
    .modal-overlay {
      position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
      background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px);
      display: flex; justify-content: center; align-items: center;
      z-index: 3000; opacity: 0; visibility: hidden; transition: all 0.3s ease;
    }
    .modal-overlay.active { opacity: 1; visibility: visible; }
    .success-modal {
      background: #fff; width: 90%; max-width: 450px; border-radius: 16px;
      padding: 40px 30px; text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,0.15);
      transform: translateY(30px) scale(0.95); opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .modal-overlay.active .success-modal { transform: translateY(0) scale(1); opacity: 1; }
    
    .success-icon-container {
      width: 80px; height: 80px; background: #d4edda; border-radius: 50%;
      display: flex; justify-content: center; align-items: center; margin: 0 auto 20px;
      color: #28a745; font-size: 40px; animation: bounceIn 0.8s ease backwards; animation-delay: 0.2s;
    }
    .success-modal h3 { color: #333; font-size: 24px; margin-bottom: 10px; font-weight: 700; }
    .success-modal p { color: #666; font-size: 16px; margin-bottom: 25px; line-height: 1.5; }
    .btn-close-modal {
      background: #00a8e8; color: white; padding: 12px 30px; border: none; border-radius: 8px;
      font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s;
    }
    .btn-close-modal:hover { background: #0096cf; }
    
    @keyframes bounceIn {
      0% { transform: scale(0); opacity: 0; }
      50% { transform: scale(1.2); opacity: 1; }
      100% { transform: scale(1); opacity: 1; }
    }

    /* Footer styles (copied from index.html) */
    footer {
      background-color: #004080;
      color: white;
      padding: 30px 20px 10px 20px;
      font-family: Arial, sans-serif;
    }
    .footer-container { display: flex; justify-content: space-around; flex-wrap: wrap; text-align: center; gap: 20px; max-width: 1100px; margin: auto; }
    .office p { font-size: 14px; line-height: 1.6; }
    .office strong { color: #fff; font-size: 16px; }
    .footer-logo img { width: 120px; }
    .footer-bottom { margin-top: 30px; display: flex; justify-content: center; }
    .menu { display: flex; gap: 15px; list-style: none; }
    .menu li a {
      color: #004080; font-size: 20px; display: flex; align-items: center; justify-content: center;
      width: 40px; height: 40px; border-radius: 50%; background: #fff; transition: 0.3s; text-decoration: none;
    }
    .menu li a:hover { transform: translateY(-3px); }
    .dev-bar-container { background: #002b5e; text-align: center; padding: 15px; margin-top: 20px; }
    .dev-bar-link { text-decoration: none; color: #ccc; font-size: 13px; }
    .dev-name { color: #fff; font-weight: bold; }
  </style>
</head>
<body>

  <!-- Header / Navbar -->
  <header>
    <div class="nav-menu" id="navMenu">
      <a href="index"><i class="fas fa-home"></i> Home</a>
      <a href="index#about"><i class="fas fa-info-circle"></i> About Us</a>
      <a href="pages/general/university-requirements"><i class="fas fa-list-check"></i> Requirements</a>
      <a href="#contact"><i class="fas fa-envelope"></i> Contact Us</a>
    </div>

    <div class="header-slider">
      <img src="assets/images/cityu_main.jpg" class="slide active" alt="City University">
      <img src="assets/images/iukl_main.jpg" class="slide" alt="IUKL">
      <img src="assets/images/cyberjaya_main.jpg" class="slide" alt="University of Cyberjaya">
      <img src="assets/images/inti_main.jpg" class="slide" alt="INTI">
      <img src="assets/images/segi_main.jpg" class="slide" alt="SEGi">
      <img src="assets/images/binary_main.jpg" class="slide" alt="Binary">
      <img src="assets/images/taylors_main.jpg" class="slide" alt="Taylors">
      <img src="assets/images/kings_main.jpg" class="slide" alt="Kings">
    </div>

    <div class="text-overlay">
      <h3>STUDENT GATEWAY TO MALAYSIA</h3>
      <h1>YS STUDY FOCUS, THE BEST AND THE MOST TRUSTED PLACE TO START YOUR PROCESSING FOR MALAYSIA</h1>
      <a href="https://forms.gle/iFXQ3ohvY4JqCbWA9" target="_blank" rel="noopener noreferrer" class="start-btn">START YOUR PROCESSING</a>
    </div>
  </header>

  <div class="form-wrapper">
    <div class="form-container">
      
      <div class="form-header">
        <h2>Book Your Free Consultation</h2>
        <p>Take the first step towards your dream education.</p>
      </div>

      <div id="form-message" class="message"></div>

      <form id="consultForm">
        
        <!-- PERSONAL DETAILS -->
        <h3 class="section-title"><i class="fas fa-user"></i> Personal Details</h3>
        
        <div class="form-group full-width">
          <label for="full_name">Full Name <span>(As per Passport)</span></label>
          <input type="text" id="full_name" name="full_name" required placeholder="e.g. Nahid Jahan">
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <div class="input-icon-wrapper">
              <i class="fas fa-phone-alt"></i>
              <input type="tel" id="phone_number" name="phone_number" required placeholder="e.g. 017xxxxxxxx">
            </div>
          </div>
          <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-icon-wrapper">
              <i class="fas fa-envelope"></i>
              <input type="email" id="email" name="email" required placeholder="student@example.com">
            </div>
          </div>
        </div>

        <!-- ACADEMIC PROFILE -->
        <h3 class="section-title"><i class="fas fa-graduation-cap"></i> Academic Profile</h3>

        <div class="form-group full-width">
          <label for="highest_education">Education Qualification</label>
          <select id="highest_education" name="highest_education" required onchange="handleEducationChange()">
            <option value="" disabled selected>Select your highest qualification</option>
            <option value="SSC">SSC</option>
            <option value="HSC">HSC</option>
            <option value="Diploma">Diploma</option>
            <option value="Bachelor">Bachelor</option>
            <option value="Masters">Masters</option>
            <option value="Other">Other - Write Manually</option>
          </select>
        </div>

        <div id="other_education_group" class="form-group full-width" style="display: none;">
          <label for="other_education">Specify Other Qualification</label>
          <input type="text" id="other_education" name="other_education" placeholder="e.g. O Levels, A Levels">
        </div>

        <div class="form-grid">
          <div class="form-group" id="result-section" style="display:none;">
            <label id="result_label" for="score_main">Result/Score</label>
            <div class="score-container">
              <select id="score_main" onchange="handleScoreChange()"></select>
              <span>.</span>
              <select id="score_dec1"></select>
              <select id="score_dec2"></select>
            </div>
            <input type="hidden" id="result_score" name="result_score">
          </div>

          <div class="form-group">
            <label for="passing_year">Passing Year</label>
            <select id="passing_year" name="passing_year" required>
              <option value="" disabled selected>Select Year</option>
            </select>
          </div>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="interested_to_study">Interested to Study</label>
            <input type="text" id="interested_to_study" name="interested_to_study" required placeholder="e.g. Computer Science, Business, Engineering...">
          </div>
          <div class="form-group">
            <label for="budget">Budget</label>
            <select id="budget" name="budget" required>
              <option value="" disabled selected>Select your budget</option>
              <option value="BDT 500,000">BDT 500,000</option>
              <option value="BDT 600,000">BDT 600,000</option>
              <option value="BDT 700,000">BDT 700,000</option>
              <option value="BDT 800,000">BDT 800,000</option>
              <option value="BDT 900,000">BDT 900,000</option>
              <option value="BDT 1,000,000/+">BDT 1,000,000/+</option>
            </select>
          </div>
        </div>

        <!-- PASSPORT STATUS -->
        <h3 class="section-title"><i class="fas fa-passport"></i> Passport Status</h3>
        
        <div class="form-group full-width">
          <label>Do you have a valid passport?</label>
          <div class="radio-group">
            <label class="radio-option">
              <input type="radio" name="has_passport" value="Yes, I do" required>
              <div class="radio-label">
                <i class="fas fa-check-circle"></i> Yes, I do
              </div>
            </label>
            <label class="radio-option">
              <input type="radio" name="has_passport" value="No, not yet">
              <div class="radio-label">
                <i class="fas fa-times-circle"></i> No, not yet
              </div>
            </label>
          </div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
          Submit Application <i class="fas fa-paper-plane"></i>
        </button>

      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer id="contact">
    <div class="footer-container">
      <div class="office left">
        <p><strong>Bangladesh Office</strong><br>
        80/A/1 Shahjalal Tower, Level-07,<br>
        CID office এর opposite, Malibagh Mor, Dhaka</p>
      </div>
      <div class="footer-logo">
        <img src="assets/images/ys_logo.png" alt="YS Logo">
      </div>
      <div class="office right">
        <p><strong>Malaysia Office</strong><br>
        De Tropicana, 52, Jalan Kuchai Maju 13<br>
        Kuchai Entrepreneurs Park<br>
        58200 Kuala Lumpur, Malaysia</p>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="menu">
        <li><a href="https://web.facebook.com/people/YS-Study-Focus/61571694410231/?_rdc=1&_rdr" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
        <li><a href="https://wa.me/601139660706" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
        <li><a href="https://youtube.com/@sabrina367sabu?si=GRfgbwVUAxxH5Ke_" target="_blank"><i class="fab fa-youtube"></i></a></li>
        <li><a href="https://www.tiktok.com/@sabu_555?_r=1&_t=ZS-92OvdlsEIcy" target="_blank"><i class="fab fa-tiktok"></i></a></li>
      </div>
    </div>
  </footer>
  <div class="dev-bar-container">
    <a href="https://nahidjahanbhuiyan.com" target="_blank" class="dev-bar-link">
      <span class="dev-content">
        <i class="fas fa-code code-icon"></i>
        Developed by: <span class="dev-name">&lt; Bhuiyan Mohamed Nahid Jahan /&gt;</span>
        <i class="fas fa-external-link-alt" style="font-size: 10px; margin-left: 5px;"></i>
      </span>
    </a>
  </div>

  <script>
    // Initialize Passing Year Dropdown
    const yearSelect = document.getElementById('passing_year');
    for (let i = 2027; i >= 2017; i--) {
        yearSelect.innerHTML += `<option value="${i}">${i}</option>`;
    }

    // Initialize Decimal Dropdowns (0-9)
    const dec1 = document.getElementById('score_dec1');
    const dec2 = document.getElementById('score_dec2');
    for (let i = 0; i <= 9; i++) {
        dec1.innerHTML += `<option value="${i}">${i}</option>`;
        dec2.innerHTML += `<option value="${i}">${i}</option>`;
    }

    let currentMax = 0;

    function handleEducationChange() {
        const edu = document.getElementById('highest_education').value;
        const resultSection = document.getElementById('result-section');
        const scoreMain = document.getElementById('score_main');
        const resultLabel = document.getElementById('result_label');
        const otherEduGroup = document.getElementById('other_education_group');

        // Reset
        scoreMain.innerHTML = '';
        dec1.value = '0';
        dec2.value = '0';
        dec1.disabled = false;
        dec2.disabled = false;
        
        if (edu === 'Other') {
            otherEduGroup.style.display = 'block';
            document.getElementById('other_education').required = true;
            resultSection.style.display = 'none';
            return;
        } else {
            otherEduGroup.style.display = 'none';
            document.getElementById('other_education').required = false;
        }

        resultSection.style.display = 'block';

        if (edu === 'SSC' || edu === 'HSC' || edu === 'Diploma') {
            currentMax = 5;
            resultLabel.textContent = 'GPA (Max 5.00)';
            for (let i = 1; i <= 5; i++) {
                scoreMain.innerHTML += `<option value="${i}">${i}</option>`;
            }
        } else if (edu === 'Bachelor' || edu === 'Masters') {
            currentMax = 4;
            resultLabel.textContent = 'CGPA (Max 4.00)';
            for (let i = 1; i <= 4; i++) {
                scoreMain.innerHTML += `<option value="${i}">${i}</option>`;
            }
        }
        scoreMain.value = "1";
    }

    function handleScoreChange() {
        const scoreMain = document.getElementById('score_main').value;
        if (parseInt(scoreMain) === currentMax) {
            dec1.value = '0';
            dec2.value = '0';
            dec1.disabled = true;
            dec2.disabled = true;
        } else {
            dec1.disabled = false;
            dec2.disabled = false;
        }
    }

    document.getElementById('consultForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const msgDiv = document.getElementById('form-message');
        const formData = new FormData(this);
        
        let finalScore = "";
        const edu = document.getElementById('highest_education').value;
        
        if (edu === 'Other') {
            const otherEdu = document.getElementById('other_education').value;
            formData.set('highest_education', 'Other: ' + otherEdu);
            finalScore = "N/A";
        } else {
            const main = document.getElementById('score_main').value;
            const d1 = document.getElementById('score_dec1').value;
            const d2 = document.getElementById('score_dec2').value;
            finalScore = `${main}.${d1}${d2}`;
        }
        formData.set('result_score', finalScore);

        btn.innerHTML = 'Submitting... <i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        msgDiv.className = 'message';
        msgDiv.textContent = '';

        fetch('api/submit-consult', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = 'Submit Application <i class="fas fa-paper-plane"></i>';
            btn.disabled = false;
            if (data.status === 'success') {
                // Show Success Modal instead of top message
                document.getElementById('successModal').classList.add('active');
                
                this.reset();
                document.getElementById('result-section').style.display = 'none';
                document.getElementById('other_education_group').style.display = 'none';
                document.querySelectorAll('.radio-option input').forEach(input => input.checked = false);
            } else {
                msgDiv.className = 'message error';
                msgDiv.textContent = data.message || 'An error occurred.';
            }
        })
        .catch(error => {
            btn.innerHTML = 'Submit Application <i class="fas fa-paper-plane"></i>';
            btn.disabled = false;
            msgDiv.className = 'message error';
            msgDiv.textContent = 'A network error occurred. Please try again.';
        });
    });

    // Close Modal Logic
    function closeSuccessModal() {
        document.getElementById('successModal').classList.remove('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Hero Slider Logic
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');

    if (slides.length > 0) {
      function nextSlide() {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
      }
      setInterval(nextSlide, 4000);
    }
  </script>

  <!-- Success Modal Overlay -->
  <div class="modal-overlay" id="successModal">
    <div class="success-modal">
      <div class="success-icon-container">
        <i class="fas fa-check"></i>
      </div>
      <h3>Application Received!</h3>
      <p>Thank you for booking a consultation. Our team will review your details and contact you very soon to discuss your journey to Malaysia.</p>
      <button class="btn-close-modal" onclick="closeSuccessModal()">Close</button>
    </div>
  </div>
</body>
</html>

