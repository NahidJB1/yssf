<?php
// book-consult.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Consult - YS Study Focus</title>
  <meta name="description" content="YS Study Focus - Your trusted educational consultant for studying in Malaysia. Get expert guidance, fee structures, and admission assistance for top Malaysian universities." />
<meta name="keywords" content="Study in Malaysia, Malaysian Universities, YS Study Focus, study abroad, international students, Malaysia scholarships, student visa Malaysia, Book Consult - YS Study Focus" />
<meta property="og:title" content="Book Consult - YS Study Focus" />
<meta property="og:description" content="YS Study Focus - Your trusted educational consultant for studying in Malaysia. Get expert guidance, fee structures, and admission assistance for top Malaysian universities." />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://nahidjb1.github.io/yssf/book-consult.php" />
<meta property="og:image" content="https://nahidjb1.github.io/yssf/assets/images/ys_logo.png" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="Book Consult - YS Study Focus" />
<meta name="twitter:description" content="YS Study Focus - Your trusted educational consultant for studying in Malaysia. Get expert guidance, fee structures, and admission assistance for top Malaysian universities." />
<meta name="twitter:image" content="https://nahidjb1.github.io/yssf/assets/images/ys_logo.png" />
<link rel="canonical" href="https://nahidjb1.github.io/yssf/book-consult.php" />
  
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Design System CSS -->
  <link rel="stylesheet" href="assets/css/global.css">
  
  <style>
    /* Page-specific form styles */
    .form-wrapper {
      padding: var(--spacing-xl) 0;
    }
    
    .form-header { text-align: center; margin-bottom: var(--spacing-lg); border-bottom: 1px solid var(--color-border); padding-bottom: var(--spacing-md); }
    .form-header h2 { color: var(--color-primary); font-size: 28px; margin-bottom: 8px; font-weight: 700; }
    .form-header p { color: var(--color-text-light); font-size: 15px; }

    .section-title {
      color: var(--color-accent); font-size: 14px; font-weight: 700; text-transform: uppercase;
      letter-spacing: 1px; margin: 25px 0 15px 0; display: flex; align-items: center; gap: 8px;
    }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full-width { grid-column: 1 / -1; }
    
    @media (max-width: 768px) {
      .form-grid { grid-template-columns: 1fr; gap: 15px; }
    }

    /* Result Custom Dropdowns */
    .score-container { display: flex; align-items: center; gap: 10px; }
    .score-container select { width: 33%; }
    .score-container span { font-size: 24px; font-weight: bold; color: var(--color-text-light); }

    /* Custom Radio for Passport */
    .radio-group { display: flex; gap: 15px; }
    .radio-option {
      flex: 1; position: relative;
    }
    .radio-option input { display: none; }
    .radio-label {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 15px; border: 1px solid var(--color-border); border-radius: var(--radius-md);
      cursor: pointer; font-weight: 600; color: var(--color-text); transition: all 0.2s;
      background: var(--color-card);
    }
    .radio-option input:checked + .radio-label {
      border-color: var(--color-primary); color: var(--color-primary); background: rgba(10, 22, 40, 0.05);
    }
    .radio-label i { font-size: 18px; color: var(--color-text-light); }
    .radio-option input:checked + .radio-label i { color: var(--color-primary); }

    @media (max-width: 600px) {
      .radio-group { flex-direction: column; gap: 10px; }
    }

    /* Success Popup Modal specific tweaks to match design system .modal-box */
    .modal-box {
      text-align: center;
    }
    .success-icon-container {
      width: 80px; height: 80px; background: #d4edda; border-radius: 50%;
      display: flex; justify-content: center; align-items: center; margin: 0 auto 20px;
      color: #28a745; font-size: 40px; animation: bounceIn 0.8s ease backwards; animation-delay: 0.2s;
    }
    .modal-box h3 { color: var(--color-text); font-size: 24px; margin-bottom: 10px; font-weight: 700; }
    .modal-box p { color: var(--color-text-light); font-size: 16px; margin-bottom: 25px; line-height: 1.5; }
    
    @keyframes bounceIn {
      0% { transform: scale(0); opacity: 0; }
      50% { transform: scale(1.2); opacity: 1; }
      100% { transform: scale(1); opacity: 1; }
    }
    
    /* Alerts */
    .message {
      padding: 15px; border-radius: var(--radius-md); margin-bottom: 20px; display: none; text-align: center; font-weight: 500;
    }
    .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; display: block; }
    .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; display: block; }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="site-nav">
    <div class="site-nav__inner">
      <a href="index.php" class="site-nav__brand">
        <img src="assets/images/ys_logo.png" alt="YS Study Focus" class="site-nav__logo">
        <span class="site-nav__brand-text">YS Study Focus</span>
      </a>
      <div class="site-nav__links">
        <a href="index.php" class="site-nav__link">Home</a>
        <a href="pages/general/about-us.html" class="site-nav__link">About</a>
        <a href="pages/general/university-requirements" class="site-nav__link">Requirements</a>
        <a href="#contact" class="site-nav__link">Contact</a>
      </div>
      <button class="site-nav__toggle" onclick="toggleMenu(event)"><i class="fas fa-bars"></i></button>
    </div>
    <div class="site-nav__mobile" id="navMenu">
      <a href="index.php" class="site-nav__mobile-link"><i class="fas fa-home"></i> Home</a>
      <a href="pages/general/about-us.html" class="site-nav__mobile-link"><i class="fas fa-info-circle"></i> About Us</a>
      <a href="pages/general/university-requirements" class="site-nav__mobile-link"><i class="fas fa-list-check"></i> Requirements</a>
      <a href="#contact" class="site-nav__mobile-link"><i class="fas fa-envelope"></i> Contact Us</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero" style="position: relative;">
    <div class="hero__bg" style="background-image: url('assets/images/yscover.png'); background-size: cover; background-position: center; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;"></div>
    <div class="hero__overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); z-index: -1;"></div>
    <div class="hero__content container" style="position: relative; z-index: 1; padding: 100px 20px;">
      <h3 style="color: var(--color-accent); margin-bottom: var(--spacing-sm); text-transform: uppercase; letter-spacing: 1px;">Student Gateway to Malaysia</h3>
      <h1 style="color: var(--color-surface); max-width: 800px; font-size: 2.5rem; margin-bottom: 0;">YS STUDY FOCUS, THE BEST AND THE MOST TRUSTED PLACE TO START YOUR PROCESSING FOR MALAYSIA</h1>
    </div>
  </section>

  <!-- Form Section -->
  <div class="section form-wrapper">
    <div class="container" style="max-width: 800px;">
      <div class="card" style="padding: var(--spacing-xl);">
        <div class="form-header">
          <h2>Book Your Free Consultation</h2>
          <p>Take the first step towards your dream education.</p>
        </div>

        <div id="form-message" class="message"></div>

        <form id="consultForm">
          
          <!-- PERSONAL DETAILS -->
          <h3 class="section-title"><i class="fas fa-user"></i> Personal Details</h3>
          
          <div class="form-group full-width">
            <label class="form-label" for="full_name">Full Name <span style="color: var(--color-text-light); font-weight: normal; font-size: 12px;">(As per Passport)</span></label>
            <input type="text" id="full_name" name="full_name" class="form-input" required placeholder="e.g. Nahid Jahan">
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="phone_number">Phone Number</label>
              <input type="tel" id="phone_number" name="phone_number" class="form-input" required placeholder="e.g. 017xxxxxxxx">
            </div>
            <div class="form-group">
              <label class="form-label" for="email">Email Address</label>
              <input type="email" id="email" name="email" class="form-input" required placeholder="student@example.com">
            </div>
          </div>

          <!-- ACADEMIC PROFILE -->
          <h3 class="section-title"><i class="fas fa-graduation-cap"></i> Academic Profile</h3>

          <div class="form-group full-width">
            <label class="form-label" for="highest_education">Education Qualification</label>
            <select id="highest_education" name="highest_education" class="form-select" required onchange="handleEducationChange()">
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
            <label class="form-label" for="other_education">Specify Other Qualification</label>
            <input type="text" id="other_education" name="other_education" class="form-input" placeholder="e.g. O Levels, A Levels">
          </div>

          <div class="form-grid">
            <div class="form-group" id="result-section" style="display:none;">
              <label class="form-label" id="result_label" for="score_main">Result/Score</label>
              <div class="score-container">
                <select id="score_main" class="form-select" onchange="handleScoreChange()"></select>
                <span>.</span>
                <select id="score_dec1" class="form-select"></select>
                <select id="score_dec2" class="form-select"></select>
              </div>
              <input type="hidden" id="result_score" name="result_score">
            </div>

            <div class="form-group">
              <label class="form-label" for="passing_year">Passing Year</label>
              <select id="passing_year" name="passing_year" class="form-select" required>
                <option value="" disabled selected>Select Year</option>
              </select>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="interested_to_study">Interested to Study</label>
              <input type="text" id="interested_to_study" name="interested_to_study" class="form-input" required placeholder="e.g. Computer Science, Business, Engineering...">
            </div>
            <div class="form-group">
              <label class="form-label" for="budget">Budget</label>
              <select id="budget" name="budget" class="form-select" required>
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
            <label class="form-label">Do you have a valid passport?</label>
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

          <button type="submit" class="btn btn--primary btn--lg btn--full" id="submitBtn" style="margin-top: var(--spacing-lg);">
            Submit Application <i class="fas fa-paper-plane"></i>
          </button>

        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
    <footer class="site-footer" id="contact">
    <div class="site-footer__main">
      <div class="site-footer__office">
        <img src="assets/images/ys_logo.png" alt="YS Study Focus" class="site-footer__logo" style="margin-bottom: 1rem;">
        <p>Your trusted gateway to studying in Malaysia. We provide expert guidance and direct university partnerships.</p>
      </div>
      
      <div class="site-footer__office">
        <h4>Quick Links</h4>
        <ul class="site-footer__list">
          <li><a href="index.html" class="site-footer__link">Home</a></li>
          <li><a href="pages/general/about-us.html" class="site-footer__link">About Us</a></li>
          <li><a href="pages/general/university-Partnerships.html" class="site-footer__link">Universities</a></li>
          <li><a href="pages/general/university-requirements.html" class="site-footer__link">Requirements</a></li>
        </ul>
      </div>

      <div class="site-footer__office">
        <h4>Resources</h4>
        <ul class="site-footer__list">
          <li><a href="pages/general/expert-agents.html" class="site-footer__link">Expert Agents</a></li>
          <li><a href="pages/general/student-success.html" class="site-footer__link">Success Stories</a></li>
          <li><a href="book-consult.php" class="site-footer__link">Book Consult</a></li>
          <li><a href="pages/general/developer-profile.html" class="site-footer__link">Developer Profile</a></li>
        </ul>
      </div>

      <div class="site-footer__office">
        <h4>Our Offices</h4>
        <div style="margin-bottom: 1rem;">
          <strong style="color: white; font-size: 0.9rem;">Bangladesh:</strong><br>
          <span style="font-size: 0.85rem;">80/A/1 Shahjalal Tower, Level-07,<br>CID office opposite, Malibagh Mor, Dhaka</span>
        </div>
        <div>
          <strong style="color: white; font-size: 0.9rem;">Malaysia:</strong><br>
          <span style="font-size: 0.85rem;">De Tropicana, 52, Jalan Kuchai Maju 13,<br>Kuchai Entrepreneurs Park, KL</span>
        </div>
      </div>
    </div>

    <div class="site-footer__social">
      <a href="https://web.facebook.com/people/YS-Study-Focus/61571694410231/?_rdc=1&_rdr" target="_blank" class="site-footer__social-link"><ion-icon name="logo-facebook"></ion-icon></a>
      <a href="https://wa.me/601139660706" target="_blank" class="site-footer__social-link"><ion-icon name="logo-whatsapp"></ion-icon></a>
      <a href="https://youtube.com/@sabrina367sabu?si=GRfgbwVUAxxH5Ke_" target="_blank" class="site-footer__social-link"><ion-icon name="logo-youtube"></ion-icon></a>
      <a href="https://www.tiktok.com/@sabu_555?_r=1&_t=ZS-92OvdlsEIcy" target="_blank" class="site-footer__social-link"><ion-icon name="logo-tiktok"></ion-icon></a>
    </div>

    <div class="site-footer__bottom">
      <p class="site-footer__credit">&copy; 2025/2026 YS Study Focus. Developed by <a href="pages/general/developer-profile.html">Bhuiyan Mohamed Nahid Jahan</a></p>
    </div>
  </footer>

  <!-- Success Modal Overlay -->
  <div class="modal-overlay" id="successModal">
    <div class="modal-box">
      <div class="success-icon-container">
        <i class="fas fa-check"></i>
      </div>
      <h3>Application Received!</h3>
      <p>Thank you for booking a consultation. Our team will review your details and contact you very soon to discuss your journey to Malaysia.</p>
      <button class="btn btn--primary" onclick="closeSuccessModal()">Close</button>
    </div>
  </div>

  <!-- Global Scripts -->
  <script src="assets/js/global.js"></script>
  
  <!-- Page Specific Scripts -->
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
  </script>
</body>
</html>


