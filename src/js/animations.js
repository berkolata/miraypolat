// Plugin'i kaydet
gsap.registerPlugin(ScrollTrigger);

let locoScroll = null;

// Saati güncelle
const updateClock = () => {
  const now = new Date();
  const seconds = now.getSeconds();
  const minutes = now.getMinutes();
  const hours = now.getHours() % 12;

  // Açıları hesapla
  const secondDegrees = (seconds / 60) * 360;
  const minuteDegrees = ((minutes + seconds / 60) / 60) * 360;
  const hourDegrees = ((hours + minutes / 60) / 12) * 360;

  // Akrep, yelkovan ve saniye kollarını döndür
  const hourHand = document.getElementById("hour-hand");
  const minuteHand = document.getElementById("minute-hand");
  const secondHand = document.getElementById("second-hand");

  if (hourHand && minuteHand && secondHand) {
    hourHand.style.transform = `translateX(-50%) rotate(${hourDegrees}deg)`;
    minuteHand.style.transform = `translateX(-50%) rotate(${minuteDegrees}deg)`;
    secondHand.style.transform = `translateX(-50%) rotate(${secondDegrees}deg)`;
  }
};

// Sayfa yüklendiğinde
window.addEventListener("DOMContentLoaded", () => {
  updateClock();
  window.clockInterval = setInterval(updateClock, 1000);
  document.body.style.overflow = "hidden";
  loadingAnimation();
  document
    .getElementById("start-button")
    .addEventListener("click", completeLoading);
});

// Loading animasyonu
const loadingAnimation = () => {
  const tl = gsap.timeline();

  // Başlangıç durumları
  gsap.set("#start-button", {
    opacity: 0,
    scale: 0,
  });
  gsap.set("#main-content", { opacity: 0 });

  // Renkli daire animasyonu
  tl.to("#progress-circle", {
    duration: 0.8,
    stroke: "#F0E2D5",
    strokeDashoffset: 330,
    ease: "power1.inOut",
  })
    .to("#progress-circle", {
      duration: 0.8,
      stroke: "#B36F2E",
      strokeDashoffset: 220,
      ease: "power1.inOut",
    })
    .to("#progress-circle", {
      duration: 0.8,
      stroke: "#235486",
      strokeDashoffset: 0,
      ease: "power1.inOut",
    })
    .to("#clock", {
      scale: 0.5,
      opacity: 0,
      duration: 0.5,
      ease: "power2.in",
    })
    .to(
      "svg.animate-spin-slow",
      {
        opacity: 0,
        scale: 0.5,
        duration: 0.5,
        ease: "power2.in",
      },
      "-=0.5"
    )
    .to("#start-button", {
      opacity: 1,
      scale: 1,
      duration: 0.5,
      ease: "back.out(1.7)",
    })
    .to("#button-text, #button-icon", {
      opacity: 1,
      duration: 0.3,
      stagger: 0.1,
    });

  return tl;
};

