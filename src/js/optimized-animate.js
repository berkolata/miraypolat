// GSAP ve ScrollTrigger'ı kaydet
import gsap from "gsap"
import ScrollTrigger from "gsap/ScrollTrigger"
import ScrollMagic from "scrollmagic"

gsap.registerPlugin(ScrollTrigger)

// DOM elementleri için önbellek
const DOM = {
  // Saat elementleri
  hourHand: null,
  minuteHand: null,
  secondHand: null,

  // Header ve menü elementleri
  header: null,
  hiddenElement: null,
  mobileMenuButton: null,
  closeMobileMenuButton: null,
  mobileMenu: null,
  mobileMenuOverlay: null,

  // Progress bar elementleri
  progressBars: [],
  sections: [],
  progressContainer: null,

  // Diğer elementler
  startButton: null,
  loadingScreen: null,
  mainContent: null,
}

// Performans için değişkenleri önbelleğe al
let lastScrollTop = 0
let ticking = false
let lastContent
let controller

// DOM elementlerini bir kez seç ve önbelleğe al
const cacheDOMElements = () => {
  // Saat elementleri
  DOM.hourHand = document.getElementById("hour-hand")
  DOM.minuteHand = document.getElementById("minute-hand")
  DOM.secondHand = document.getElementById("second-hand")

  // Header ve menü elementleri
  DOM.header = document.querySelector("header")
  DOM.hiddenElement = document.querySelector(".hidden-on-scroll")
  DOM.mobileMenuButton = document.getElementById("mobile-menu-button")
  DOM.closeMobileMenuButton = document.getElementById("close-mobile-menu")
  DOM.mobileMenu = document.getElementById("mobile-menu")
  DOM.mobileMenuOverlay = document.getElementById("mobile-menu-overlay")

  // Progress bar elementleri
  DOM.progressBars = [
    document.getElementById("progress-1"),
    document.getElementById("progress-2"),
    document.getElementById("progress-3"),
    document.getElementById("progress-4"),
  ]

  DOM.sections = [
    document.getElementById("birinci-adim"),
    document.getElementById("ikinci-adim"),
    document.getElementById("ucuncu-adim"),
    document.getElementById("dorduncu-adim"),
  ]

  DOM.progressContainer = document.getElementById("progress-container")

  // Diğer elementler
  DOM.startButton = document.getElementById("start-button")
  DOM.loadingScreen = document.getElementById("loading-screen")
  DOM.mainContent = document.getElementById("main-content")
}

// Saati güncelle - optimize edilmiş versiyon
const updateClock = () => {
  if (!DOM.hourHand || !DOM.minuteHand || !DOM.secondHand) return

  const now = new Date()
  const seconds = now.getSeconds()
  const minutes = now.getMinutes()
  const hours = now.getHours() % 12

  // Açıları hesapla
  const secondDegrees = (seconds / 60) * 360
  const minuteDegrees = ((minutes + seconds / 60) / 60) * 360
  const hourDegrees = ((hours + minutes / 60) / 12) * 360

  // transform değerlerini önbelleğe al
  const hourTransform = `translateX(-50%) rotate(${hourDegrees}deg)`
  const minuteTransform = `translateX(-50%) rotate(${minuteDegrees}deg)`
  const secondTransform = `translateX(-50%) rotate(${secondDegrees}deg)`

  // Tek bir requestAnimationFrame içinde tüm güncellemeleri yap
  requestAnimationFrame(() => {
    DOM.hourHand.style.transform = hourTransform
    DOM.minuteHand.style.transform = minuteTransform
    DOM.secondHand.style.transform = secondTransform
  })

  // Bir sonraki saniye başında güncelle
  const msToNextSecond = 1000 - now.getMilliseconds()
  setTimeout(updateClock, msToNextSecond)
}

// Loading animasyonu - optimize edilmiş
const loadingAnimation = () => {
  const tl = gsap.timeline({
    defaults: {
      ease: "power1.inOut",
      overwrite: true,
    },
  })

  // Başlangıç durumları - tek bir set ile
  gsap.set("#start-button", {
    opacity: 0,
    scale: 0,
  })
  gsap.set("#main-content", { opacity: 0 })

  // Renkli daire animasyonu
  tl.to("#progress-circle", {
    duration: 0.8,
    stroke: "#F0E2D5",
    strokeDashoffset: 330,
  })
    .to("#progress-circle", {
      duration: 0.8,
      stroke: "#B36F2E",
      strokeDashoffset: 220,
    })
    .to("#progress-circle", {
      duration: 0.8,
      stroke: "#235486",
      strokeDashoffset: 0,
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
      "-=0.5",
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
    })

  return tl
}

