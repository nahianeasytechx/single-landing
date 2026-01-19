
  function loadGoogleTranslate() {
    const gtScript = document.createElement('script');
    gtScript.src = "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
    document.body.appendChild(gtScript);
  }

  function googleTranslateElementInit() {
    new google.translate.TranslateElement({
      pageLanguage: 'en',
      includedLanguages: 'en,bn',
      autoDisplay: false
    }, 'google_translate_element');
  }

  function changeLanguage(langText) {
    const iframe = document.querySelector('iframe.goog-te-menu-frame');
    if (!iframe) {
      console.warn("Google Translate iframe not ready.");
      return;
    }

    const innerDoc = iframe.contentDocument || iframe.contentWindow.document;
    const langItems = innerDoc.querySelectorAll('.goog-te-menu2-item span.text');

    langItems.forEach(item => {
      if (item.innerText.toLowerCase().includes(langText.toLowerCase())) {
        item.click();
      }
    });
  }

  function waitForIframeAndTranslate(langText) {
    const checkInterval = setInterval(() => {
      const iframe = document.querySelector('iframe.goog-te-menu-frame');
      if (iframe) {
        clearInterval(checkInterval);
        changeLanguage(langText);
      }
    }, 500);
  }

  document.addEventListener("DOMContentLoaded", function () {
    loadGoogleTranslate();

    const langButtons = document.querySelectorAll(".lang-btn");

    langButtons.forEach(btn => {
      btn.addEventListener("click", function () {
        const lang = btn.getAttribute("data-lang");

        // Remove 'active' class from all buttons
        langButtons.forEach(b => b.classList.remove("active"));

        // Add 'active' to the clicked one
        btn.classList.add("active");

        // Trigger translation
        if (lang === "en") {
          waitForIframeAndTranslate("English");
        } else if (lang === "bn") {
          waitForIframeAndTranslate("Bengali");
        }
      });
    });
  });