// Loading tamamlandığında
const completeLoading = () => {
  // İlk section'ı başlangıçta gizle
  gsap.set(".mast", { opacity: 0 });
  gsap.set(".mast__bg", { 
    scale: 1.1,
    transformOrigin: "center center" 
  });
  gsap.set(".mast__header", { y: 50, opacity: 0 });
  gsap.set(".sep", { scaleX: 0 });
  gsap.set("#mast-button", { opacity: 0, y: 20 });
  gsap.set("header", { opacity: 0, y: 0 });

  // spanize fonksiyonu
  const spanize = () => {
    document.querySelectorAll('.js-spanize').forEach(element => {
      const text = element.textContent;
      const spanized = text.trim().split('').map(char => 
        `<span style="opacity: 0; transform: translateY(50px)">${char}</span>`
      ).join('');
      element.innerHTML = spanized;
    });
  };

  // Spanize'ı çağır
  //spanize();

  // Sayfayı en üste scroll et ve scroll'u engelle
  window.scrollTo(0, 0);
  document.documentElement.style.overflow = 'hidden';

  // Ana timeline
  const mainTl = gsap.timeline();

  mainTl
    .to("#loading-screen", {
      opacity: 0,
      duration: 1,
      onComplete: () => {
        document.getElementById("loading-screen").style.display = "none";
        window.scrollTo(0, 0);
      }
    })
    .to("#main-content", {
      opacity: 1,
      duration: 0.5
    }, "-=0.5")
    .to(".mast", {
      opacity: 1,
      duration: 1
    }, "-=0.5")
    .to(".mast__header", {
      y: 0,
      opacity: 1,
      duration: 0.8,
      ease: "power2.out"
    }, "-=1.2")
    .to(".js-spanize span", {
      y: 0,
      opacity: 1,
      duration: 0.6,
      stagger: 0.03,
      ease: "power2.out"
    }, "-=0.8")
    .to(".sep", {
      scaleX: 1,
      duration: 0.8,
      ease: "power2.out"
    }, "-=0.5")
    .to("#mast-button", {
      opacity: 1,
      y: 0,
      duration: 0.8,
      ease: "power2.out"
    })
    .to("header", {
      opacity: 1,
      y: 0,
      duration: 1,
      ease: "back.inOut",
      onStart: () => {
        document.querySelector('header').classList.remove('initially-hidden');
      },
      onComplete: () => {
        // Tüm animasyonlar tamamlandığında scroll'u aktif et
        document.documentElement.style.overflow = 'visible';
        document.body.style.overflow = 'visible';

        // Scroll animasyonlarını başlat
        initScrollAnimations();
      }
    });
};

// Scroll animasyonlarını kur
const initScrollAnimations = () => {
  gsap.registerPlugin(ScrollTrigger);

  // Container fade-in animasyonu
  gsap.set('.container', { opacity: 0 });
  gsap.to('.container', {
    opacity: 1,
    duration: 0.16,
    scrollTrigger: {
      trigger: '.container',
      start: 'top 80%',
    }
  });

  // Fade elementleri için animasyon
  gsap.utils.toArray('.fade').forEach(fade => {
    gsap.set(fade, { 
      opacity: 0,
      y: 0
    });

    gsap.to(fade, {
      opacity: 1,
      y: 0,
      duration: 0.16,
      scrollTrigger: {
        trigger: fade,
        start: 'top 80%',
        toggleActions: 'play none none reverse'
      }
    });
  });

  // Flow elementleri için animasyon
  gsap.utils.toArray('.flow').forEach(flow => {
    const flowContent = flow.querySelector('.flowcontent');
    const caption = flow.querySelector('.caption');
    const image = flow.querySelector('.imageholder') || flow.querySelector('video');

    gsap.set(flow, { opacity: 0 });

    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: flow,
        start: 'top top',
        end: '+=600',
        scrub: true,
        pin: true,
        toggleActions: 'play none none reverse'
      }
    });

    tl.to(flow, {
      opacity: 1,
      duration: 0.3
    })
    .from(image, {
      scale: 1.1,
      duration: 1
    }, 0)
    .from(caption, {
      y: 50,
      opacity: 0,
      duration: 0.5
    }, 0.2);
  });

  initProgressBar();
};