// Kullanıcı tıklaması için flag ekleyelim
let userClicked = false

// Spanize fonksiyonu - optimize edilmiş
const spanize = () => {
  const elements = document.querySelectorAll(".js-spanize")

  elements.forEach((element) => {
    const text = element.textContent.trim()
    let html = ""

    for (let i = 0; i < text.length; i++) {
      html += `<span style="opacity: 0; transform: translateY(50px)">${text[i]}</span>`
    }

    element.innerHTML = html
  })

  // Tüm sayfaya tıklama dinleyicisi ekle
  document.addEventListener(
    "click",
    () => {
      userClicked = true
    },
    { once: true },
  )
}

// Loading tamamlandığında - optimize edilmiş
const completeLoading = () => {
  if (!DOM.loadingScreen || !DOM.mainContent) return

  // İlk section'ı başlangıçta gizle - tek bir set ile
  gsap.set(".mast", { opacity: 0 })
  gsap.set(".mast__bg", {
    scale: 1.1,
    transformOrigin: "center center",
  })
  gsap.set(".mast__header", { y: 50, opacity: 0 })
  gsap.set(".sep", { scaleX: 0 })
  gsap.set("#mast-button", { opacity: 0, y: 20 })
  gsap.set("header", { opacity: 0, y: -20 })

  // Spanize'ı çağır
  spanize()

  // Sayfayı en üste scroll et ve scroll'u engelle
  window.scrollTo(0, 0)
  document.documentElement.style.overflow = "hidden"

  // Ana timeline - optimize edilmiş
  const mainTl = gsap.timeline({
    defaults: {
      ease: "power2.out",
      overwrite: true,
    },
    onComplete: () => {
      // Tüm animasyonlar tamamlandığında scroll'u aktif et
      document.documentElement.style.overflow = "visible"
      document.body.style.overflow = "visible"
      // Scroll animasyonlarını başlat
      initScrollAnimations()
    },
  })

  mainTl
    .to(DOM.loadingScreen, {
      opacity: 0,
      duration: 1,
      onComplete: () => {
        DOM.loadingScreen.style.display = "none"
        window.scrollTo(0, 0)
      },
    })
    .to(
      DOM.mainContent,
      {
        opacity: 1,
        duration: 0.5,
      },
      "-=0.5",
    )
    .to(
      ".mast",
      {
        opacity: 1,
        duration: 0.5,
      },
      "-=0.5",
    )
    .to(
      ".mast__header",
      {
        y: 0,
        opacity: 1,
        duration: 1,
      },
      "-=0.2",
    )
    .to(
      ".js-spanize span",
      {
        y: 0,
        opacity: 1,
        duration: userClicked ? 0.2 : 0.8, // Kullanıcı tıklamışsa daha hızlı
        stagger: userClicked ? 0.005 : 0.03, // Kullanıcı tıklamışsa daha hızlı stagger
        onStart: () => {
          // Animasyon başladığında da tıklama dinleyelim
          if (!userClicked) {
            document.addEventListener(
              "click",
              () => {
                // Animasyonu hızlandır
                gsap.to(".js-spanize span", {
                  y: 0,
                  opacity: 1,
                  duration: 0.2,
                  stagger: 0.005,
                  overwrite: true,
                })
                userClicked = true
              },
              { once: true },
            )
          }
        },
      },
      "-=0.8",
    )
    .to(
      ".sep",
      {
        scaleX: 1,
        duration: 0.8,
      },
      "-=0.5",
    )
    .to("#mast-button", {
      opacity: 1,
      y: 0,
      duration: 0.8,
    })
    .to("header", {
      opacity: 1,
      y: 0,
      duration: 0.8,
      onStart: () => {
        if (DOM.header) DOM.header.classList.remove("initially-hidden")
      },
    })
}

