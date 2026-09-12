  let ALL_PROGRAMS = []; 
  let currentMatches = []; 
  let visibleCount = 0;    
  const BATCH_SIZE = 10;   

  const uniLinks = {
    "City University Malaysia": "city-university",
    "Infrastructure University Kuala Lumpur (IUKL)": "iukl-fees",
    "BAC EDUCATION GROUP": "bac-university",
    "ALFA UNIVERSITY MALAYSIA": "alfa-university",
    "INTI International University": "inti-university",
    "UNIVERSITY OF CYBERJAYA": "university-of-cyberjaya",
    "University College MAIWP International (UCMI)": "ucmi-university",
    "CYBERNETICS MALAYSIA (KTAC)": "cybernetics-university",
    "GENOVASI UNIVERSITY MALAYSIA": "genovasi-university",
    "SEGI UNIVERSITY MALAYSIA": "segi-university",
    "Taylor's University": "taylors-university",
    "Binary University": "binary-university",
    "King's University": "kings-university",
    "Universiti Tun Abdul Razak (UNIRAZAK)": "unirazak-university", 
    "Binary University of Management & Entrepreneurship": "binary-university", 
    "Kings University College": "kings-university",
    "VISION University College": "vision-fees",
    "Lincoln University College": "lincoln-fees",
    "Universiti Kuala Lumpur (UniKL)": "unikl-fees",
    "Asia Pacific University (APU)": "apu-fees",
    "MANTISSA COLLEGE MALAYSIA": "mantissa-college",
    "ASIA e UNIVERSITY": "asia-e-university"
  };

  async function fetchAllPrograms() {
    try {
      const response = await fetch('universities.json');
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      ALL_PROGRAMS = await response.json();
      console.log("Programs data loaded successfully.");
    } catch (error) {
      console.error("Could not fetch programs data:", error);
      document.getElementById('searchResults').innerHTML = '<p style="color: red;">Error loading university data.</p>';
    }
  }

  let baseSearchResults = []; 

  function searchUniversities() {
    const input = document.getElementById("subjectInput").value.toLowerCase().trim();
    const resultBox = document.getElementById("searchResults");
    const filterBar = document.getElementById("filterBar");
    
    resultBox.innerHTML = "";
    baseSearchResults = []; 
    currentMatches = [];
    visibleCount = 0;

    if(filterBar) filterBar.style.display = 'none';

    if (input.length < 2) return;

    const searchTerms = input.split(' ').filter(term => term.length > 0);
    
    ALL_PROGRAMS.forEach(uni => {
      const universityName = uni.name;
      const universityLink = uniLinks[universityName] || "#"; 
      
      uni.programs.forEach(program => {
        const programNameLower = program.name.toLowerCase();
        const allTermsMatch = searchTerms.every(term => programNameLower.includes(term));
        
        if (allTermsMatch) {
          baseSearchResults.push({
            uni: universityName,
            campus: program.campus,
            program: program.name,
            link: universityLink
          });
        }
      });
    });

    if (baseSearchResults.length >= 10) {
      if(filterBar) {
        filterBar.style.display = 'flex'; 
        const btns = document.querySelectorAll('.filter-pill');
        btns.forEach(btn => btn.classList.remove('active'));
        if(btns[0]) btns[0].classList.add('active');
      }
    }

    currentMatches = [...baseSearchResults];

    if (currentMatches.length > 0) {
      renderNextBatch();
    } else {
      resultBox.innerHTML = "<div style='padding:20px; color:#666; text-align: center;'>No results found.</div>";
    }
  }

  function filterResults(category, btnElement) {
    document.querySelectorAll('.filter-pill').forEach(btn => btn.classList.remove('active'));
    btnElement.classList.add('active');

    const resultBox = document.getElementById("searchResults");
    resultBox.innerHTML = "";
    visibleCount = 0;
    
    if (category === 'All') {
      currentMatches = [...baseSearchResults];
    } else {
      const searchKey = category === 'Masters' ? 'Master' : category;
      currentMatches = baseSearchResults.filter(item => 
        item.program.toLowerCase().includes(searchKey.toLowerCase())
      );
    }

    if (currentMatches.length > 0) {
      renderNextBatch();
    } else {
      resultBox.innerHTML = "<div style='padding:20px; color:#666; text-align: center;'>No " + category + " programmes found for this search.</div>";
    }
  }

  function renderNextBatch() {
    const resultBox = document.getElementById("searchResults");
    
    const existingBtn = document.getElementById("seeMoreBtnContainer");
    if (existingBtn) existingBtn.remove();

    const nextBatch = currentMatches.slice(visibleCount, visibleCount + BATCH_SIZE);

    nextBatch.forEach(match => {
      const campusDisplay = match.campus ? ` Ã¢â‚¬â€ ${match.campus}` : '';
      const encodedProgram = encodeURIComponent(match.program);
      const isBachelor = match.program.toLowerCase().includes('bachelor');
      
      const bgColor = isBachelor ? '#FFD700' : 'var(--color-primary)';
      const textColor = isBachelor ? 'var(--color-primary)' : '#fff';

      resultBox.innerHTML += `
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 1.5rem;">
          <div style="flex: 1; padding-right: 1rem;">
             <h4 style="color: var(--color-primary); margin-bottom: 0.5rem; font-size: 1.1rem;">${match.program}</h4>
             <p style="color: #666; font-size: 0.9rem;">${match.uni}${campusDisplay}</p>
          </div>
          <button onclick="window.location.href='${match.link}?q=${encodedProgram}';" class="btn" style="background-color: ${bgColor}; color: ${textColor}; white-space: nowrap;">
            View Fees
          </button>
        </div>
      `;
    });

    visibleCount += nextBatch.length;

    if (visibleCount < currentMatches.length) {
      const remaining = currentMatches.length - visibleCount;
      resultBox.innerHTML += `
        <div id="seeMoreBtnContainer" style="text-align: center; margin-top: 1.5rem;">
          <button onclick="renderNextBatch()" class="btn btn--primary">
            See More Universities (${remaining} more)
          </button>
        </div>
      `;
    }
  }

  document.addEventListener('DOMContentLoaded', fetchAllPrograms);

  function updateStudentCount() {
    const startDate = new Date(2025, 11, 21); 
    const today = new Date();
    const diffInMs = today - startDate;
    const weeksPassed = Math.floor(diffInMs / (1000 * 60 * 60 * 24 * 7));
    const currentCount = 126 + (weeksPassed > 0 ? weeksPassed : 0);
    
    const countElement = document.getElementById("dynamicStudentCount");
    if (countElement) {
      countElement.innerText = currentCount + "+";
    }
  }

  document.addEventListener('DOMContentLoaded', updateStudentCount);

  function scrollToSearch() {
    const searchSection = document.querySelector(".search-section");
    if (searchSection) {
      searchSection.scrollIntoView({ behavior: 'smooth' });
    }
  }

  function initTestimonialSlider() {
    const slider = document.getElementById('testimonialSlider');
    if (!slider) return;

    if (!slider.getAttribute('data-cloned')) {
      slider.innerHTML += slider.innerHTML;
      slider.setAttribute('data-cloned', 'true');
    }

    const RESUME_DELAY = 10000; 
    const SCROLL_SPEED = 1;     
    const REFRESH_RATE = 20;    

    let autoScrollInterval;
    let resumeTimeout;
    let isAutoScrolling = true;

    let isDown = false;
    let startX;
    let scrollLeft;

    function startLoop() {
      clearInterval(autoScrollInterval); 
      autoScrollInterval = setInterval(() => {
        if (slider.scrollLeft >= slider.scrollWidth / 2) {
          slider.scrollLeft = 0;
        }

        if (isAutoScrolling) {
          slider.scrollLeft += SCROLL_SPEED;
        }
      }, REFRESH_RATE);
    }

    function triggerPause() {
      isAutoScrolling = false; 
      clearTimeout(resumeTimeout); 
      resumeTimeout = setTimeout(() => {
        isAutoScrolling = true; 
      }, RESUME_DELAY);
    }

    slider.addEventListener('mousedown', (e) => {
      triggerPause();
      isDown = true;
      slider.style.cursor = 'grabbing';
      startX = e.pageX - slider.offsetLeft;
      scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener('mouseleave', () => {
      isDown = false;
      slider.style.cursor = 'default';
    });

    slider.addEventListener('mouseup', () => {
      isDown = false;
      slider.style.cursor = 'default';
    });

    slider.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      triggerPause();
      e.preventDefault();
      const x = e.pageX - slider.offsetLeft;
      const walk = (x - startX) * 2;
      slider.scrollLeft = scrollLeft - walk;
    });

    slider.addEventListener('touchstart', triggerPause, { passive: true });
    slider.addEventListener('touchmove', triggerPause, { passive: true });
    slider.addEventListener('wheel', triggerPause, { passive: true });

    startLoop();
  }

  document.addEventListener('DOMContentLoaded', initTestimonialSlider);

  function revealUniversities() {
    const hiddenCards = document.querySelectorAll('.hidden-card');
    const btn = document.getElementById('seeMoreBtn');
    
    hiddenCards.forEach(card => {
      card.classList.remove('hidden-card');
    });

    if (btn) btn.style.display = 'none';
  }

  function toggleMenu(event) {
    if (event) event.stopPropagation();

    const menu = document.getElementById("navMenu");
    const menuBtn = document.querySelector(".site-nav__toggle");
    
    menu.classList.toggle("active");
    
    if (menu.classList.contains("active")) {
      menuBtn.innerHTML = '<i class="fas fa-times"></i>'; 
    } else {
      menuBtn.innerHTML = '<i class="fas fa-bars"></i>'; 
    }
  }

  document.addEventListener('click', function(event) {
    const menu = document.getElementById("navMenu");
    const menuBtn = document.querySelector(".site-nav__toggle");
    
    if (menu && menu.classList.contains("active")) {
      if (!menu.contains(event.target)) {
        menu.classList.remove("active");
        if (menuBtn) menuBtn.innerHTML = '<i class="fas fa-bars"></i>'; 
      }
    }
  });

  document.querySelectorAll('.site-nav__mobile-link').forEach(link => {
    link.addEventListener('click', () => {
       const menu = document.getElementById("navMenu");
       const menuBtn = document.querySelector(".site-nav__toggle");
       if (menu) menu.classList.remove("active");
       if (menuBtn) menuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
    const pausedLinks = document.querySelectorAll('.paused-recruitment');
    const modalOverlay = document.getElementById('recruitmentOverlay');
    const proceedBtn = document.getElementById('proceedToUniBtn');
    let pendingUrl = '';

    pausedLinks.forEach(link => {
      link.addEventListener('click', (event) => {
        event.preventDefault(); 
        event.stopPropagation(); 
        
        pendingUrl = link.getAttribute('href');
        if (modalOverlay) modalOverlay.classList.add('active');
      });
    });

    if (proceedBtn) {
      proceedBtn.addEventListener('click', () => {
        if (pendingUrl) {
          window.location.href = pendingUrl;
        }
      });
    }
  });

  function closeRecruitmentModal() {
    const modalOverlay = document.getElementById('recruitmentOverlay');
    if (modalOverlay) modalOverlay.classList.remove('active');
  }
  function initTestimonialSlider() {
    const slider = document.getElementById('testimonialSlider');
    if (!slider) return;

    if (!slider.getAttribute('data-cloned')) {
      slider.innerHTML += slider.innerHTML;
      slider.setAttribute('data-cloned', 'true');
    }

    const RESUME_DELAY = 10000;
    const SCROLL_SPEED = 1;
    const REFRESH_RATE = 20;

    let autoScrollInterval;
    let resumeTimeout;
    let isAutoScrolling = true;

    let isDown = false;
    let startX;
    let scrollLeft;

    function startLoop() {
      clearInterval(autoScrollInterval);
      autoScrollInterval = setInterval(() => {
        if (slider.scrollLeft >= slider.scrollWidth / 2) {
          slider.scrollLeft = 0;
        }
        if (isAutoScrolling) {
          slider.scrollLeft += SCROLL_SPEED;
        }
      }, REFRESH_RATE);
    }

    function triggerPause() {
      isAutoScrolling = false;
      clearTimeout(resumeTimeout);
      resumeTimeout = setTimeout(() => {
        isAutoScrolling = true;
      }, RESUME_DELAY);
    }

    slider.addEventListener('mousedown', (e) => {
      triggerPause();
      isDown = true;
      slider.style.cursor = 'grabbing';
      startX = e.pageX - slider.offsetLeft;
      scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener('mouseleave', () => {
      isDown = false;
      slider.style.cursor = 'default';
    });

    slider.addEventListener('mouseup', () => {
      isDown = false;
      slider.style.cursor = 'default';
    });

    slider.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      triggerPause();
      e.preventDefault();
      const x = e.pageX - slider.offsetLeft;
      const walk = (x - startX) * 2;
      slider.scrollLeft = scrollLeft - walk;
    });

    slider.addEventListener('touchstart', triggerPause, { passive: true });
    slider.addEventListener('touchmove', triggerPause, { passive: true });
    slider.addEventListener('wheel', triggerPause, { passive: true });

    startLoop();
  }
  document.addEventListener('DOMContentLoaded', initTestimonialSlider);
</script>