// Progress bar kontrolü için yeni fonksiyon
const initProgressBar = () => {
  const sections = [
    document.getElementById('birinci-adim'),
    document.getElementById('ikinci-adim'),
    document.getElementById('ucuncu-adim'),
    document.getElementById('dorduncu-adim')
  ];
  
  const progressBars = [
    document.getElementById('progress-1'),
    document.getElementById('progress-2'),
    document.getElementById('progress-3'),
    document.getElementById('progress-4')
  ];
  
  const progressContainer = document.getElementById('progress-container');
  let lastScrollY = window.scrollY;
  
  // Progress container'ı başlangıçta gizle
  progressContainer.style.transform = 'translateY(100%)';
  
  // Progress'i güncelle
  const updateProgress = () => {
    const windowHeight = window.innerHeight;
    const scrollPosition = window.scrollY;
    const firstSectionTop = sections[0].offsetTop - windowHeight * 0.5;
    const lastSection = sections[3];
    const lastSectionBottom = lastSection.offsetTop + lastSection.offsetHeight;

    // İlk section'a gelmeden veya son section'ı geçince progress bar'ı gizle
    if (scrollPosition < firstSectionTop || scrollPosition > lastSectionBottom - windowHeight * 0.5) {
      progressContainer.style.transform = 'translateY(100%)';
      return;
    }

    // Progress bar'ı göster
    progressContainer.style.transform = 'translateY(0)';

    sections.forEach((section, index) => {
      if (!section) return;
      
      const rect = section.getBoundingClientRect();
      const sectionTop = rect.top + scrollPosition;
      const nextSection = sections[index + 1];
      
      // Mevcut section için progress hesapla
      if (scrollPosition >= sectionTop - windowHeight * 0.5) {
        // Önceki barları doldur
        for (let i = 0; i < index; i++) {
          progressBars[i].style.width = '100%';
        }
        
        // Eğer bir sonraki section varsa, onun pozisyonuna göre progress hesapla
        if (nextSection) {
          const nextSectionTop = nextSection.offsetTop;
          const progressEnd = nextSectionTop - windowHeight * 0.5; // Bir sonraki section'ın ortası
          const progressStart = sectionTop - windowHeight * 0.5;
          const progress = Math.min(
            100,
            Math.max(
              0,
              ((scrollPosition - progressStart) / (progressEnd - progressStart)) * 100
            )
          );
          progressBars[index].style.width = `${progress}%`;
        } else {
          // Son section için progress
          const progressEnd = lastSectionBottom - windowHeight * 0.5;
          const progressStart = sectionTop - windowHeight * 0.5;
          const progress = Math.min(
            100,
            Math.max(
              0,
              ((scrollPosition - progressStart) / (progressEnd - progressStart)) * 100
            )
          );
          progressBars[index].style.width = `${progress}%`;
        }
      } else {
        progressBars[index].style.width = '0%';
      }
    });
  };

  // Scroll event listener'ları
  let ticking = false;
  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        updateProgress();
        ticking = false;
      });
      ticking = true;
    }
  });

  // İlk yüklemede progress'i güncelle
  updateProgress();

  // Progress bar'a tıklama işlevselliği ekle
  progressBars.forEach((bar, index) => {
    bar.parentElement.parentElement.addEventListener('click', () => {
      const targetSection = sections[index];
      if (targetSection) {
        targetSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
};

// Resize olayını dinle
window.addEventListener("resize", () => {
  if (locoScroll) {
    setTimeout(() => {
      ScrollTrigger.refresh();
      locoScroll.update();
    }, 100);
  }
});

// CSS animasyonları için style ekle
const style = document.createElement("style");
style.textContent = `
  html.has-scroll-smooth {
    overflow: hidden;
  }

  html.has-scroll-dragging {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }

  .has-scroll-smooth body {
    overflow: hidden;
  }

  .has-scroll-smooth [data-scroll-container] {
    min-height: 100vh;
  }
`;
document.head.appendChild(style);

// Header ve hidden element scroll efekti
let lastScrollTop = 0;
const header = document.querySelector('header');
const hiddenElement = document.querySelector('.hidden-on-scroll');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    
    // Header için scroll kontrolü
    if (currentScroll > lastScrollTop) {
        // Aşağı scroll
        header.style.transform = 'translateY(-100%)';
        if (hiddenElement) {
            hiddenElement.style.transform = 'translateY(-100%)';
            hiddenElement.style.visibility = 'hidden'; // Ekran yüksekliğini etkilememesi için
            hiddenElement.style.height = '0px';
        }
    } else {
        // Yukarı scroll
        header.style.transform = 'translateY(0)';
        
        // Hidden element sadece sayfa en üstteyse görünür olsun
        if (hiddenElement) {
            if (currentScroll <= 0) {
                hiddenElement.style.transform = 'translateY(0)';
                hiddenElement.style.visibility = 'visible';
                hiddenElement.style.height = 'auto';
            } else {
                hiddenElement.style.transform = 'translateY(-100%)';
                hiddenElement.style.visibility = 'hidden';
            }
        }
    }
    
    // Header'ın arkaplan efekti
    if (currentScroll > 0) {
        header.classList.add('header-scroll');
    } else {
        header.classList.remove('header-scroll');
    }
    
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
});

// Mobil menü kontrolü
const mobileMenuButton = document.getElementById('mobile-menu-button');
const closeMobileMenuButton = document.getElementById('close-mobile-menu');
const mobileMenu = document.getElementById('mobile-menu');
const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

const openMobileMenu = () => {
    mobileMenu.classList.add('mobile-menu-open');
    mobileMenuOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
};

const closeMobileMenu = () => {
    mobileMenu.classList.remove('mobile-menu-open');
    mobileMenuOverlay.classList.remove('active');
    document.body.style.overflow = '';
};

mobileMenuButton.addEventListener('click', openMobileMenu);
closeMobileMenuButton.addEventListener('click', closeMobileMenu);
mobileMenuOverlay.addEventListener('click', closeMobileMenu);

// Mobil menüde dropdown'ları otomatik kapat
document.querySelectorAll('.mobile-nav-link').forEach(link => {
    link.addEventListener('click', () => {
        const nextElement = link.nextElementSibling;
        if (!nextElement?.classList.contains('mobile-dropdown-content')) {
            mobileMenu.classList.remove('mobile-menu-open');
            document.body.style.overflow = '';
        }
    });
});

// ScrollMagic controller
const controller = new ScrollMagic.Controller();

// Sayfa yüklendiğinde animasyonları başlat
document.addEventListener('DOMContentLoaded', () => {
    initFlowAnimations();
    initFadeAnimations();
});

// Flow animasyonları
const initFlowAnimations = () => {
    document.querySelectorAll('.flow').forEach(flow => {
        flow.classList.add('out');
        
        new ScrollMagic.Scene({
            triggerElement: flow,
            triggerHook: 0,
            duration: 600
        })
        .on("enter", (ev) => {
            ev.target.triggerElement().classList.remove('out');
        })
        .on("leave", (ev) => {
            ev.target.triggerElement().classList.add('out');
        })
        .addTo(controller);
    });
};

// Fade animasyonları 
const initFadeAnimations = () => {
    document.querySelectorAll('.fade').forEach(fade => {
        fade.classList.add('out');
        
        new ScrollMagic.Scene({
            triggerElement: fade,
            triggerHook: 0.65
        })
        .on("enter", (ev) => {
            ev.target.triggerElement().classList.remove('out');
        })
        .on("leave", (ev) => {
            ev.target.triggerElement().classList.add('out');
        })
        .addTo(controller);
    });
};

// Container sınıfı için jQuery kodunu vanilla JS'e çevirelim
document.querySelectorAll('.container').forEach(container => {
    container.classList.remove('out');
});

// Kitap Animasyonları
const initBookAnimations = () => {
  const books = document.querySelectorAll('.book');
  const bookBacks = document.querySelectorAll('.book-back');

  books.forEach(book => {
      // Mouse Enter animasyonu
      book.addEventListener('mouseenter', () => {
          const bookWrap = book.closest('.book-wrap');
          gsap.to(bookWrap, {
              rotationY: 20,
              duration: 0.6,
              ease: "power2.out"
          });
      });

      // Click animasyonu
      book.addEventListener('click', () => {
          const bookWrap = book.closest('.book-wrap');
          gsap.to(bookWrap, {
              rotationY: 180,
              duration: 0.8,
              ease: "power2.inOut"
          });
      });
  });

  // Kitap arkası click animasyonu
  bookBacks.forEach(bookBack => {
      bookBack.addEventListener('click', () => {
          const bookWrap = bookBack.closest('.book-wrap');
          gsap.to(bookWrap, {
              rotationY: 0,
              duration: 0.8,
              ease: "power2.inOut"
          });
      });
  });
};

// Sayfanın yüklenmesi tamamlandığında kitap animasyonlarını başlat
window.addEventListener('load', () => {
    initBookAnimations();
});