// Scroll animasyonlarını kur - optimize edilmiş
const initScrollAnimations = () => {
  // Önce mevcut ScrollTrigger'ları temizle
  ScrollTrigger.getAll().forEach((trigger) => trigger.kill())

  // Container fade-in animasyonu
  const containers = document.querySelectorAll(".container")
  if (containers.length) {
    gsap.set(containers, { opacity: 0 })

    containers.forEach((container) => {
      gsap.to(container, {
        opacity: 1,
        duration: 0.16,
        scrollTrigger: {
          trigger: container,
          start: "top 80%",
          markers: false,
          toggleActions: "play none none reverse",
        },
      })
    })
  }

  // Fade elementleri için animasyon - batch işlemi
  const fadeElements = document.querySelectorAll(".fade")
  if (fadeElements.length) {
    gsap.set(fadeElements, {
      opacity: 0,
      y: 20,
    })

    fadeElements.forEach((fade) => {
      gsap.to(fade, {
        opacity: 1,
        y: 0,
        duration: 0.16,
        scrollTrigger: {
          trigger: fade,
          start: "top 80%",
          markers: false,
          toggleActions: "play none none reverse",
        },
      })
    })
  }

  // Flow elementleri için animasyon - optimize edilmiş
  const flowElements = document.querySelectorAll(".flow")
  if (flowElements.length) {
    flowElements.forEach((flow) => {
      const flowContent = flow.querySelector(".flowcontent")
      const caption = flow.querySelector(".caption")
      const image = flow.querySelector(".imageholder") || flow.querySelector("video")

      if (!flowContent && !caption && !image) return

      gsap.set(flow, { opacity: 0 })

      gsap
        .timeline({
          scrollTrigger: {
            trigger: flow,
            start: "top center",
            end: "+=400",
            scrub: 0.5, // Daha düşük scrub değeri
            markers: false,
            toggleActions: "play none none reverse",
          },
        })
        .to(flow, {
          opacity: 1,
          duration: 0.3,
        })
        .from(
          image,
          {
            scale: 1.05, // Daha küçük scale değeri
            duration: 0.3,
          },
          0,
        )
        .from(
          caption,
          {
            y: 20, // Daha küçük y değeri
            opacity: 0,
            duration: 0.3,
          },
          0.2,
        )
    })
  }

  // Progress bar'ı başlat
  initProgressBar()

}

// Progress bar kontrolü için optimize edilmiş fonksiyon
const initProgressBar = () => {
  // DOM elementlerini doğrudan seçelim (önbellek yerine)
  const sections = [
    document.getElementById("birinci-adim"),
    document.getElementById("ikinci-adim"),
    document.getElementById("ucuncu-adim"),
  ]

  const progressBars = [
    document.getElementById("progress-1"),
    document.getElementById("progress-2"),
    document.getElementById("progress-3"),
    document.getElementById("progress-4"),
  ]

  const progressContainer = document.getElementById("progress-container")

  if (!sections.every(Boolean) || !progressBars.every(Boolean) || !progressContainer) {
    console.warn("Progress bar elementleri bulunamadı")
    return
  }

  // Progress container'ı başlangıçta gizle
  progressContainer.style.transform = "translateY(100%)"

  // Progress'i güncelle - optimize edilmiş
  const updateProgress = () => {
    const windowHeight = window.innerHeight
    const scrollPosition = window.scrollY
    const firstSectionTop = sections[0].offsetTop - windowHeight * 0.5
    const lastSection = sections[3]
    const lastSectionBottom = lastSection.offsetTop + lastSection.offsetHeight

    // İlk section'a gelmeden veya son section'ı geçince progress bar'ı gizle
    if (scrollPosition < firstSectionTop || scrollPosition > lastSectionBottom - windowHeight * 0.5) {
      progressContainer.style.transform = "translateY(100%)"
      return
    }

    // Progress bar'ı göster
    progressContainer.style.transform = "translateY(0)"

    // Her section için progress hesapla
    sections.forEach((section, index) => {
      const rect = section.getBoundingClientRect()
      const sectionTop = rect.top + scrollPosition
      const nextSection = sections[index + 1]

      // Mevcut section için progress hesapla
      if (scrollPosition >= sectionTop - windowHeight * 0.5) {
        // Önceki barları doldur
        for (let i = 0; i < index; i++) {
          progressBars[i].style.width = "100%"
        }

        // Eğer bir sonraki section varsa, onun pozisyonuna göre progress hesapla
        if (nextSection) {
          const nextSectionTop = nextSection.offsetTop
          const progressEnd = nextSectionTop - windowHeight * 0.5
          const progressStart = sectionTop - windowHeight * 0.5
          const progress = Math.min(
            100,
            Math.max(0, ((scrollPosition - progressStart) / (progressEnd - progressStart)) * 100),
          )
          progressBars[index].style.width = `${progress}%`
        } else {
          // Son section için progress
          const progressEnd = lastSectionBottom - windowHeight * 0.5
          const progressStart = sectionTop - windowHeight * 0.5
          const progress = Math.min(
            100,
            Math.max(0, ((scrollPosition - progressStart) / (progressEnd - progressStart)) * 100),
          )
          progressBars[index].style.width = `${progress}%`
        }
      } else {
        progressBars[index].style.width = "0%"
      }
    })
  }

  // Scroll event listener - throttle ile optimize edilmiş
  let ticking = false
  window.addEventListener("scroll", () => {
    if (!ticking) {
      requestAnimationFrame(() => {
        updateProgress()
        ticking = false
      })
      ticking = true
    }
  })

  // İlk yüklemede progress'i güncelle
  updateProgress()

  // Progress bar'a tıklama işlevselliği ekle
  progressBars.forEach((bar, index) => {
    if (!bar || !bar.parentElement || !bar.parentElement.parentElement) return

    bar.parentElement.parentElement.addEventListener("click", () => {
      const targetSection = sections[index]
      if (targetSection) {
        targetSection.scrollIntoView({ behavior: "smooth" })
      }
    })
  })
}

// Resize için debounce fonksiyonu - optimize edilmiş
const debounce = (func, wait) => {
  let timeout
  return (...args) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => func(...args), wait)
  }
}

// Header scroll efekti - optimize edilmiş
const handleHeaderScroll = () => {
  if (!DOM.header) return

  const currentScroll = window.pageYOffset || document.documentElement.scrollTop

  // Header için scroll kontrolü
  if (currentScroll > lastScrollTop) {
    // Aşağı scroll
    DOM.header.style.transform = "translateY(-100%)"
    if (DOM.hiddenElement) {
      DOM.hiddenElement.style.transform = "translateY(-100%)"
      DOM.hiddenElement.style.visibility = "hidden"
      DOM.hiddenElement.style.height = "0px"
    }
  } else {
    // Yukarı scroll
    DOM.header.style.transform = "translateY(0)"

    // Hidden element sadece sayfa en üstteyse görünür olsun
    if (DOM.hiddenElement) {
      if (currentScroll <= 0) {
        DOM.hiddenElement.style.transform = "translateY(0)"
        DOM.hiddenElement.style.visibility = "visible"
        DOM.hiddenElement.style.height = "auto"
      } else {
        DOM.hiddenElement.style.transform = "translateY(-100%)"
        DOM.hiddenElement.style.visibility = "hidden"
      }
    }
  }

  // Header'ın arkaplan efekti
  if (currentScroll > 0) {
    DOM.header.classList.add("header-scroll")
  } else {
    DOM.header.classList.remove("header-scroll")
  }

  lastScrollTop = currentScroll <= 0 ? 0 : currentScroll
}

// Scroll event listener - throttle ile optimize edilmiş
const setupScrollListeners = () => {
  window.addEventListener("scroll", () => {
    if (!ticking) {
      requestAnimationFrame(() => {
        handleHeaderScroll()
        ticking = false
      })
      ticking = true
    }
  })
}

// Mobil menü kontrolü - optimize edilmiş
const setupMobileMenu = () => {
  if (!DOM.mobileMenuButton || !DOM.closeMobileMenuButton || !DOM.mobileMenu || !DOM.mobileMenuOverlay) return

  const openMobileMenu = () => {
    DOM.mobileMenu.classList.add("mobile-menu-open")
    DOM.mobileMenuOverlay.classList.add("active")
    document.body.style.overflow = "hidden"
  }

  const closeMobileMenu = () => {
    DOM.mobileMenu.classList.remove("mobile-menu-open")
    DOM.mobileMenuOverlay.classList.remove("active")
    document.body.style.overflow = ""
  }

  DOM.mobileMenuButton.addEventListener("click", openMobileMenu)
  DOM.closeMobileMenuButton.addEventListener("click", closeMobileMenu)
  DOM.mobileMenuOverlay.addEventListener("click", closeMobileMenu)

  // Mobil menüde dropdown'ları otomatik kapat
  document.querySelectorAll(".mobile-nav-link").forEach((link) => {
    link.addEventListener("click", () => {
      const nextElement = link.nextElementSibling
      if (!nextElement?.classList.contains("mobile-dropdown-content")) {
        DOM.mobileMenu.classList.remove("mobile-menu-open")
        document.body.style.overflow = ""
      }
    })
  })
}

// ScrollMagic controller'ı vanilla JS ile oluşturalım
const setupScrollMagic = () => {
  controller = new ScrollMagic.Controller()

  // Flow animasyonları
  document.querySelectorAll(".flow").forEach((flow) => {
    flow.classList.add("out")

    new ScrollMagic.Scene({
      triggerElement: flow,
      triggerHook: 0,
      duration: 600,
    })
      .on("enter", (ev) => {
        const element = ev.target.triggerElement()
        if (element) element.classList.remove("out")
      })
      .on("leave", (ev) => {
        const element = ev.target.triggerElement()
        if (element) element.classList.add("out")
      })
      .addTo(controller)
  })

  // Fade animasyonları
  document.querySelectorAll(".fade").forEach((fade) => {
    fade.classList.add("out")

    new ScrollMagic.Scene({
      triggerElement: fade,
      triggerHook: 0.65,
    })
      .on("enter", (ev) => {
        const element = ev.target.triggerElement()
        if (element) element.classList.remove("out")
      })
      .on("leave", (ev) => {
        const element = ev.target.triggerElement()
        if (element) element.classList.add("out")
      })
      .addTo(controller)
  })

  // Container sınıfı
  document.querySelectorAll(".container").forEach((container) => {
    container.classList.remove("out")
  })
}

// Kitap Animasyonları - optimize edilmiş
const initBookAnimations = () => {
  const books = document.querySelectorAll(".book")
  const bookBacks = document.querySelectorAll(".book-back")

  if (!books.length && !bookBacks.length) return

  books.forEach((book) => {
    const bookWrap = book.closest(".book-wrap")
    if (!bookWrap) return

    // Mouse Enter animasyonu
    book.addEventListener("mouseenter", () => {
      gsap.to(bookWrap, {
        rotationY: 20,
        duration: 0.4,
        ease: "power2.out",
      })
    })

    // Mouse Leave animasyonu
    book.addEventListener("mouseleave", () => {
      if (!bookWrap.style.transform || !bookWrap.style.transform.includes("rotateY(180deg)")) {
        gsap.to(bookWrap, {
          rotationY: 0,
          duration: 0.4,
          ease: "power2.out",
        })
      }
    })

    // Click animasyonu - Arka kapak için düzeltme
    book.addEventListener("click", () => {
      // Arka kapağı düzgün göstermek için
      gsap.set(bookWrap.querySelector(".book-back"), {
        rotationY: 0,
        rotationX: 0,
        rotationZ: 0,
      })

      gsap.to(bookWrap, {
        rotationY: 180,
        duration: 0.6,
        ease: "power2.inOut",
      })
    })
  })

  // Kitap arkası click animasyonu
  bookBacks.forEach((bookBack) => {
    bookBack.addEventListener("click", () => {
      const bookWrap = bookBack.closest(".book-wrap")
      if (!bookWrap) return

      gsap.to(bookWrap, {
        rotationY: 0,
        duration: 0.6,
        ease: "power2.inOut",
      })
    })
  })
}

// CSS animasyonları için style ekle
const addStyles = () => {
  const style = document.createElement("style")
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
    
    .mobile-menu-open {
      transform: translateX(0) !important;
    }
    
    .mobile-menu-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    
    .book-wrap {
      transform-style: preserve-3d;
      perspective: 1000px;
      position: relative;
      width: 240px;
      height: 400px;
      margin: 0 20px;
      transition: transform 0.5s ease;
    }
    
    .book-back {
      transform: rotateY(180deg);
      background-color: #235486;
      padding: 20px;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      justify-content: center;
      backface-visibility: hidden;
    }
    
    .book {
      backface-visibility: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
  `
  document.head.appendChild(style)
}

// Sayfa yüklendiğinde
document.addEventListener("DOMContentLoaded", () => {
  // DOM elementlerini önbelleğe al
  cacheDOMElements()

  // Stilleri ekle
  addStyles()

  // Saati başlat
  updateClock()

  // Overflow'u gizle
  document.body.style.overflow = "hidden"

  // Loading animasyonunu başlat
  loadingAnimation()

  // Start butonuna event listener ekle
  if (DOM.startButton) {
    DOM.startButton.addEventListener("click", completeLoading)
  }

  // Scroll listener'ları kur
  setupScrollListeners()

  // Mobil menüyü kur
  setupMobileMenu()

  // ScrollMagic'i kur
  setupScrollMagic()

  // Progress bar'ı başlat - DOM önbelleğinden bağımsız olarak çağıralım
  initProgressBar()

  // Resize event listener'ı
  window.addEventListener(
    "resize",
    debounce(() => {
      ScrollTrigger.refresh()
      // Resize olduğunda progress bar'ı güncelle
      initProgressBar()
    }, 300),
  )
})

// Sayfanın yüklenmesi tamamlandığında kitap animasyonlarını başlat
window.addEventListener("load", () => {
  initBookAnimations()
})

